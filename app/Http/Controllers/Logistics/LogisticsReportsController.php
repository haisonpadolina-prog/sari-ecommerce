<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Orders\MarketplaceOrder;
use App\Support\CurrentLogisticsHub;
use App\Support\LogisticsOrderAccess;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LogisticsReportsController extends Controller
{
    private const ACTIVE_STATUSES = [
        'courier_accepted', 'heading_pickup', 'arrived_pickup',
        'in_transit', 'arrived_buyer',
    ];

    public function __construct(
        private readonly LogisticsOrderAccess $orderAccess,
    ) {}

    public function index(Request $request): View
    {
        $logistics = $this->orderAccess->prepare($request);
        $isHubOperator = CurrentLogisticsHub::isHubOperator($request);
        $hubFacility = CurrentLogisticsHub::requireOperatingFacility($request);

        if ($hubFacility) {
            abort_unless(
                (int) $hubFacility->logistics_account_id === (int) $logistics->id,
                403,
                'Your assigned hub does not belong to this Logistics network.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Server-side report filters
        |--------------------------------------------------------------------------
        */

        $now = CarbonImmutable::now('Asia/Manila');

        $input = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
            'type' => ['nullable', 'in:all,deliveries,pipeline,riders,fees'],
        ]);

        $from = $input['from'] ?? $now->subDays(29)->toDateString();
        $to = $input['to'] ?? $now->toDateString();
        $reportType = $input['type'] ?? 'all';

        $dateFrom = CarbonImmutable::createFromFormat(
            '!Y-m-d',
            $from,
            'Asia/Manila'
        );

        $dateTo = CarbonImmutable::createFromFormat(
            '!Y-m-d',
            $to,
            'Asia/Manila'
        );

        if (
            $dateFrom->greaterThan($dateTo)
            || $dateFrom->diffInDays($dateTo) > 366
        ) {
            throw ValidationException::withMessages([
                'from' => 'Select a valid date range of up to 367 calendar days (inclusive).',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Provider and assigned-hub visibility
        |--------------------------------------------------------------------------
        */

        $orders = fn (string $visibility = 'involved'): Builder =>
            CurrentLogisticsHub::scopeOrders(
                $request,
                MarketplaceOrder::query()
                    ->where('logistics_account_id', $logistics->id),
                $visibility
            );

        $parcels = fn (string $visibility = 'involved'): Builder =>
            LogisticsParcel::query()->whereHas(
                'order',
                function (Builder $query) use (
                    $request,
                    $logistics,
                    $visibility
                ): void {
                    CurrentLogisticsHub::scopeOrders(
                        $request,
                        $query->where(
                            'logistics_account_id',
                            $logistics->id
                        ),
                        $visibility
                    );
                }
            );

        /*
        |--------------------------------------------------------------------------
        | Completed orders within selected dates
        |--------------------------------------------------------------------------
        */

        $completedInRange = fn (): Builder => $orders()
            ->where('status', 'delivered')
            ->whereDate('delivered_at', '>=', $from)
            ->whereDate('delivered_at', '<=', $to);

        $periodDelivered = $completedInRange()->count();

        $periodFees = (float) $completedInRange()
            ->sum('delivery_fee');

        /*
        |--------------------------------------------------------------------------
        | Current parcel status counts
        |--------------------------------------------------------------------------
        */

        $originCounts = $this->statusCounts(
            $parcels('origin')
        );

        $destinationCounts = $hubFacility
            ? $this->statusCounts($parcels('destination'))
            : $originCounts;

        $origin = fn (string $status): int =>
            (int) $originCounts->get($status, 0);

        $destination = fn (string $status): int =>
            (int) $destinationCounts->get($status, 0);

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        |
        | Delivered and fee figures use selected dates.
        | Active operations and pipeline remain current snapshots.
        |
        */

        $stats = [
            'waiting_assignment' => $orders('origin')
                ->where('status', 'ready_for_pickup')
                ->whereNull('courier_email')
                ->count(),

            'active_deliveries' => $orders()
                ->whereIn('status', self::ACTIVE_STATUSES)
                ->count(),

            'delivered_today' => $orders()
                ->where('status', 'delivered')
                ->whereDate(
                    'delivered_at',
                    $now->toDateString()
                )
                ->count(),

            'delivered_this_month' => $periodDelivered,

            'delivery_fees_this_month' => $periodFees,

            'average_delivery_fee' => $periodDelivered
                ? $periodFees / $periodDelivered
                : 0,

            'origin_processed' => $parcels('origin')
                ->whereNotNull('received_at')
                ->count(),

            'ready_linehaul' => $origin('sorted'),

            'linehaul_active' =>
                $origin('linehaul_assigned')
                + $origin('linehaul_in_transit'),

            'destination_ready' =>
                $destination('destination_received'),

            'last_mile_active' =>
                $destination('last_mile_assigned')
                + $destination('out_for_delivery')
                + $destination('arrived_buyer'),
        ];

        /*
        |--------------------------------------------------------------------------
        | Delivery trend
        |--------------------------------------------------------------------------
        |
        | Displays the last seven calendar days that fall within the
        | user-selected date range.
        |
        */

        $trendStart = $dateTo->subDays(6);

        if ($trendStart->lessThan($dateFrom)) {
            $trendStart = $dateFrom;
        }

        $deliveryTrend = collect();

        for (
            $day = $trendStart;
            $day->lessThanOrEqualTo($dateTo);
            $day = $day->addDay()
        ) {
            $key = $day->toDateString();

            $daily = $completedInRange()
                ->whereDate('delivered_at', $key);

            $deliveryTrend->push([
                'date' => $day,
                'label' => $day->format('D'),
                'short_date' => $day->format('M d'),
                'count' => (clone $daily)->count(),
                'fees' => (float) (clone $daily)
                    ->sum('delivery_fee'),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Current pipeline (not historical)
        |--------------------------------------------------------------------------
        */

        $pipeline = collect([
            [
                'key' => 'pickup',
                'label' => 'Pickup Queue',
                'description' => 'Currently waiting for Seller pickup assignment',
                'count' => $stats['waiting_assignment'],
                'tone' => 'amber',
            ],
            [
                'key' => 'origin',
                'label' => 'Origin Hub',
                'description' => 'Currently received or sorted at origin',
                'count' => $origin('received') + $origin('sorted'),
                'tone' => 'neutral',
            ],
            [
                'key' => 'linehaul',
                'label' => 'Hub Transfer',
                'description' => 'Currently assigned to or moving in linehaul',
                'count' => $stats['linehaul_active'],
                'tone' => 'blue',
            ],
            [
                'key' => 'destination',
                'label' => 'Destination Hub',
                'description' => 'Currently received and waiting for local dispatch',
                'count' => $stats['destination_ready'],
                'tone' => 'green',
            ],
            [
                'key' => 'last_mile',
                'label' => 'Last Mile',
                'description' => 'Currently assigned or with local Rider',
                'count' => $stats['last_mile_active'],
                'tone' => 'blue',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Last-Mile Rider ranking within selected dates
        |--------------------------------------------------------------------------
        */

        $ranking = DB::table(
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
                'orders.logistics_account_id',
                $logistics->id
            )
            ->where('orders.status', 'delivered')
            ->whereDate('orders.delivered_at', '>=', $from)
            ->whereDate('orders.delivered_at', '<=', $to);

        /*
        | Hub Operators see rankings belonging to their
        | assigned destination hub only.
        */

        if ($hubFacility) {
            $ranking
                ->where(
                    'assignment.destination_facility_id',
                    $hubFacility->id
                )
                ->whereExists(
                    function (QueryBuilder $query) use ($hubFacility): void {
                        $query
                            ->selectRaw('1')
                            ->from(
                                'delivery_route_stops as route_stop'
                            )
                            ->whereColumn(
                                'route_stop.marketplace_order_id',
                                'orders.id'
                            )
                            ->where(
                                'route_stop.stop_key',
                                'destination_facility'
                            )
                            ->where(
                                'route_stop.logistics_facility_id',
                                $hubFacility->id
                            );
                    }
                );
        }

        $topLastMileRiders = $ranking
            ->select(
                'rider.id',
                'rider.first_name',
                'rider.last_name',
                'rider.vehicle_type',
                'rider.barangay_name',
                'rider.municipality_name'
            )
            ->selectRaw(
                'COUNT(DISTINCT orders.id) as delivered_count'
            )
            ->selectRaw(
                'SUM(COALESCE(orders.delivery_fee, 0)) as delivered_fees'
            )
            ->groupBy(
                'rider.id',
                'rider.first_name',
                'rider.last_name',
                'rider.vehicle_type',
                'rider.barangay_name',
                'rider.municipality_name'
            )
            ->orderByDesc('delivered_count')
            ->orderBy('rider.first_name')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Filtered completed-order breakdown
        |--------------------------------------------------------------------------
        */

        $recentDelivered = $completedInRange()
            ->with([
                'seller',
                'deliveryRouteStops.facility',
            ])
            ->latest('delivered_at')
            ->limit(15)
            ->get();

        return view(
            'logistics.reports',
            compact(
                'stats',
                'deliveryTrend',
                'pipeline',
                'topLastMileRiders',
                'recentDelivered',
                'isHubOperator',
                'hubFacility',
                'from',
                'to',
                'reportType',
                'periodDelivered',
                'periodFees'
            )
        );
    }

    private function statusCounts(Builder $query): Collection
    {
        return $query
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(fn ($count): int => (int) $count);
    }
}
