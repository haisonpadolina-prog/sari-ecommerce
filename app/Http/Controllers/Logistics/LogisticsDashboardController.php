<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Registration\RegistrationApplication;
use App\Support\CurrentLogisticsHub;
use App\Support\LogisticsOrderAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogisticsDashboardController extends Controller
{
    private const PICKUP_ACTIVE_STATUSES = [
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
    ];

    private const DELIVERY_ACTIVE_STATUSES = [
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
        'arrived_buyer',
    ];

    public function __construct(
        private readonly LogisticsOrderAccess $orderAccess,
    ) {
    }

    public function index(Request $request): View
    {
        $logistics =
            $this->orderAccess->prepare($request);

        /*
        |--------------------------------------------------------------------------
        | Current Hub Context
        |--------------------------------------------------------------------------
        */

        $isHubOperator =
            CurrentLogisticsHub::isHubOperator($request);

        $hubFacility =
            CurrentLogisticsHub::requireOperatingFacility($request);

        /*
        |--------------------------------------------------------------------------
        | Order ID Subqueries Per Hub Leg
        |--------------------------------------------------------------------------
        */

        $originOrderIds =
            function () use ($hubFacility) {
                return DB::table('delivery_route_stops')
                    ->select('marketplace_order_id')
                    ->where(
                        'stop_key',
                        'origin_facility'
                    )
                    ->when(
                        $hubFacility,
                        fn ($query) =>
                            $query->where(
                                'logistics_facility_id',
                                $hubFacility->id
                            )
                    );
            };

        $destinationOrderIds =
            function () use ($hubFacility) {
                return DB::table('delivery_route_stops')
                    ->select('marketplace_order_id')
                    ->where(
                        'stop_key',
                        'destination_facility'
                    )
                    ->when(
                        $hubFacility,
                        fn ($query) =>
                            $query->where(
                                'logistics_facility_id',
                                $hubFacility->id
                            )
                    );
            };

        $hubOrderIds =
            function () use ($hubFacility) {
                return DB::table('delivery_route_stops')
                    ->select('marketplace_order_id')
                    ->whereIn(
                        'stop_key',
                        [
                            'origin_facility',
                            'destination_facility',
                        ]
                    )
                    ->when(
                        $hubFacility,
                        fn ($query) =>
                            $query->where(
                                'logistics_facility_id',
                                $hubFacility->id
                            )
                    );
            };

        /*
        |--------------------------------------------------------------------------
        | Reusable Owned Order Queries
        |--------------------------------------------------------------------------
        */

        $ownedOrders =
            fn () =>
                MarketplaceOrder::query()
                    ->where(
                        'logistics_account_id',
                        $logistics->id
                    );

        $originOrders =
            function () use (
                $ownedOrders,
                $originOrderIds,
                $hubFacility
            ) {
                $query = $ownedOrders();

                if ($hubFacility) {
                    $query->whereIn(
                        'id',
                        $originOrderIds()
                    );
                }

                return $query;
            };

        $destinationOrders =
            function () use (
                $ownedOrders,
                $destinationOrderIds,
                $hubFacility
            ) {
                $query = $ownedOrders();

                if ($hubFacility) {
                    $query->whereIn(
                        'id',
                        $destinationOrderIds()
                    );
                }

                return $query;
            };

        $hubOrders =
            function () use (
                $ownedOrders,
                $hubOrderIds,
                $hubFacility
            ) {
                $query = $ownedOrders();

                if ($hubFacility) {
                    $query->whereIn(
                        'id',
                        $hubOrderIds()
                    );
                }

                return $query;
            };

        /*
        |--------------------------------------------------------------------------
        | Reusable Owned Parcel Queries
        |--------------------------------------------------------------------------
        */

        $originParcels =
            function () use (
                $logistics,
                $originOrderIds,
                $hubFacility
            ) {
                return LogisticsParcel::query()
                    ->whereHas(
                        'order',
                        function ($query) use (
                            $logistics,
                            $originOrderIds,
                            $hubFacility
                        ): void {
                            $query->where(
                                'logistics_account_id',
                                $logistics->id
                            );

                            if ($hubFacility) {
                                $query->whereIn(
                                    'id',
                                    $originOrderIds()
                                );
                            }
                        }
                    );
            };

        $destinationParcels =
            function () use (
                $logistics,
                $destinationOrderIds,
                $hubFacility
            ) {
                return LogisticsParcel::query()
                    ->whereHas(
                        'order',
                        function ($query) use (
                            $logistics,
                            $destinationOrderIds,
                            $hubFacility
                        ): void {
                            $query->where(
                                'logistics_account_id',
                                $logistics->id
                            );

                            if ($hubFacility) {
                                $query->whereIn(
                                    'id',
                                    $destinationOrderIds()
                                );
                            }
                        }
                    );
            };

        /*
        |--------------------------------------------------------------------------
        | Approved Rider Applications
        |--------------------------------------------------------------------------
        */

        $approvedRiderApplicationIds =
            RegistrationApplication::query()
                ->where(
                    function ($query) use ($logistics): void {
                        $query
                            ->where(
                                'logistics_account_id',
                                $logistics->id
                            )
                            ->orWhereNull(
                                'logistics_account_id'
                            );
                    }
                )
                ->where(
                    'role',
                    'rider'
                )
                ->where(
                    'status',
                    'approved'
                )
                ->select('id');

        $riders =
            function () use (
                $logistics,
                $approvedRiderApplicationIds,
                $hubFacility
            ) {
                $query =
                    CourierAccount::query()
                        ->where(
                            function ($query) use ($logistics): void {
                                $query
                                    ->where(
                                        'logistics_account_id',
                                        $logistics->id
                                    )
                                    ->orWhereNull(
                                        'logistics_account_id'
                                    );
                            }
                        )
                        ->whereIn(
                            'registration_application_id',
                            $approvedRiderApplicationIds
                        );

                if ($hubFacility) {
                    $query->whereRaw(
                        'LOWER(TRIM(municipality_name)) = ?',
                        [
                            strtolower(
                                trim(
                                    (string)
                                        $hubFacility
                                            ->municipality_name
                                )
                            ),
                        ]
                    );
                }

                return $query;
            };

        /*
        |--------------------------------------------------------------------------
        | Hub Transfer Query
        |--------------------------------------------------------------------------
        */

        $hubTransfers =
            function () use (
                $logistics,
                $hubFacility
            ) {
                $query =
                    DB::table('hub_transfers')
                        ->where(
                            'logistics_account_id',
                            $logistics->id
                        );

                if ($hubFacility) {
                    $query->where(
                        function ($query) use ($hubFacility): void {
                            $query
                                ->where(
                                    'origin_facility_id',
                                    $hubFacility->id
                                )
                                ->orWhere(
                                    'destination_facility_id',
                                    $hubFacility->id
                                );
                        }
                    );
                }

                return $query;
            };

        /*
        |--------------------------------------------------------------------------
        | Last-Mile Assignment Query
        |--------------------------------------------------------------------------
        */

        $lastMileAssignments =
            function () use (
                $logistics,
                $hubFacility
            ) {
                $query =
                    DB::table(
                        'last_mile_assignments'
                    )
                        ->where(
                            'logistics_account_id',
                            $logistics->id
                        );

                if ($hubFacility) {
                    $query->where(
                        'destination_facility_id',
                        $hubFacility->id
                    );
                }

                return $query;
            };

        /*
        |--------------------------------------------------------------------------
        | Dashboard Statistics
        |--------------------------------------------------------------------------
        */

        $stats = [

            'pending_rider_applications' =>
                $hubFacility
                    ? 0
                    : RegistrationApplication::query()
                        ->where(
                            function ($query) use ($logistics): void {
                                $query
                                    ->where(
                                        'logistics_account_id',
                                        $logistics->id
                                    )
                                    ->orWhereNull(
                                        'logistics_account_id'
                                    );
                            }
                        )
                        ->where(
                            'role',
                            'rider'
                        )
                        ->where(
                            'status',
                            'pending'
                        )
                        ->count(),

            'active_riders' =>
                $riders()
                    ->where(
                        'account_status',
                        'active'
                    )
                    ->count(),

            'online_riders' =>
                $riders()
                    ->where(
                        'account_status',
                        'active'
                    )
                    ->where(
                        'availability_status',
                        'online'
                    )
                    ->count(),

            'active_facilities' =>
                $hubFacility
                    ? 1
                    : LogisticsFacility::query()
                        ->forLogistics(
                            $logistics->id
                        )
                        ->active()
                        ->count(),

            'network_facilities' =>
                LogisticsFacility::query()
                    ->forLogistics(
                        $logistics->id
                    )
                    ->active()
                    ->count(),

            /*
             * Seller Pickup / Origin Hub
             */

            'pickup_requests' =>
                $originOrders()
                    ->where(
                        'status',
                        'ready_for_pickup'
                    )
                    ->whereNull(
                        'courier_email'
                    )
                    ->count(),

            'pickup_active' =>
                $originOrders()
                    ->whereIn(
                        'status',
                        self::PICKUP_ACTIVE_STATUSES
                    )
                    ->whereNotNull(
                        'courier_email'
                    )
                    ->count(),

            'awaiting_intake' =>
                $originParcels()
                    ->where(
                        'status',
                        'awaiting_intake'
                    )
                    ->count(),

            'sorting_queue' =>
                $originParcels()
                    ->where(
                        'status',
                        'received'
                    )
                    ->count(),

            'ready_for_transfer' =>
                $originParcels()
                    ->where(
                        'status',
                        'sorted'
                    )
                    ->count(),

            /*
             * Hub Transfer
             */

            'hub_transfer_preparing' =>
                $hubTransfers()
                    ->where(
                        'status',
                        'preparing'
                    )
                    ->count(),

            'hub_transfer_in_transit' =>
                $hubTransfers()
                    ->where(
                        'status',
                        'in_transit'
                    )
                    ->count(),

            'hub_transfer_received_today' =>
                $hubTransfers()
                    ->where(
                        'status',
                        'received'
                    )
                    ->whereDate(
                        'received_at',
                        now()->toDateString()
                    )
                    ->count(),

            /*
             * Destination Hub
             */

            'destination_received' =>
                $destinationParcels()
                    ->where(
                        'status',
                        'destination_received'
                    )
                    ->count(),

            /*
             * Last Mile
             */

            'last_mile_assigned' =>
                $lastMileAssignments()
                    ->where(
                        'status',
                        'assigned'
                    )
                    ->count(),

            'last_mile_out_for_delivery' =>
                $lastMileAssignments()
                    ->where(
                        'status',
                        'out_for_delivery'
                    )
                    ->count(),

            /*
             * Completed
             */

            'delivered_today' =>
                $destinationOrders()
                    ->where(
                        'status',
                        'delivered'
                    )
                    ->whereDate(
                        'delivered_at',
                        now()->toDateString()
                    )
                    ->count(),

            'total_delivered' =>
                $destinationOrders()
                    ->where(
                        'status',
                        'delivered'
                    )
                    ->count(),

            'orders_today' =>
                $hubOrders()
                    ->whereDate(
                        'created_at',
                        now()->toDateString()
                    )
                    ->count(),

            'active_deliveries' =>
                $hubOrders()
                    ->whereIn(
                        'status',
                        self::DELIVERY_ACTIVE_STATUSES
                    )
                    ->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Derived Statistics
        |--------------------------------------------------------------------------
        */

        $stats['hub_transfer_active'] =
            $stats['hub_transfer_preparing']
            + $stats['hub_transfer_in_transit'];

        $stats['last_mile_active'] =
            $stats['last_mile_assigned']
            + $stats['last_mile_out_for_delivery'];

        $stats['busy_riders'] =
            (int) $lastMileAssignments()
                ->whereIn(
                    'status',
                    [
                        'assigned',
                        'out_for_delivery',
                    ]
                )
                ->distinct()
                ->count('courier_account_id');

        $stats['online_rate'] =
            $stats['active_riders'] > 0
                ? (int) round(
                    ($stats['online_riders'] / $stats['active_riders']) * 100
                )
                : 0;

        $stats['rider_utilization'] =
            $stats['active_riders'] > 0
                ? (int) round(
                    ($stats['busy_riders'] / $stats['active_riders']) * 100
                )
                : 0;

        /*
        |--------------------------------------------------------------------------
        | Seven-Day Operations Analytics
        |--------------------------------------------------------------------------
        |
        | These arrays feed the dashboard charts. Values remain scoped to the
        | currently selected operating branch through destinationOrders() and
        | hubTransfers().
        |
        */

        $trendStart =
            now()->copy()
                ->subDays(6)
                ->startOfDay();

        $trendEnd =
            now()->copy()
                ->endOfDay();

        $deliveredByDate =
            $destinationOrders()
                ->where(
                    'status',
                    'delivered'
                )
                ->whereBetween(
                    'delivered_at',
                    [
                        $trendStart,
                        $trendEnd,
                    ]
                )
                ->selectRaw(
                    'DATE(delivered_at) as activity_date, COUNT(*) as aggregate'
                )
                ->groupBy(
                    DB::raw('DATE(delivered_at)')
                )
                ->pluck(
                    'aggregate',
                    'activity_date'
                );

        $receivedTransfersByDate =
            $hubTransfers()
                ->where(
                    'status',
                    'received'
                )
                ->whereBetween(
                    'received_at',
                    [
                        $trendStart,
                        $trendEnd,
                    ]
                )
                ->selectRaw(
                    'DATE(received_at) as activity_date, COUNT(*) as aggregate'
                )
                ->groupBy(
                    DB::raw('DATE(received_at)')
                )
                ->pluck(
                    'aggregate',
                    'activity_date'
                );

        $deliveryTrend = [];
        $transferReceiptTrend = [];

        for ($daysAgo = 6; $daysAgo >= 0; $daysAgo--) {
            $day = now()->copy()->subDays($daysAgo);
            $dateKey = $day->toDateString();

            $deliveryTrend[] = [
                'label' => $day->format('D'),
                'date' => $day->format('M j'),
                'value' => (int) ($deliveredByDate[$dateKey] ?? 0),
            ];

            $transferReceiptTrend[] = [
                'label' => $day->format('D'),
                'date' => $day->format('M j'),
                'value' => (int) ($receivedTransfersByDate[$dateKey] ?? 0),
            ];
        }

        $flowBreakdown = [
            [
                'label' => 'Pickup',
                'value' =>
                    $stats['pickup_requests']
                    + $stats['pickup_active'],
            ],
            [
                'label' => 'Hub Intake',
                'value' => $stats['awaiting_intake'],
            ],
            [
                'label' => 'Sorting',
                'value' =>
                    $stats['sorting_queue']
                    + $stats['ready_for_transfer'],
            ],
            [
                'label' => 'Linehaul',
                'value' => $stats['hub_transfer_active'],
            ],
            [
                'label' => 'Destination',
                'value' => $stats['destination_received'],
            ],
            [
                'label' => 'Last Mile',
                'value' => $stats['last_mile_active'],
            ],
        ];

        $flowTotal = collect($flowBreakdown)
            ->sum('value');

        /*
        |--------------------------------------------------------------------------
        | Recent Orders
        |--------------------------------------------------------------------------
        */

        $recentOrders =
            $hubOrders()
                ->with([
                    'seller',
                    'logisticsParcel',
                    'deliveryRouteStops.facility',
                ])
                ->latest('updated_at')
                ->limit(8)
                ->get();

        /*
        |--------------------------------------------------------------------------
        | Last-Mile Details for Recent Orders
        |--------------------------------------------------------------------------
        */

        $recentOrderIds =
            $recentOrders
                ->pluck('id')
                ->all();

        $lastMileByOrder = collect();

        if (! empty($recentOrderIds)) {
            $lastMileByOrder =
                DB::table(
                    'last_mile_assignment_orders as link'
                )
                    ->join(
                        'last_mile_assignments as assignment',
                        'assignment.id',
                        '=',
                        'link.last_mile_assignment_id'
                    )
                    ->join(
                        'courier_accounts as rider',
                        'rider.id',
                        '=',
                        'assignment.courier_account_id'
                    )
                    ->whereIn(
                        'link.marketplace_order_id',
                        $recentOrderIds
                    )
                    ->select([
                        'link.marketplace_order_id',

                        'assignment.assignment_code',
                        'assignment.delivery_area',
                        'assignment.status',

                        'rider.first_name',
                        'rider.last_name',
                    ])
                    ->get()
                    ->keyBy(
                        'marketplace_order_id'
                    );
        }

        /*
        |--------------------------------------------------------------------------
        | Recent Hub Transfers
        |--------------------------------------------------------------------------
        */

        $recentTransfersQuery =
            DB::table(
                'hub_transfers as transfer'
            )
                ->join(
                    'logistics_facilities as origin',
                    'origin.id',
                    '=',
                    'transfer.origin_facility_id'
                )
                ->join(
                    'logistics_facilities as destination',
                    'destination.id',
                    '=',
                    'transfer.destination_facility_id'
                )
                ->where(
                    'transfer.logistics_account_id',
                    $logistics->id
                );

        if ($hubFacility) {
            $recentTransfersQuery->where(
                function ($query) use ($hubFacility): void {
                    $query
                        ->where(
                            'transfer.origin_facility_id',
                            $hubFacility->id
                        )
                        ->orWhere(
                            'transfer.destination_facility_id',
                            $hubFacility->id
                        );
                }
            );
        }

        $recentTransfers =
            $recentTransfersQuery
                ->select([
                    'transfer.id',
                    'transfer.transfer_code',
                    'transfer.origin_facility_id',
                    'transfer.destination_facility_id',
                    'transfer.vehicle_type',
                    'transfer.vehicle_plate',
                    'transfer.driver_name',
                    'transfer.status',
                    'transfer.dispatched_at',
                    'transfer.received_at',
                    'transfer.created_at',

                    'origin.name as origin_name',
                    'destination.name as destination_name',
                ])
                ->orderByDesc(
                    'transfer.id'
                )
                ->limit(4)
                ->get();

        foreach ($recentTransfers as $transfer) {
            $transfer->parcel_count =
                DB::table(
                    'hub_transfer_parcels'
                )
                    ->where(
                        'hub_transfer_id',
                        $transfer->id
                    )
                    ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | Operations Pipeline
        |--------------------------------------------------------------------------
        */

        $pipeline = [
            [
                'label' => 'Pickup Queue',
                'value' => $stats['pickup_requests'],
                'route' => 'logistics.pickup-requests',
                'description' => 'Seller parcels waiting for Logistics verification.',
                'step' => '01',
            ],

            [
                'label' => 'Pickup Assigned',
                'value' => $stats['pickup_active'],
                'route' => 'logistics.delivery-assignment',
                'description' => 'Orders currently assigned to pickup Riders.',
                'step' => '02',
            ],

            [
                'label' => 'Hub Intake',
                'value' => $stats['awaiting_intake'],
                'route' => 'logistics.incoming-parcels',
                'description' => 'Picked-up parcels waiting at the origin hub.',
                'step' => '03',
            ],

            [
                'label' => 'Sorting Queue',
                'value' => $stats['sorting_queue'],
                'route' => 'logistics.parcel-sorting',
                'description' => 'Origin-hub parcels waiting to be sorted.',
                'step' => '04',
            ],

            [
                'label' => 'Ready for Transfer',
                'value' => $stats['ready_for_transfer'],
                'route' => 'logistics.hub-transfers',
                'description' => 'Sorted parcels ready for line-haul batching.',
                'step' => '05',
            ],

            [
                'label' => 'Hub Transfers',
                'value' => $stats['hub_transfer_active'],
                'route' => 'logistics.hub-transfers',
                'description' => 'Preparing or currently in-transit hub transfers.',
                'step' => '06',
            ],

            [
                'label' => 'Destination Hub',
                'value' => $stats['destination_received'],
                'route' => 'logistics.last-mile-assignment',
                'description' => 'Parcels received and waiting for local Riders.',
                'step' => '07',
            ],

            [
                'label' => 'Last Mile',
                'value' => $stats['last_mile_active'],
                'route' => 'logistics.last-mile-assignment',
                'description' => 'Local delivery batches assigned or on route.',
                'step' => '08',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Display Identity
        |--------------------------------------------------------------------------
        */

        $logisticsName =
            $hubFacility
                ? $hubFacility->name
                : $logistics->displayName();

        return view(
            'logistics.dashboard',
            compact(
                'stats',
                'pipeline',
                'deliveryTrend',
                'transferReceiptTrend',
                'flowBreakdown',
                'flowTotal',
                'recentOrders',
                'lastMileByOrder',
                'recentTransfers',
                'logistics',
                'logisticsName',
                'hubFacility',
                'isHubOperator'
            )
        );
    }
}