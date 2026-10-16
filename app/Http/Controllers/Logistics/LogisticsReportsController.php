<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Support\LogisticsOrderAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogisticsReportsController extends Controller
{
    private const ACTIVE_DELIVERY_STATUSES = [
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

        $now =
            now('Asia/Manila');

        $today =
            $now->toDateString();

        $monthStart =
            $now
                ->copy()
                ->startOfMonth();

        /*
        |--------------------------------------------------------------------------
        | Reusable scoped queries
        |--------------------------------------------------------------------------
        */

        $orders =
            fn () =>
                MarketplaceOrder::query()
                    ->where(
                        'logistics_account_id',
                        $logistics->id
                    );

        $parcels =
            fn () =>
                LogisticsParcel::query()
                    ->whereHas(
                        'order',
                        fn ($query) =>
                            $query->where(
                                'logistics_account_id',
                                $logistics->id
                            )
                    );

        /*
        |--------------------------------------------------------------------------
        | Delivered this month
        |--------------------------------------------------------------------------
        */

        $deliveredThisMonth =
            $orders()
                ->where(
                    'status',
                    'delivered'
                )
                ->where(
                    'delivered_at',
                    '>=',
                    $monthStart
                )
                ->count();

        $deliveryFeesThisMonth =
            (float) $orders()
                ->where(
                    'status',
                    'delivered'
                )
                ->where(
                    'delivered_at',
                    '>=',
                    $monthStart
                )
                ->sum(
                    'delivery_fee'
                );

        $averageDeliveryFee =
            $deliveredThisMonth > 0
                ? $deliveryFeesThisMonth
                    / $deliveredThisMonth
                : 0;

        /*
        |--------------------------------------------------------------------------
        | Current parcel status counts
        |--------------------------------------------------------------------------
        */

        $parcelStatusCounts =
            $parcels()
                ->select('status')
                ->selectRaw(
                    'COUNT(*) as total'
                )
                ->groupBy('status')
                ->pluck(
                    'total',
                    'status'
                )
                ->map(
                    fn ($value): int =>
                        (int) $value
                );

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $stats = [
            'waiting_assignment' =>
                $orders()
                    ->where(
                        'status',
                        'ready_for_pickup'
                    )
                    ->whereNull(
                        'courier_email'
                    )
                    ->count(),

            'active_deliveries' =>
                $orders()
                    ->whereIn(
                        'status',
                        self::ACTIVE_DELIVERY_STATUSES
                    )
                    ->count(),

            'delivered_today' =>
                $orders()
                    ->where(
                        'status',
                        'delivered'
                    )
                    ->whereDate(
                        'delivered_at',
                        $today
                    )
                    ->count(),

            'delivered_this_month' =>
                $deliveredThisMonth,

            'delivery_fees_this_month' =>
                $deliveryFeesThisMonth,

            'average_delivery_fee' =>
                $averageDeliveryFee,

            'origin_processed' =>
                $parcels()
                    ->whereNotNull(
                        'received_at'
                    )
                    ->count(),

            'ready_linehaul' =>
                (int) (
                    $parcelStatusCounts[
                        'sorted'
                    ]
                    ?? 0
                ),

            'linehaul_active' =>
                (int) (
                    (
                        $parcelStatusCounts[
                            'linehaul_assigned'
                        ]
                        ?? 0
                    )
                    +
                    (
                        $parcelStatusCounts[
                            'linehaul_in_transit'
                        ]
                        ?? 0
                    )
                ),

            'destination_ready' =>
                (int) (
                    $parcelStatusCounts[
                        'destination_received'
                    ]
                    ?? 0
                ),

            'last_mile_active' =>
                (int) (
                    (
                        $parcelStatusCounts[
                            'last_mile_assigned'
                        ]
                        ?? 0
                    )
                    +
                    (
                        $parcelStatusCounts[
                            'out_for_delivery'
                        ]
                        ?? 0
                    )
                    +
                    (
                        $parcelStatusCounts[
                            'arrived_buyer'
                        ]
                        ?? 0
                    )
                ),
        ];

        /*
        |--------------------------------------------------------------------------
        | Seven-day delivery trend
        |--------------------------------------------------------------------------
        */

        $deliveryTrend =
            collect(
                range(6, 0)
            )
                ->map(
                    function (
                        int $daysAgo
                    ) use (
                        $orders,
                        $now
                    ): array {
                        $date =
                            $now
                                ->copy()
                                ->subDays(
                                    $daysAgo
                                );

                        $count =
                            $orders()
                                ->where(
                                    'status',
                                    'delivered'
                                )
                                ->whereDate(
                                    'delivered_at',
                                    $date
                                        ->toDateString()
                                )
                                ->count();

                        $fees =
                            (float) $orders()
                                ->where(
                                    'status',
                                    'delivered'
                                )
                                ->whereDate(
                                    'delivered_at',
                                    $date
                                        ->toDateString()
                                )
                                ->sum(
                                    'delivery_fee'
                                );

                        return [
                            'date' =>
                                $date,

                            'label' =>
                                $date->format(
                                    'D'
                                ),

                            'short_date' =>
                                $date->format(
                                    'M d'
                                ),

                            'count' =>
                                $count,

                            'fees' =>
                                $fees,
                        ];
                    }
                );

        /*
        |--------------------------------------------------------------------------
        | Current parcel pipeline
        |--------------------------------------------------------------------------
        */

        $pipeline = collect([
            [
                'key' =>
                    'pickup',

                'label' =>
                    'Pickup Queue',

                'description' =>
                    'Waiting for Seller pickup assignment',

                'count' =>
                    $stats[
                        'waiting_assignment'
                    ],

                'tone' =>
                    'amber',
            ],

            [
                'key' =>
                    'origin',

                'label' =>
                    'Origin Hub',

                'description' =>
                    'Currently received or sorted at origin',

                'count' =>
                    (
                        $parcelStatusCounts[
                            'received'
                        ]
                        ?? 0
                    )
                    +
                    (
                        $parcelStatusCounts[
                            'sorted'
                        ]
                        ?? 0
                    ),

                'tone' =>
                    'neutral',
            ],

            [
                'key' =>
                    'linehaul',

                'label' =>
                    'Hub Transfer',

                'description' =>
                    'Assigned to or moving in linehaul',

                'count' =>
                    $stats[
                        'linehaul_active'
                    ],

                'tone' =>
                    'blue',
            ],

            [
                'key' =>
                    'destination',

                'label' =>
                    'Destination Hub',

                'description' =>
                    'Received and waiting for local dispatch',

                'count' =>
                    $stats[
                        'destination_ready'
                    ],

                'tone' =>
                    'green',
            ],

            [
                'key' =>
                    'last_mile',

                'label' =>
                    'Last Mile',

                'description' =>
                    'Assigned or currently with local Rider',

                'count' =>
                    $stats[
                        'last_mile_active'
                    ],

                'tone' =>
                    'blue',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Top Last-Mile Riders this month
        |--------------------------------------------------------------------------
        */

        $topLastMileRiders =
            DB::table(
                'last_mile_assignment_orders as item'
            )
                ->join(
                    'last_mile_assignments as assignment',
                    'assignment.id',
                    '=',
                    'item.last_mile_assignment_id'
                )
                ->join(
                    'courier_accounts as rider',
                    'rider.id',
                    '=',
                    'assignment.courier_account_id'
                )
                ->join(
                    'marketplace_orders as orders',
                    'orders.id',
                    '=',
                    'item.marketplace_order_id'
                )
                ->where(
                    'assignment.logistics_account_id',
                    $logistics->id
                )
                ->where(
                    'orders.status',
                    'delivered'
                )
                ->where(
                    'orders.delivered_at',
                    '>=',
                    $monthStart
                )
                ->select([
                    'rider.id',
                    'rider.first_name',
                    'rider.last_name',
                    'rider.vehicle_type',
                    'rider.barangay_name',
                    'rider.municipality_name',
                ])
                ->selectRaw(
                    'COUNT(*) as delivered_count'
                )
                ->selectRaw(
                    '
                    SUM(
                        COALESCE(
                            orders.delivery_fee,
                            0
                        )
                    ) as delivered_fees
                    '
                )
                ->groupBy([
                    'rider.id',
                    'rider.first_name',
                    'rider.last_name',
                    'rider.vehicle_type',
                    'rider.barangay_name',
                    'rider.municipality_name',
                ])
                ->orderByDesc(
                    'delivered_count'
                )
                ->orderBy(
                    'rider.first_name'
                )
                ->limit(5)
                ->get();

        /*
        |--------------------------------------------------------------------------
        | Recent completed deliveries
        |--------------------------------------------------------------------------
        */

        $recentDelivered =
            $orders()
                ->with([
                    'seller',
                    'deliveryRouteStops.facility',
                ])
                ->where(
                    'status',
                    'delivered'
                )
                ->latest(
                    'delivered_at'
                )
                ->limit(15)
                ->get();

        return view(
            'logistics.reports',
            compact(
                'stats',
                'deliveryTrend',
                'pipeline',
                'topLastMileRiders',
                'recentDelivered'
            )
        );
    }
}