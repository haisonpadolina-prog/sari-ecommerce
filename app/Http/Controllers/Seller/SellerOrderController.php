<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;

use App\Models\Orders\MarketplaceOrder;
use App\Models\Orders\MarketplaceOrderEvent;
use App\Models\Accounts\SellerAccount;
use App\Services\Orders\MarketplaceOrderWorkflowService;
use App\Services\Delivery\DeliveryRouteService;
use App\Support\ParcelQrCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;
use Illuminate\Http\Request;

class SellerOrderController extends Controller
{
    public function __construct(
        private MarketplaceOrderWorkflowService $workflow,
        private DeliveryRouteService $deliveryRoute,
        private ParcelQrCode $parcelQrCode,
    ) {}

    private function seller(Request $request): SellerAccount
    {
        abort_unless($request->session()->get('is_seller'), 403);

        // Seller middleware already resolves this model for page requests.
        // Reuse it so navigation does not execute a duplicate account query.
        $resolved = $request->attributes->get('sellerAccount');

        if ($resolved instanceof SellerAccount) {
            return $resolved;
        }

        $seller = SellerAccount::findOrFail(
            $request->session()->get('seller_account_id')
        );

        $request->attributes->set('sellerAccount', $seller);

        return $seller;
    }

    private function ownedOrder(Request $request, MarketplaceOrder $order): MarketplaceOrder
    {
        $seller = $this->seller($request);

        abort_unless(
            (int) $order->seller_account_id === (int) $seller->id,
            403
        );

        return $order;
    }

    public function index(Request $request)
    {
        $seller = $this->seller($request);

        $orders = MarketplaceOrder::query()
            ->where('seller_account_id', $seller->id)
            ->latest('updated_at')
            ->get([
                'id',
                'order_number',
                'buyer_account_id',
                'buyer_social_account_id',
                'buyer_name',
                'buyer_email',
                'buyer_address',
                'payment_method',
                'items',
                'subtotal',
                'delivery_fee',
                'total',
                'status',
                'courier_name',
                'courier_email',
                'created_at',
                'updated_at',
                'delivered_at',
            ]);

        $stats = [
            'new' => $orders->where('status', 'new')->count(),
            'preparing' => $orders->where('status', 'preparing')->count(),
            'ready' => $orders->where('status', 'ready_for_pickup')->count(),
            'courier' => $orders->whereIn('status', [
                'courier_accepted',
                'heading_pickup',
                'arrived_pickup',
                'in_transit',
                'arrived_buyer',
            ])->count(),
            'delivered' => $orders->where('status', 'delivered')->count(),
        ];

        $latestEvent = MarketplaceOrderEvent::query()
            ->where('seller_account_id', $seller->id)
            ->whereIn('audience', ['seller', 'both'])
            ->latest('id')
            ->first();

        return view('seller.orders', compact(
            'seller',
            'orders',
            'stats',
            'latestEvent'
        ));
    }

    public function prepare(Request $request, MarketplaceOrder $order)
    {
        $order = $this->ownedOrder($request, $order);

        $this->workflow->transition(
            $order,
            'new',
            'preparing',
            'Order Preparation Started',
            'The Seller started preparing order ' . $order->order_number . '.',
            'both'
        );

        return back()->with('success', 'Order is now being prepared.');
    }

    /**
     * Prepare only a seller-owned set of New orders. The action is atomic:
     * if any selected order is ineligible or changes concurrently, none are prepared.
     */
    public function prepareSelected(Request $request)
    {
        $seller = $this->seller($request);
        $validated = $request->validate([
            'order_ids' => ['required', 'array', 'min:1', 'max:100'],
            'order_ids.*' => ['required', 'integer', 'min:1', 'distinct'],
        ]);
        $ids = array_map('intval', $validated['order_ids']);

        DB::transaction(function () use ($seller, $ids) {
            $orders = MarketplaceOrder::query()
                ->where('seller_account_id', $seller->id)
                ->whereIn('id', $ids)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            // Do not expose or mutate another seller's orders, even when IDs were forged.
            abort_unless($orders->count() === count($ids), 403);

            if ($orders->contains(fn (MarketplaceOrder $order) => $order->status !== 'new')) {
                throw ValidationException::withMessages([
                    'order_ids' => 'All selected orders must have New status. Refresh and choose only New orders.',
                ]);
            }

            foreach ($orders as $order) {
                $this->workflow->transition(
                    $order,
                    'new',
                    'preparing',
                    'Order Preparation Started',
                    'The Seller started preparing order ' . $order->order_number . '.',
                    'both'
                );
            }
        }, 3);

        $count = count($ids);
        return back()->with('success', $count . ' selected ' . ($count === 1 ? 'order is' : 'orders are') . ' now being prepared.');
    }

    public function readyForPickup(Request $request, MarketplaceOrder $order)
    {
        $order = $this->ownedOrder($request, $order);

        $this->workflow->transition(
            $order,
            'preparing',
            'ready_for_pickup',
            'Order Ready for Pickup',
            'Order ' . $order->order_number . ' is ready. A delivery request is now available to couriers.',
            'both',
            ['ready_at' => now()]
        );

        /*
         * Build the route immediately when possible so the correct Logistics
         * provider can see the pickup request without waiting for a dashboard
         * refresh. Geocoding/routing failure must never roll back Seller status.
         */
        try {
            $this->deliveryRoute->ensureStops(
                $order->fresh(['seller', 'buyer']) ?? $order
            );
        } catch (Throwable $e) {
            Log::warning(
                'SARI could not pre-route a ready-for-pickup order.',
                [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]
            );
        }

        return back()->with('success', 'Courier request created. Order is ready for pickup.');
    }

    public function waybill(Request $request, MarketplaceOrder $order)
    {
        $order = $this->ownedOrder($request, $order);

        $parcelQrPayload = $this->parcelQrCode->payload($order);

        return view('seller.orders.waybill', compact('order', 'parcelQrPayload'));
    }

    public function liveState(Request $request)
    {
        $seller = $this->seller($request);

        $latestEvent = MarketplaceOrderEvent::query()
            ->where('seller_account_id', $seller->id)
            ->whereIn('audience', ['seller', 'both'])
            ->latest('id')
            ->first();

        $revision = MarketplaceOrder::query()
            ->where('seller_account_id', $seller->id)
            ->max('updated_at');

        return response()->json([
            'latest_event' => $latestEvent ? [
                'id' => $latestEvent->id,
                'type' => $latestEvent->type,
                'title' => $latestEvent->title,
                'message' => $latestEvent->message,
                'status' => $latestEvent->status,
                'created_at' => $latestEvent->created_at?->toIso8601String(),
            ] : null,
            'revision' => $revision ? (string) $revision : null,
        ]);
    }
}
