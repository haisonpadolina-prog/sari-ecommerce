<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use App\Services\MarketplaceOrderWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CourierDeliveryController extends Controller
{
    public function __construct(
        private MarketplaceOrderWorkflowService $workflow
    ) {}

    private function guard(Request $request): void
    {
        abort_unless(
            $request->session()->get('is_courier'),
            403,
            'Courier session required.'
        );
    }

    private function courierEmail(Request $request): string
    {
        return (string) $request->session()->get('courier_email', 'courier@gmail.com');
    }

    private function courierName(Request $request): string
    {
        return (string) $request->session()->get('courier_name', 'SARI Courier');
    }

    private function assignedOrder(Request $request, MarketplaceOrder $order): MarketplaceOrder
    {
        $this->guard($request);

        abort_unless(
            $order->courier_email === $this->courierEmail($request),
            403
        );

        return $order;
    }

    public function requests(Request $request)
    {
        $this->guard($request);

        $orders = MarketplaceOrder::query()
            ->with('seller')
            ->where('status', 'ready_for_pickup')
            ->whereNull('courier_email')
            ->oldest('ready_at')
            ->get();

        $activeOrder = MarketplaceOrder::query()
            ->where('courier_email', $this->courierEmail($request))
            ->whereIn('status', [
                'courier_accepted',
                'heading_pickup',
                'arrived_pickup',
                'in_transit',
                'arrived_buyer',
            ])
            ->latest('updated_at')
            ->first();

        return view('courier.requests', compact('orders', 'activeOrder'));
    }

    public function accept(Request $request, MarketplaceOrder $order)
    {
        $this->guard($request);

        $courierEmail = $this->courierEmail($request);
        $courierName = $this->courierName($request);

        DB::transaction(function () use ($order, $courierEmail, $courierName) {
            $activeExists = MarketplaceOrder::query()
                ->where('courier_email', $courierEmail)
                ->whereIn('status', [
                    'courier_accepted',
                    'heading_pickup',
                    'arrived_pickup',
                    'in_transit',
                    'arrived_buyer',
                ])
                ->lockForUpdate()
                ->exists();

            if ($activeExists) {
                throw ValidationException::withMessages([
                    'courier' => 'Finish your current delivery before accepting another request.',
                ]);
            }

            $locked = MarketplaceOrder::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status !== 'ready_for_pickup' || $locked->courier_email) {
                throw ValidationException::withMessages([
                    'order' => 'This delivery request is no longer available.',
                ]);
            }

            $locked->forceFill([
                'status' => 'courier_accepted',
                'courier_name' => $courierName,
                'courier_email' => $courierEmail,
                'accepted_at' => now(),
            ])->save();

            $this->workflow->record(
                $locked,
                'seller',
                'courier_accepted',
                'Courier Accepted Your Order',
                $courierName . ' accepted delivery request ' . $locked->order_number . '.'
            );
        });

        return redirect()
            ->route('courier.dashboard')
            ->with('success', 'Delivery request accepted.');
    }

    public function proceedToPickup(Request $request, MarketplaceOrder $order)
    {
        $order = $this->assignedOrder($request, $order);

        $this->workflow->transition(
            $order,
            'courier_accepted',
            'heading_pickup',
            'Courier Is Heading to Pickup',
            ($order->courier_name ?: 'Courier') . ' is now heading to your pickup location for order ' . $order->order_number . '.',
            'seller',
            ['pickup_started_at' => now()]
        );

        return back()->with('success', 'Pickup trip started.');
    }

    public function arrivedAtPickup(Request $request, MarketplaceOrder $order)
    {
        $order = $this->assignedOrder($request, $order);

        $this->workflow->transition(
            $order,
            'heading_pickup',
            'arrived_pickup',
            'Courier Arrived at Seller',
            ($order->courier_name ?: 'Courier') . ' arrived at the seller pickup point for order ' . $order->order_number . '.',
            'seller',
            ['arrived_pickup_at' => now()]
        );

        return back()->with('success', 'Arrival at seller confirmed.');
    }

    public function confirmPickup(Request $request, MarketplaceOrder $order)
    {
        $order = $this->assignedOrder($request, $order);

        $this->workflow->transition(
            $order,
            'arrived_pickup',
            'in_transit',
            'Order Picked Up',
            'Order ' . $order->order_number . ' was picked up by ' . ($order->courier_name ?: 'the courier') . ' and is now in transit.',
            'seller',
            ['picked_up_at' => now()]
        );

        return back()->with('success', 'Item pickup confirmed. Delivery is now in transit.');
    }

    public function arrivedAtBuyer(Request $request, MarketplaceOrder $order)
    {
        $order = $this->assignedOrder($request, $order);

        $this->workflow->transition(
            $order,
            'in_transit',
            'arrived_buyer',
            'Courier Reached Buyer',
            ($order->courier_name ?: 'Courier') . ' reached the buyer location for order ' . $order->order_number . '.',
            'seller',
            ['arrived_buyer_at' => now()]
        );

        return back()->with('success', 'Buyer arrival confirmed.');
    }

    public function complete(Request $request, MarketplaceOrder $order)
    {
        $order = $this->assignedOrder($request, $order);

        $this->workflow->transition(
            $order,
            'arrived_buyer',
            'delivered',
            'Order Delivered Successfully',
            'Order ' . $order->order_number . ' was successfully delivered to ' . $order->buyer_name . '.',
            'seller',
            [
                'delivered_at' => now(),
                'payment_status' => $order->payment_method === 'COD' ? 'paid' : $order->payment_status,
            ]
        );

        return back()->with('success', 'Delivery completed successfully.');
    }

    public function liveState(Request $request)
    {
        $this->guard($request);

        $latestAvailable = MarketplaceOrder::query()
            ->where('status', 'ready_for_pickup')
            ->whereNull('courier_email')
            ->latest('updated_at')
            ->first();

        return response()->json([
            'available_count' => MarketplaceOrder::query()
                ->where('status', 'ready_for_pickup')
                ->whereNull('courier_email')
                ->count(),
            'latest_request_id' => $latestAvailable?->id,
            'revision' => $latestAvailable?->updated_at?->toIso8601String(),
        ]);
    }
}
