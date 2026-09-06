<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderEvent;
use App\Models\SellerAccount;
use App\Services\MarketplaceOrderWorkflowService;
use Illuminate\Http\Request;

class SellerOrderController extends Controller
{
    public function __construct(
        private MarketplaceOrderWorkflowService $workflow
    ) {}

    private function seller(Request $request): SellerAccount
    {
        abort_unless($request->session()->get('is_seller'), 403);

        return SellerAccount::findOrFail(
            $request->session()->get('seller_account_id')
        );
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
            ->get();

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
            'You started preparing order ' . $order->order_number . '. Pack the items and mark the package ready when complete.',
            'seller'
        );

        return back()->with('success', 'Order is now being prepared.');
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

        return back()->with('success', 'Courier request created. Order is ready for pickup.');
    }

    public function waybill(Request $request, MarketplaceOrder $order)
    {
        $order = $this->ownedOrder($request, $order);

        return view('seller.orders.waybill', compact('order'));
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
