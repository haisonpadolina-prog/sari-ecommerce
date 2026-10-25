<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Orders\MarketplaceOrderWorkflowService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CourierLastMileController extends Controller
{
    public function __construct(
        private readonly MarketplaceOrderWorkflowService $workflow
    ) {
    }

    public function index(
        Request $request
    ): View|RedirectResponse {
        if (
            ! $request
                ->session()
                ->get('is_courier')
        ) {
            return redirect()
                ->route('login');
        }

        $rider =
            $this->currentRider(
                $request
            );

        $assignments =
            DB::table(
                'last_mile_assignments as assignment'
            )
                ->join(
                    'logistics_facilities as facility',
                    'facility.id',
                    '=',
                    'assignment.destination_facility_id'
                )
                ->where(
                    'assignment.courier_account_id',
                    $rider->id
                )
                ->whereIn(
                    'assignment.status',
                    [
                        'assigned',
                        'out_for_delivery',
                        'completed',
                    ]
                )
                ->select([
                    'assignment.*',

                    'facility.name as facility_name',
                    'facility.address as facility_address',

                    'facility.municipality_name as facility_municipality',
                ])
                ->orderByRaw("
                    CASE
                        WHEN assignment.status = 'out_for_delivery' THEN 1
                        WHEN assignment.status = 'assigned' THEN 2
                        ELSE 3
                    END
                ")
                ->orderByDesc(
                    'assignment.id'
                )
                ->get();

        foreach (
            $assignments
            as $assignment
        ) {
            $assignment->orders =
                DB::table(
                    'last_mile_assignment_orders as link'
                )
                    ->join(
                        'marketplace_orders as orders',
                        'orders.id',
                        '=',
                        'link.marketplace_order_id'
                    )
                    ->leftJoin(
                        'logistics_parcels as parcel',
                        'parcel.marketplace_order_id',
                        '=',
                        'orders.id'
                    )
                    ->where(
                        'link.last_mile_assignment_id',
                        $assignment->id
                    )
                    ->select([
                        'orders.id',
                        'orders.order_number',
                        'orders.buyer_name',
                        'orders.buyer_phone',
                        'orders.buyer_address',

                        'orders.status as order_status',

                        'parcel.status as parcel_status',
                    ])
                    ->orderBy(
                        'orders.order_number'
                    )
                    ->get();
        }

        return view(
            'courier.last-mile',
            compact(
                'assignments',
                'rider'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Start Last-Mile Route
    |--------------------------------------------------------------------------
    */

    public function start(
        Request $request,
        int $assignment
    ): RedirectResponse {
        $rider =
            $this->currentRider(
                $request
            );

        DB::transaction(
            function () use (
                $rider,
                $assignment
            ): void {
                $lastMile =
                    DB::table(
                        'last_mile_assignments'
                    )
                        ->where(
                            'id',
                            $assignment
                        )
                        ->where(
                            'courier_account_id',
                            $rider->id
                        )
                        ->lockForUpdate()
                        ->first();

                abort_unless(
                    $lastMile,
                    404
                );

                if (
                    $lastMile->status
                    !== 'assigned'
                ) {
                    throw ValidationException::withMessages([
                        'assignment' =>
                            'This last-mile assignment cannot be started.',
                    ]);
                }

                $orderIds =
                    DB::table(
                        'last_mile_assignment_orders'
                    )
                        ->where(
                            'last_mile_assignment_id',
                            $assignment
                        )
                        ->pluck(
                            'marketplace_order_id'
                        );

                if (
                    $orderIds->isEmpty()
                ) {
                    throw ValidationException::withMessages([
                        'assignment' =>
                            'This last-mile assignment has no parcels.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Every parcel must still be ready for last-mile dispatch
                |--------------------------------------------------------------------------
                */

                $eligibleParcels =
                    DB::table(
                        'logistics_parcels'
                    )
                        ->whereIn(
                            'marketplace_order_id',
                            $orderIds
                        )
                        ->where(
                            'status',
                            'last_mile_assigned'
                        )
                        ->lockForUpdate()
                        ->count();

                if (
                    $eligibleParcels
                    !== $orderIds->count()
                ) {
                    throw ValidationException::withMessages([
                        'assignment' =>
                            'One or more parcels are no longer ready for last-mile delivery.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Marketplace orders must belong to this Logistics provider
                |--------------------------------------------------------------------------
                */

                $eligibleOrders =
                    MarketplaceOrder::query()
                        ->whereIn(
                            'id',
                            $orderIds
                        )
                        ->where(
                            'logistics_account_id',
                            $lastMile
                                ->logistics_account_id
                        )
                        ->where(
                            'status',
                            'in_transit'
                        )
                        ->lockForUpdate()
                        ->count();

                if (
                    $eligibleOrders
                    !== $orderIds->count()
                ) {
                    throw ValidationException::withMessages([
                        'assignment' =>
                            'One or more linked orders are not in the expected delivery state.',
                    ]);
                }

                DB::table(
                    'last_mile_assignments'
                )
                    ->where(
                        'id',
                        $assignment
                    )
                    ->update([
                        'status' =>
                            'out_for_delivery',

                        'started_at' =>
                            now(),

                        'updated_at' =>
                            now(),
                    ]);

                DB::table(
                    'logistics_parcels'
                )
                    ->whereIn(
                        'marketplace_order_id',
                        $orderIds
                    )
                    ->where(
                        'status',
                        'last_mile_assigned'
                    )
                    ->update([
                        'status' =>
                            'out_for_delivery',

                        'updated_at' =>
                            now(),
                    ]);

                /*
                |--------------------------------------------------------------------------
                | MarketplaceOrder remains in_transit
                |--------------------------------------------------------------------------
                |
                | It has already been in transit since Seller pickup.
                | No direct DB update is needed here.
                |
                */
            }
        );

        return back()->with(
            'success',
            'Last-mile delivery route started.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Arrived at Buyer
    |--------------------------------------------------------------------------
    */

    public function arrived(
        Request $request,
        int $assignment,
        int $order
    ): RedirectResponse {
        $rider =
            $this->currentRider(
                $request
            );

        DB::transaction(
            function () use (
                $rider,
                $assignment,
                $order
            ): void {
                $lastMile =
                    DB::table(
                        'last_mile_assignments'
                    )
                        ->where(
                            'id',
                            $assignment
                        )
                        ->where(
                            'courier_account_id',
                            $rider->id
                        )
                        ->lockForUpdate()
                        ->first();

                abort_unless(
                    $lastMile,
                    404
                );

                if (
                    $lastMile->status
                    !== 'out_for_delivery'
                ) {
                    throw ValidationException::withMessages([
                        'assignment' =>
                            'Start the last-mile delivery route first.',
                    ]);
                }

                $belongsToAssignment =
                    DB::table(
                        'last_mile_assignment_orders'
                    )
                        ->where(
                            'last_mile_assignment_id',
                            $assignment
                        )
                        ->where(
                            'marketplace_order_id',
                            $order
                        )
                        ->exists();

                abort_unless(
                    $belongsToAssignment,
                    404
                );

                $marketplaceOrder =
                    MarketplaceOrder::query()
                        ->whereKey(
                            $order
                        )
                        ->where(
                            'logistics_account_id',
                            $lastMile
                                ->logistics_account_id
                        )
                        ->lockForUpdate()
                        ->first();

                abort_unless(
                    $marketplaceOrder,
                    404
                );

                $parcel =
                    DB::table(
                        'logistics_parcels'
                    )
                        ->where(
                            'marketplace_order_id',
                            $order
                        )
                        ->lockForUpdate()
                        ->first();

                abort_unless(
                    $parcel,
                    404
                );

                /*
                |--------------------------------------------------------------------------
                | Safe repeated request
                |--------------------------------------------------------------------------
                */

                if (
                    $parcel->status
                    === 'arrived_buyer'
                    &&
                    $marketplaceOrder->status
                    === 'arrived_buyer'
                ) {
                    return;
                }

                if (
                    $parcel->status
                    !== 'out_for_delivery'
                    ||
                    $marketplaceOrder->status
                    !== 'in_transit'
                ) {
                    throw ValidationException::withMessages([
                        'order' =>
                            'This parcel is not currently out for delivery.',
                    ]);
                }

                $riderName =
                    $this->riderName(
                        $rider
                    );

                /*
                |--------------------------------------------------------------------------
                | Use Marketplace workflow
                |--------------------------------------------------------------------------
                |
                | This creates the MarketplaceOrderEvent and triggers the same
                | Eloquent/realtime Seller update as the regular order flow.
                |
                */

                $this->workflow->transition(
                    $marketplaceOrder,
                    'in_transit',
                    'arrived_buyer',
                    'Last-Mile Rider Reached Buyer',
                    $riderName
                        . ' reached the buyer location for order '
                        . $marketplaceOrder
                            ->order_number
                        . '.',
                    'both',
                    [
                        'arrived_buyer_at' =>
                            now(),
                    ]
                );

                DB::table(
                    'logistics_parcels'
                )
                    ->where(
                        'marketplace_order_id',
                        $order
                    )
                    ->update([
                        'status' =>
                            'arrived_buyer',

                        'updated_at' =>
                            now(),
                    ]);
            }
        );

        return back()->with(
            'success',
            'Arrival at Buyer confirmed.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delivered
    |--------------------------------------------------------------------------
    */

    public function delivered(
        Request $request,
        int $assignment,
        int $order
    ): RedirectResponse {
        $rider =
            $this->currentRider(
                $request
            );

        DB::transaction(
            function () use (
                $rider,
                $assignment,
                $order
            ): void {
                $lastMile =
                    DB::table(
                        'last_mile_assignments'
                    )
                        ->where(
                            'id',
                            $assignment
                        )
                        ->where(
                            'courier_account_id',
                            $rider->id
                        )
                        ->lockForUpdate()
                        ->first();

                abort_unless(
                    $lastMile,
                    404
                );

                if (
                    ! in_array(
                        $lastMile->status,
                        [
                            'out_for_delivery',
                            'completed',
                        ],
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'assignment' =>
                            'This last-mile assignment is not currently out for delivery.',
                    ]);
                }

                $belongsToAssignment =
                    DB::table(
                        'last_mile_assignment_orders'
                    )
                        ->where(
                            'last_mile_assignment_id',
                            $assignment
                        )
                        ->where(
                            'marketplace_order_id',
                            $order
                        )
                        ->exists();

                abort_unless(
                    $belongsToAssignment,
                    404
                );

                $marketplaceOrder =
                    MarketplaceOrder::query()
                        ->whereKey(
                            $order
                        )
                        ->where(
                            'logistics_account_id',
                            $lastMile
                                ->logistics_account_id
                        )
                        ->lockForUpdate()
                        ->first();

                abort_unless(
                    $marketplaceOrder,
                    404
                );

                $parcel =
                    DB::table(
                        'logistics_parcels'
                    )
                        ->where(
                            'marketplace_order_id',
                            $order
                        )
                        ->lockForUpdate()
                        ->first();

                abort_unless(
                    $parcel,
                    404
                );

                /*
                |--------------------------------------------------------------------------
                | Safe repeated request
                |--------------------------------------------------------------------------
                */

                if (
                    $parcel->status
                    === 'delivered'
                    &&
                    $marketplaceOrder->status
                    === 'delivered'
                ) {
                    return;
                }

                if (
                    $parcel->status
                    !== 'arrived_buyer'
                    ||
                    $marketplaceOrder->status
                    !== 'arrived_buyer'
                ) {
                    throw ValidationException::withMessages([
                        'order' =>
                            'Confirm arrival at the Buyer before marking the parcel delivered.',
                    ]);
                }

                $riderName =
                    $this->riderName(
                        $rider
                    );

                /*
                |--------------------------------------------------------------------------
                | Marketplace completion + finance
                |--------------------------------------------------------------------------
                |
                | Rule:
                | - courier_email remains the Seller-pickup Rider snapshot.
                | - the explicit Rider ID below receives the full delivery fee.
                |
                */

                $this->workflow->transition(
                    $marketplaceOrder,
                    'arrived_buyer',
                    'delivered',
                    'Order Delivered Successfully',
                    'Order '
                        . $marketplaceOrder
                            ->order_number
                        . ' was successfully delivered to '
                        . $marketplaceOrder
                            ->buyer_name
                        . ' by '
                        . $riderName
                        . '.',
                    'both',
                    [
                        'delivered_at' =>
                            now(),

                        'payment_status' =>
                            $marketplaceOrder
                                ->payment_method
                                === 'COD'
                                    ? 'paid'
                                    : $marketplaceOrder
                                        ->payment_status,
                    ],
                    $rider->id
                );

                DB::table(
                    'logistics_parcels'
                )
                    ->where(
                        'marketplace_order_id',
                        $order
                    )
                    ->update([
                        'status' =>
                            'delivered',

                        'updated_at' =>
                            now(),
                    ]);

                /*
                |--------------------------------------------------------------------------
                | Complete assignment when every parcel is delivered
                |--------------------------------------------------------------------------
                */

                $orderIds =
                    DB::table(
                        'last_mile_assignment_orders'
                    )
                        ->where(
                            'last_mile_assignment_id',
                            $assignment
                        )
                        ->pluck(
                            'marketplace_order_id'
                        );

                $remaining =
                    DB::table(
                        'logistics_parcels'
                    )
                        ->whereIn(
                            'marketplace_order_id',
                            $orderIds
                        )
                        ->where(
                            'status',
                            '!=',
                            'delivered'
                        )
                        ->count();

                if (
                    $remaining === 0
                ) {
                    DB::table(
                        'last_mile_assignments'
                    )
                        ->where(
                            'id',
                            $assignment
                        )
                        ->update([
                            'status' =>
                                'completed',

                            'completed_at' =>
                                now(),

                            'updated_at' =>
                                now(),
                        ]);
                }
            }
        );

        return back()->with(
            'success',
            'Parcel delivered successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Current Rider
    |--------------------------------------------------------------------------
    */

    private function currentRider(
        Request $request
    ): CourierAccount {
        abort_unless(
            $request
                ->session()
                ->get(
                    'is_courier'
                ),
            403
        );

        $email =
            strtolower(
                trim(
                    (string) $request
                        ->session()
                        ->get(
                            'courier_email',
                            ''
                        )
                )
            );

        abort_if(
            $email === '',
            403,
            'Rider identity is missing from the session.'
        );

        $rider =
            CourierAccount::query()
                ->whereRaw(
                    'LOWER(email) = ?',
                    [
                        $email,
                    ]
                )
                ->where(
                    'account_status',
                    'active'
                )
                ->first();

        abort_unless(
            $rider,
            403,
            'Active Rider account required.'
        );

        return $rider;
    }

    private function riderName(
        CourierAccount $rider
    ): string {
        $name =
            trim(
                $rider->first_name
                . ' '
                . $rider->last_name
            );

        return $name !== ''
            ? $name
            : 'SARI Rider';
    }
}