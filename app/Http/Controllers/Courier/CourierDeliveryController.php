<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Orders\MarketplaceOrderWorkflowService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CourierDeliveryController extends Controller
{
    private const ACTIVE_DIRECT_STATUSES = [
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
        'arrived_buyer',
    ];

    private const ACTIVE_PICKUP_STATUSES = [
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
    ];

    public function __construct(
        private readonly MarketplaceOrderWorkflowService $workflow
    ) {
    }

    private function guard(
        Request $request
    ): void {
        abort_unless(
            $request
                ->session()
                ->get('is_courier'),
            403,
            'Rider session required.'
        );
    }

    private function courierEmail(
        Request $request
    ): string {
        return strtolower(
            trim(
                (string) $request
                    ->session()
                    ->get(
                        'courier_email',
                        ''
                    )
            )
        );
    }

    private function assignedOrder(
        Request $request,
        MarketplaceOrder $order
    ): MarketplaceOrder {
        $this->guard(
            $request
        );

        abort_unless(
            strtolower(
                trim(
                    (string) $order
                        ->courier_email
                )
            )
            ===
            $this->courierEmail(
                $request
            ),
            403
        );

        return $order;
    }

    /*
    |--------------------------------------------------------------------------
    | Active pickup/direct jobs
    |--------------------------------------------------------------------------
    |
    | Logistics-managed pickup jobs only remain active for the pickup Rider
    | while the parcel is awaiting intake at the origin hub.
    |
    | After origin-hub intake, courier_email remains only as a historical
    | pickup Rider snapshot and must not keep the old Rider operationally busy.
    |
    */

    private function activeCourierOrders(
        Request $request
    ): Builder {
        return MarketplaceOrder::query()
            ->whereRaw(
                'LOWER(courier_email) = ?',
                [
                    $this->courierEmail(
                        $request
                    ),
                ]
            )
            ->where(
                function (
                    Builder $query
                ): void {
                    /*
                    |--------------------------------------------------------------------------
                    | Legacy/direct deliveries
                    |--------------------------------------------------------------------------
                    */

                    $query
                        ->where(
                            function (
                                Builder $direct
                            ): void {
                                $direct
                                    ->whereDoesntHave(
                                        'logisticsParcel'
                                    )
                                    ->whereIn(
                                        'status',
                                        self::ACTIVE_DIRECT_STATUSES
                                    );
                            }
                        )

                        /*
                        |--------------------------------------------------------------------------
                        | Logistics Seller-pickup leg
                        |--------------------------------------------------------------------------
                        */

                        ->orWhere(
                            function (
                                Builder $pickup
                            ): void {
                                $pickup
                                    ->whereIn(
                                        'status',
                                        self::ACTIVE_PICKUP_STATUSES
                                    )
                                    ->whereHas(
                                        'logisticsParcel',
                                        fn (
                                            Builder $parcel
                                        ) =>
                                            $parcel->where(
                                                'status',
                                                'awaiting_intake'
                                            )
                                    );
                            }
                        );
                }
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Logistics final-mile guard
    |--------------------------------------------------------------------------
    */

    private function assertNotLogisticsFinalMile(
        MarketplaceOrder $order
    ): void {
        $hasLogisticsParcel =
            LogisticsParcel::query()
                ->where(
                    'marketplace_order_id',
                    $order->id
                )
                ->exists();

        if ($hasLogisticsParcel) {
            throw ValidationException::withMessages([
                'order' =>
                    'Final delivery for Logistics-managed orders must be completed by the assigned last-mile Rider.',
            ]);
        }
    }

    public function requests(
        Request $request
    ): View {
        $this->guard(
            $request
        );

        $orders =
            MarketplaceOrder::query()
                ->with([
                    'seller',
                    'logisticsParcel',
                ])
                ->whereRaw(
                    'LOWER(courier_email) = ?',
                    [
                        $this->courierEmail(
                            $request
                        ),
                    ]
                )
                ->where(
                    'status',
                    'courier_accepted'
                )
                ->latest(
                    'accepted_at'
                )
                ->get();

        $activeOrder =
            $this->activeCourierOrders(
                $request
            )
                ->latest(
                    'updated_at'
                )
                ->first();

        return view(
            'courier.requests',
            compact(
                'orders',
                'activeOrder'
            )
        );
    }

    public function accept(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {
        $this->guard(
            $request
        );

        throw ValidationException::withMessages([
            'order' =>
                'Delivery jobs are assigned by SARI Logistics. Riders can no longer self-accept unassigned orders.',
        ]);
    }

    public function proceedToPickup(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {
        $order =
            $this->assignedOrder(
                $request,
                $order
            );

        $this->workflow->transition(
            $order,
            'courier_accepted',
            'heading_pickup',
            'Rider Is Heading to Pickup',
            ($order->courier_name ?: 'Rider')
                . ' is now heading to the seller pickup location for order '
                . $order->order_number
                . '.',
            'both',
            [
                'pickup_started_at' =>
                    now(),
            ]
        );

        return back()->with(
            'success',
            'Pickup trip started.'
        );
    }

    public function arrivedAtPickup(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {
        $order =
            $this->assignedOrder(
                $request,
                $order
            );

        $this->workflow->transition(
            $order,
            'heading_pickup',
            'arrived_pickup',
            'Rider Arrived at Seller',
            ($order->courier_name ?: 'Rider')
                . ' arrived at the seller pickup point for order '
                . $order->order_number
                . '.',
            'both',
            [
                'arrived_pickup_at' =>
                    now(),
            ]
        );

        return back()->with(
            'success',
            'Arrival at seller confirmed.'
        );
    }

    public function confirmPickup(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {
        $order =
            $this->assignedOrder(
                $request,
                $order
            );

        $this->workflow->transition(
            $order,
            'arrived_pickup',
            'in_transit',
            'Order Picked Up',
            'Order '
                . $order->order_number
                . ' was picked up by '
                . (
                    $order->courier_name
                    ?: 'the Rider'
                )
                . ' and is now moving through SARI Logistics for hub intake and final delivery.',
            'both',
            [
                'picked_up_at' =>
                    now(),
            ]
        );

        return back()->with(
            'success',
            'Pickup confirmed. Delivery is now in transit.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Direct-delivery Buyer arrival
    |--------------------------------------------------------------------------
    |
    | Logistics parcels are forbidden here.
    | Their final delivery belongs to CourierLastMileController.
    |
    */

    public function arrivedAtBuyer(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {
        $order =
            $this->assignedOrder(
                $request,
                $order
            );

        $this->assertNotLogisticsFinalMile(
            $order
        );

        $this->workflow->transition(
            $order,
            'in_transit',
            'arrived_buyer',
            'Rider Reached Buyer',
            ($order->courier_name ?: 'Rider')
                . ' reached the buyer location for order '
                . $order->order_number
                . '.',
            'both',
            [
                'arrived_buyer_at' =>
                    now(),
            ]
        );

        return back()->with(
            'success',
            'Buyer arrival confirmed.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Direct-delivery completion
    |--------------------------------------------------------------------------
    */

    public function complete(
        Request $request,
        MarketplaceOrder $order
    ): RedirectResponse {
        $order =
            $this->assignedOrder(
                $request,
                $order
            );

        $this->assertNotLogisticsFinalMile(
            $order
        );

        $this->workflow->transition(
            $order,
            'arrived_buyer',
            'delivered',
            'Order Delivered Successfully',
            'Order '
                . $order->order_number
                . ' was successfully delivered to '
                . $order->buyer_name
                . '.',
            'both',
            [
                'delivered_at' =>
                    now(),

                'payment_status' =>
                    $order->payment_method
                    === 'COD'
                        ? 'paid'
                        : $order
                            ->payment_status,
            ]
        );

        return back()->with(
            'success',
            'Delivery completed successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Live assignment state
    |--------------------------------------------------------------------------
    */

    public function liveState(
        Request $request
    ): JsonResponse {
        $this->guard(
            $request
        );

        $activeQuery =
            $this->activeCourierOrders(
                $request
            );

        $latestAssignment =
            (clone $activeQuery)
                ->latest(
                    'updated_at'
                )
                ->first();

        /*
        |--------------------------------------------------------------------------
        | requests.blade.php compares this with the courier_accepted queue only.
        |--------------------------------------------------------------------------
        */

        $assignedCount =
            MarketplaceOrder::query()
                ->whereRaw(
                    'LOWER(courier_email) = ?',
                    [
                        $this->courierEmail(
                            $request
                        ),
                    ]
                )
                ->where(
                    'status',
                    'courier_accepted'
                )
                ->count();

        return response()->json([
            'assigned_count' =>
                $assignedCount,

            'latest_assignment_id' =>
                $latestAssignment?->id,

            'revision' =>
                $latestAssignment
                    ?->updated_at
                    ?->toIso8601String(),
        ]);
    }
}