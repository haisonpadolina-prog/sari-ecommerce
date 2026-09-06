<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderEvent;
use App\Models\SellerProduct;
use App\Services\BuyerIdentityService;
use App\Services\ProductVariantInventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BuyerOrderController extends Controller
{
    public function __construct(
        private readonly BuyerIdentityService $identity,
        private readonly ProductVariantInventoryService $inventory,
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_buyer')) {
            return redirect()->route('login');
        }

        $orders = $this->identity
            ->apply(MarketplaceOrder::query(), $request)
            ->with(['seller', 'reviews'])
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
            'reviews',
        ]);

        return view('buyer.order-details', compact('order'));
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