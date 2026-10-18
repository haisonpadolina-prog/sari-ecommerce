<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Finance\RiderEarning;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourierDashboardController extends Controller
{
    private const PICKUP_ACTIVE_STATUSES = [
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
    ];

    private const DIRECT_ACTIVE_STATUSES = [
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
        'arrived_buyer',
    ];

    private const ACTIVE_LAST_MILE_STATUSES = [
        'assigned',
        'out_for_delivery',
    ];

    public function index(Request $request): View|RedirectResponse
    {
        if (! $request->session()->get('is_courier')) {
            return redirect()->route('login');
        }

        $rider = $this->currentRider($request);

        $currentDelivery = $this->activeMarketplaceWorkQuery($rider)
            ->with([
                'seller',
                'logisticsParcel',
            ])
            ->latest('updated_at')
            ->first();

        return $this->dashboard(
            $rider,
            $currentDelivery
        );
    }

    public function show(
        Request $request,
        MarketplaceOrder $order
    ): View|RedirectResponse {
        if (! $request->session()->get('is_courier')) {
            return redirect()->route('login');
        }

        $rider = $this->currentRider($request);

        $courierEmail = strtolower(
            trim(
                (string) $rider->email
            )
        );

        abort_unless(
            strtolower(
                trim(
                    (string) $order->courier_email
                )
            ) === $courierEmail,
            403
        );

        $order->loadMissing([
            'seller',
            'logisticsParcel',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Logistics Seller pickup
        |--------------------------------------------------------------------------
        |
        | The pickup Rider owns this job only until the parcel is physically
        | accepted by the origin hub.
        |
        */

        if ($order->logisticsParcel) {
            abort_unless(
                in_array(
                    $order->status,
                    self::PICKUP_ACTIVE_STATUSES,
                    true
                )
                &&
                $order->logisticsParcel->status === 'awaiting_intake',
                404
            );
        } else {
            /*
            |--------------------------------------------------------------------------
            | Legacy / direct delivery
            |--------------------------------------------------------------------------
            */

            abort_unless(
                in_array(
                    $order->status,
                    self::DIRECT_ACTIVE_STATUSES,
                    true
                ),
                404
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Dedicated workflow page
        |--------------------------------------------------------------------------
        */

        return view(
            'courier.delivery',
            compact(
                'order',
                'rider'
            )
        );
    }

    private function dashboard(
        CourierAccount $rider,
        ?MarketplaceOrder $currentDelivery
    ): View {
        $courierEmail = strtolower(
            trim(
                (string) $rider->email
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Pickup / Direct queue
        |--------------------------------------------------------------------------
        */

        $assignments = MarketplaceOrder::query()
            ->with([
                'seller',
                'logisticsParcel',
            ])
            ->whereRaw(
                'LOWER(courier_email) = ?',
                [
                    $courierEmail,
                ]
            )
            ->where(
                'status',
                'courier_accepted'
            )
            ->where(
                function (
                    Builder $query
                ): void {
                    $query
                        ->whereDoesntHave(
                            'logisticsParcel'
                        )
                        ->orWhereHas(
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
            )
            ->latest(
                'accepted_at'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Last-Mile work
        |--------------------------------------------------------------------------
        */

        $lastMileAssignments =
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
                    self::ACTIVE_LAST_MILE_STATUSES
                )
                ->select([
                    'assignment.id',
                    'assignment.assignment_code',
                    'assignment.delivery_area',
                    'assignment.status',
                    'assignment.assigned_at',
                    'assignment.started_at',

                    'facility.name as facility_name',
                    'facility.address as facility_address',
                ])
                ->orderByRaw(
                    "
                    CASE
                        WHEN assignment.status = 'out_for_delivery'
                            THEN 1
                        ELSE 2
                    END
                    "
                )
                ->orderByDesc(
                    'assignment.id'
                )
                ->get();

        foreach (
            $lastMileAssignments
            as $assignment
        ) {
            $assignment->parcel_count =
                DB::table(
                    'last_mile_assignment_orders'
                )
                    ->where(
                        'last_mile_assignment_id',
                        $assignment->id
                    )
                    ->count();

            $assignment->delivery_fee_total =
                (float) DB::table(
                    'last_mile_assignment_orders as link'
                )
                    ->join(
                        'marketplace_orders as orders',
                        'orders.id',
                        '=',
                        'link.marketplace_order_id'
                    )
                    ->where(
                        'link.last_mile_assignment_id',
                        $assignment->id
                    )
                    ->sum(
                        'orders.delivery_fee'
                    );
        }

        $currentLastMile =
            $lastMileAssignments->first();

        /*
        |--------------------------------------------------------------------------
        | Logistics pickup statistics
        |--------------------------------------------------------------------------
        */

        $pickupWaiting =
            MarketplaceOrder::query()
                ->whereRaw(
                    'LOWER(courier_email) = ?',
                    [
                        $courierEmail,
                    ]
                )
                ->where(
                    'status',
                    'courier_accepted'
                )
                ->whereHas(
                    'logisticsParcel',
                    fn (
                        Builder $query
                    ) =>
                        $query->where(
                            'status',
                            'awaiting_intake'
                        )
                )
                ->count();

        $pickupActive =
            MarketplaceOrder::query()
                ->whereRaw(
                    'LOWER(courier_email) = ?',
                    [
                        $courierEmail,
                    ]
                )
                ->whereIn(
                    'status',
                    [
                        'heading_pickup',
                        'arrived_pickup',
                        'in_transit',
                    ]
                )
                ->whereHas(
                    'logisticsParcel',
                    fn (
                        Builder $query
                    ) =>
                        $query->where(
                            'status',
                            'awaiting_intake'
                        )
                )
                ->count();

        /*
        |--------------------------------------------------------------------------
        | Legacy / direct statistics
        |--------------------------------------------------------------------------
        */

        $directWaiting =
            MarketplaceOrder::query()
                ->whereRaw(
                    'LOWER(courier_email) = ?',
                    [
                        $courierEmail,
                    ]
                )
                ->where(
                    'status',
                    'courier_accepted'
                )
                ->whereDoesntHave(
                    'logisticsParcel'
                )
                ->count();

        $directActive =
            MarketplaceOrder::query()
                ->whereRaw(
                    'LOWER(courier_email) = ?',
                    [
                        $courierEmail,
                    ]
                )
                ->whereIn(
                    'status',
                    [
                        'heading_pickup',
                        'arrived_pickup',
                        'in_transit',
                        'arrived_buyer',
                    ]
                )
                ->whereDoesntHave(
                    'logisticsParcel'
                )
                ->count();

        /*
        |--------------------------------------------------------------------------
        | Last-Mile statistics
        |--------------------------------------------------------------------------
        */

        $lastMileWaiting =
            $lastMileAssignments
                ->where(
                    'status',
                    'assigned'
                )
                ->count();

        $lastMileActive =
            $lastMileAssignments
                ->where(
                    'status',
                    'out_for_delivery'
                )
                ->count();

        /*
        |--------------------------------------------------------------------------
        | Rider earnings
        |--------------------------------------------------------------------------
        */

        $todayStart =
            now('Asia/Manila')
                ->startOfDay()
                ->utc();

        $tomorrowStart =
            now('Asia/Manila')
                ->addDay()
                ->startOfDay()
                ->utc();

        $todayLedger =
            RiderEarning::query()
                ->where(
                    'courier_account_id',
                    $rider->id
                )
                ->where(
                    'earned_at',
                    '>=',
                    $todayStart
                )
                ->where(
                    'earned_at',
                    '<',
                    $tomorrowStart
                );

        $stats = [
            'assigned_waiting' =>
                $pickupWaiting
                + $directWaiting
                + $lastMileWaiting,

            'active_deliveries' =>
                $pickupActive
                + $directActive
                + $lastMileActive,

            'completed_today' =>
                (clone $todayLedger)
                    ->count(),

            'earnings_today' =>
                (float) (
                    clone $todayLedger
                )->sum(
                    'delivery_fee_amount'
                ),

            'pickup_waiting' =>
                $pickupWaiting
                + $directWaiting,

            'pickup_active' =>
                $pickupActive
                + $directActive,

            'last_mile_waiting' =>
                $lastMileWaiting,

            'last_mile_active' =>
                $lastMileActive,

            'available_requests' =>
                $pickupWaiting
                + $directWaiting,
        ];

        return view(
            'courier.dashboard',
            compact(
                'currentDelivery',
                'currentLastMile',
                'lastMileAssignments',
                'assignments',
                'stats',
                'rider'
            )
        );
    }

    private function activeMarketplaceWorkQuery(
        CourierAccount $rider
    ): Builder {
        $courierEmail = strtolower(
            trim(
                (string) $rider->email
            )
        );

        return MarketplaceOrder::query()
            ->whereRaw(
                'LOWER(courier_email) = ?',
                [
                    $courierEmail,
                ]
            )
            ->where(
                function (
                    Builder $query
                ): void {
                    /*
                    |--------------------------------------------------------------------------
                    | Direct work
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
                                        self::DIRECT_ACTIVE_STATUSES
                                    );
                            }
                        )

                        /*
                        |--------------------------------------------------------------------------
                        | Logistics Seller pickup
                        |--------------------------------------------------------------------------
                        */

                        ->orWhere(
                            function (
                                Builder $pickup
                            ): void {
                                $pickup
                                    ->whereIn(
                                        'status',
                                        self::PICKUP_ACTIVE_STATUSES
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

    private function currentRider(
        Request $request
    ): CourierAccount {
        $id =
            (int) $request
                ->session()
                ->get(
                    'courier_account_id',
                    0
                );

        $rider =
            $id > 0
                ? CourierAccount::query()
                    ->whereKey(
                        $id
                    )
                    ->where(
                        'account_status',
                        'active'
                    )
                    ->first()
                : null;

        if (! $rider) {
            $email = strtolower(
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
        }

        abort_unless(
            $rider,
            403,
            'Active Rider account required.'
        );

        $request
            ->session()
            ->put(
                'courier_account_id',
                $rider->id
            );

        return $rider;
    }
}