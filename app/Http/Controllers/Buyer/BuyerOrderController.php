<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;

use App\Models\Orders\MarketplaceOrder;
use App\Models\Orders\MarketplaceOrderEvent;
use App\Models\Accounts\CourierAccount;
use App\Models\Catalog\SellerProduct;
use App\Models\Catalog\SellerProductVariant;
use App\Services\Buyer\BuyerIdentityService;
use App\Services\Delivery\DeliveryRouteService;
use App\Services\Products\ProductVariantInventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BuyerOrderController extends Controller
{
    public function __construct(
        private readonly BuyerIdentityService $identity,
        private readonly ProductVariantInventoryService $inventory,
        private readonly DeliveryRouteService $deliveryRoute,
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_buyer')) {
            return redirect()->route('login');
        }

        $orders = $this->identity
            ->apply(MarketplaceOrder::query(), $request)
            ->with(['seller', 'reviews', 'returnRequest', 'logisticsParcel', 'events'])
            ->latest('created_at')
            ->get();

        $stats = [
            'all' => $orders->count(),
            'active' => $orders->whereNotIn('status', ['delivered', 'cancelled'])->count(),
            'in_transit' => $orders->whereIn('status', [
                'courier_accepted',
                'heading_pickup',
                'arrived_pickup',
                'in_transit',
                'arrived_buyer'
            ])->count(),
            'delivered' => $orders->where('status', 'delivered')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
        ];

        return view('buyer.orders', compact('orders', 'stats'));
    }


    /**
     * Display single buyer order details
     */
    public function show(Request $request, MarketplaceOrder $order): View
    {
        $this->guardOwned($request, $order);

        $order->load([
            'seller',
            'buyer',
            'reviews',
            'returnRequest',
            'logisticsParcel',
            'events',
        ]);

        $trackingRoute = $this->deliveryRoute->buyerPayload($order);

        // Order snapshots store IDs, not image URLs. Resolve the purchased variant
        // first, then the product photo, without requiring an active listing.
        $primaryItem = (array) (collect($order->items ?? [])->first() ?? []);
        $primaryProductId = (int) ($primaryItem['product_id'] ?? 0);
        $primaryVariantId = (int) ($primaryItem['variant_id'] ?? 0);
        $primaryItemImageUrl = null;

        if ($primaryProductId > 0) {
            $product = SellerProduct::query()
                ->whereKey($primaryProductId)
                ->where('seller_account_id', $order->seller_account_id)
                ->first();

            if ($product) {
                $variantPath = $primaryVariantId > 0
                    ? SellerProductVariant::query()
                        ->whereKey($primaryVariantId)
                        ->where('seller_product_id', $product->id)
                        ->value('image_path')
                    : null;

                foreach ([$variantPath, $product->image_path] as $imagePath) {
                    $imagePath = trim((string) $imagePath);

                    if ($imagePath !== '' && Storage::disk('public')->exists($imagePath)) {
                        $primaryItemImageUrl = Storage::disk('public')->url($imagePath);
                        break;
                    }
                }
            }
        }

        // The order is buyer-owned (guardOwned above). Lookup is scoped to the
        // courier email assigned on this order, not another rider's account.
        $courier = null;
        $courierEmail = trim((string) ($order->courier_email ?? ''));

        if ($courierEmail !== '') {
            $courier = CourierAccount::query()
                ->where('email', $courierEmail)
                ->first();
        }

        $courierName = $courier
            ? trim(implode(' ', array_filter([
                $courier->first_name,
                $courier->middle_initial ? trim($courier->middle_initial, ' .') . '.' : null,
                $courier->last_name,
            ])))
            : '';

        $courierPhotoPath = trim((string) ($courier?->profile_image_path ?? ''));
        $courierPhotoUrl = $courierPhotoPath !== '' && Storage::disk('public')->exists($courierPhotoPath)
            ? Storage::disk('public')->url($courierPhotoPath)
            : null;

        $courierDetails = [
            'name' => $courierName !== ''
                ? $courierName
                : (trim((string) ($order->courier_name ?? '')) ?: 'Waiting for rider assignment'),
            'phone' => trim((string) ($courier?->contact_no ?? '')),
            'email' => $courierEmail !== '' ? $courierEmail : trim((string) ($courier?->email ?? '')),
            'photo_url' => $courierPhotoUrl,
            'assigned' => $courierEmail !== '' || trim((string) ($order->courier_name ?? '')) !== '',
        ];

        return view('buyer.order-details', compact(
            'order',
            'trackingRoute',
            'primaryItemImageUrl',
            'courierDetails'
        ));
    }


    public function cancel(Request $request, MarketplaceOrder $order): RedirectResponse
    {
        $this->guardOwned($request, $order);

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($order, $validated) {

            $locked = MarketplaceOrder::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();


            if ($locked->status !== 'new') {
                throw ValidationException::withMessages([
                    'order' => 'This order can no longer be cancelled because the seller already started fulfillment.',
                ]);
            }


            foreach ((array) $locked->items as $item) {

                $productId = (int) ($item['product_id'] ?? 0);

                if ($productId <= 0) {
                    continue;
                }


                $product = SellerProduct::query()->find($productId);


                if (!$product) {
                    continue;
                }


                $this->inventory->restoreForCancelledOrder(
                    $product,
                    !empty($item['variant_id'])
                        ? (int) $item['variant_id']
                        : null,
                    max(1, (int) ($item['qty'] ?? 1))
                );
            }


            $locked->forceFill([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => trim(
                    (string) (
                        $validated['reason']
                        ?? 'Buyer cancelled before preparation.'
                    )
                ),
            ])->save();



            MarketplaceOrderEvent::create([
                'marketplace_order_id' => $locked->id,
                'seller_account_id' => $locked->seller_account_id,
                'audience' => 'both',
                'type' => 'cancelled',
                'title' => 'Order Cancelled',
                'message' => 'The buyer cancelled order ' . $locked->order_number . ' before preparation started.',
                'status' => 'cancelled',
            ]);

        }, 3);


        return back()
            ->with('success', 'Order cancelled and reserved stock was restored.');
    }


    /**
     * Buyer-owned location snapshots and delivery milestones for My Orders.
     * Route stops are real saved coordinates, NOT a rider GPS location or ETA.
     */
    public function trackingState(Request $request, MarketplaceOrder $order): JsonResponse
    {
        $this->guardOwned($request, $order);

        $order->loadMissing(['seller', 'buyer', 'logisticsParcel']);

        $route = $this->deliveryRoute->buyerPayload($order);

        return response()->json([
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'status_label' => $order->statusLabel(),
            'courier_name' => $order->courier_name ?: null,
            'updated_at' => $order->updated_at?->toIso8601String(),
            'route' => $route,
        ])->header('Cache-Control', 'private, no-store, max-age=0');
    }


    public function liveState(Request $request): JsonResponse
    {
        $this->identity->guard($request);


        $query = $this->identity
            ->apply(MarketplaceOrder::query(), $request);


        $revision = (clone $query)->max('updated_at');


        return response()->json([
            'revision' => $revision ? (string) $revision : null,
            'active_count' => (clone $query)
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->count(),
        ]);
    }


    private function guardOwned(Request $request, MarketplaceOrder $order): void
    {
        $this->identity->guard($request);


        $owned = $this->identity->accountId($request)

            ? (int) $order->buyer_account_id === (int) $this->identity->accountId($request)

            : (int) $order->buyer_social_account_id === (int) $this->identity->socialId($request);


        abort_unless($owned, 403);
    }
}