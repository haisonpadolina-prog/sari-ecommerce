<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use App\Models\LogisticsParcel;
use App\Services\MarketplaceOrderWorkflowService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CourierDeliveryController extends Controller
{
    public function __construct(
        private readonly MarketplaceOrderWorkflowService $workflow
    ) {
    }

    private function guard(Request $request): void
    {
        abort_unless(
            $request->session()->get('is_courier'),
            403,
            'Rider session required.'
        );
    }

    private function courierEmail(Request $request): string
    {
        return strtolower(
            (string) $request->session()->get('courier_email', '')
        );
    }

    private function assignedOrder(
        Request $request,
        MarketplaceOrder $order
    ): MarketplaceOrder {
        $this->guard($request);

        abort_unless(
            strtolower((string) $order->courier_email) === $this->courierEmail($request),
            403
        );

        return $order;
    }

    public function requests(Request $request): View
    {
        $this->guard($request);

        $orders = MarketplaceOrder::query()
            ->with('seller')
            ->whereRaw('LOWER(courier_email) = ?', [$this->courierEmail($request)])
            ->where('status', 'courier_accepted')
            ->latest('accepted_at')
            ->get();

        $activeOrder = MarketplaceOrder::query()
            ->whereRaw('LOWER(courier_email) = ?', [$this->courierEmail($request)])
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

    public function accept(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {
        $this->guard($request);

        throw ValidationException::withMessages([
            'order' => 'Delivery jobs are assigned by SARI Logistics. Riders can no longer self-accept unassigned orders.',
        ]);
    }

    public function proceedToPickup(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {
        $order = $this->assignedOrder($request, $order);

        $this->workflow->transition(
            $order,
            'courier_accepted',
            'heading_pickup',
            'Rider Is Heading to Pickup',
            ($order->courier_name ?: 'Rider') .
                ' is now heading to the seller pickup location for order ' .
                $order->order_number . '.',
            'both',
            ['pickup_started_at' => now()]
        );

        return back()->with('success', 'Pickup trip started.');
    }

    public function arrivedAtPickup(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {
        $order = $this->assignedOrder($request, $order);

        $this->workflow->transition(
            $order,
            'heading_pickup',
            'arrived_pickup',
            'Rider Arrived at Seller',
            ($order->courier_name ?: 'Rider') .
                ' arrived at the seller pickup point for order ' .
                $order->order_number . '.',
            'both',
            ['arrived_pickup_at' => now()]
        );

        return back()->with('success', 'Arrival at seller confirmed.');
    }

    public function confirmPickup(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {
        $order = $this->assignedOrder($request, $order);

        $this->workflow->transition(
            $order,
            'arrived_pickup',
            'in_transit',
            'Order Picked Up',
            'Order ' . $order->order_number .
                ' was picked up by ' .
                ($order->courier_name ?: 'the Rider') .
                ' and is now moving through SARI Logistics for hub intake and final delivery.',
            'both',
            ['picked_up_at' => now()]
        );

        return back()->with('success', 'Pickup confirmed. Delivery is now in transit.');
    }

    public function arrivedAtBuyer(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {
        $order = $this->assignedOrder($request, $order);

        $parcel = LogisticsParcel::query()
            ->where('marketplace_order_id', $order->id)
            ->first();

        if ($parcel && $parcel->status !== 'sorted') {
            throw ValidationException::withMessages([
                'parcel' => 'Logistics must receive and sort this parcel before final delivery to the Buyer.',
            ]);
        }

        $this->workflow->transition(
            $order,
            'in_transit',
            'arrived_buyer',
            'Rider Reached Buyer',
            ($order->courier_name ?: 'Rider') .
                ' reached the buyer location for order ' .
                $order->order_number . '.',
            'both',
            ['arrived_buyer_at' => now()]
        );

        return back()->with('success', 'Buyer arrival confirmed.');
    }

    public function complete(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {
        $order = $this->assignedOrder($request, $order);

        $this->workflow->transition(
            $order,
            'arrived_buyer',
            'delivered',
            'Order Delivered Successfully',
            'Order ' . $order->order_number .
                ' was successfully delivered to ' .
                $order->buyer_name . '.',
            'both',
            [
                'delivered_at' => now(),
                'payment_status' => $order->payment_method === 'COD'
                    ? 'paid'
                    : $order->payment_status,
            ]
        );

        return back()->with('success', 'Delivery completed successfully.');
    }

    public function liveState(Request $request): JsonResponse
    {
        $this->guard($request);

        $latestAssignment = MarketplaceOrder::query()
            ->whereRaw('LOWER(courier_email) = ?', [$this->courierEmail($request)])
            ->whereIn('status', [
                'courier_accepted',
                'heading_pickup',
                'arrived_pickup',
                'in_transit',
                'arrived_buyer',
            ])
            ->latest('updated_at')
            ->first();

        return response()->json([
            'assigned_count' => MarketplaceOrder::query()
                ->whereRaw('LOWER(courier_email) = ?', [$this->courierEmail($request)])
                ->whereIn('status', [
                    'courier_accepted',
                    'heading_pickup',
                    'arrived_pickup',
                    'in_transit',
                    'arrived_buyer',
                ])
                ->count(),
            'latest_assignment_id' => $latestAssignment?->id,
            'revision' => $latestAssignment?->updated_at?->toIso8601String(),
        ]);
    }
}
