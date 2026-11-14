<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Finance\LogisticsEarning;
use App\Models\Orders\MarketplaceOrder;
use App\Support\CurrentLogisticsHub;
use App\Support\LogisticsOrderAccess;
use App\Support\Pdf\LogisticsReportPdf;
use Illuminate\Http\Response;
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

    public function index(Request $request): View|Response
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
            'print' => ['nullable', 'boolean'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
            'type' => ['nullable', 'in:all,deliveries,pipeline,riders,fees'],
        ]);

        $printReport = $request->boolean('print');
        $reportGeneratedAt = $now;

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

        $trendStart = $printReport ? $dateFrom : $dateTo->subDays(6);

        if ($trendStart->lessThan($dateFrom)) {
            $trendStart = $dateFrom;
        }

        $deliveryTrend = collect();
        $printDailyTotals = $printReport
            ? $completedInRange()
                ->selectRaw('DATE(delivered_at) as report_date, COUNT(*) as delivered_count, COALESCE(SUM(delivery_fee), 0) as delivery_fees')
                ->groupByRaw('DATE(delivered_at)')
                ->get()
                ->keyBy('report_date')
            : collect();

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
                'count' => $printReport
                    ? (int) ($printDailyTotals->get($key)?->delivered_count ?? 0)
                    : (clone $daily)->count(),
                'fees' => $printReport
                    ? (float) ($printDailyTotals->get($key)?->delivery_fees ?? 0)
                    : (float) (clone $daily)->sum('delivery_fee'),
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
            ->when(! $printReport, fn (QueryBuilder $query) => $query->limit(5))
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
            ->when(! $printReport, fn (Builder $query) => $query->limit(15))
            ->get();

        // Company earnings follow the Finance page's manager-only visibility.
        // All totals use the complete delivered-order scope, never the display page.
        $earningsSummary = null;
        $reportEarnings = null;

        if (! $isHubOperator) {
            $earningsQuery = LogisticsEarning::query()
                ->where('logistics_account_id', $logistics->id)
                ->whereIn('marketplace_order_id', $completedInRange()->select('marketplace_orders.id'));

            $earningsSummary = (clone $earningsQuery)
                ->selectRaw('COUNT(*) as recorded_orders')
                ->selectRaw('COALESCE(SUM(gross_shipping_revenue), 0) as gross_shipping')
                ->selectRaw('COALESCE(SUM(pickup_rider_cost), 0) as pickup_cost')
                ->selectRaw('COALESCE(SUM(last_mile_rider_cost), 0) as last_mile_cost')
                ->selectRaw('COALESCE(SUM(net_logistics_amount), 0) as net_logistics')
                ->selectRaw("COALESCE(SUM(CASE WHEN status = 'pending_remittance' THEN net_logistics_amount ELSE 0 END), 0) as pending_earnings")
                ->selectRaw("COALESCE(SUM(CASE WHEN status = 'available' THEN net_logistics_amount ELSE 0 END), 0) as available_earnings")
                ->first();

            $earningRows = (clone $earningsQuery)
                ->with('order')
                ->latest('eligible_at')
                ->orderByDesc('id');

            $reportEarnings = $printReport
                ? $earningRows->get()
                : $earningRows->paginate(15, ['*'], 'earnings_page')->withQueryString();
        }

        if ($printReport) {
            $money = static fn ($amount): string => 'PHP '.number_format((float) $amount, 2);
            $sections = [[
                'title' => '01  Delivery Summary',
                'columns' => ['METRIC', 'VALUE', 'BASIS'],
                'widths' => [280, 140, 341.89],
                'rows' => [
                    ['Delivered in period', number_format($periodDelivered), 'Selected dates and operating branch'],
                    ['Delivery fees in period', $money($periodFees), 'Recorded order delivery fees'],
                    ['Active deliveries', number_format($stats['active_deliveries']), 'Current snapshot at generation time'],
                    ['Delivered today', number_format($stats['delivered_today']), 'Today in Asia/Manila'],
                ],
            ]];
            if (! $isHubOperator && $earningsSummary) {
                $sections[] = [
                    'title' => '02  Earnings & Commissions',
                    'columns' => ['METRIC', 'AMOUNT', 'BASIS'],
                    'widths' => [280, 140, 341.89],
                    'rows' => [
                        ['Gross shipping earnings', $money($earningsSummary->gross_shipping), 'Recorded shipping revenue before Rider shares'],
                        ['Pickup Rider share', $money($earningsSummary->pickup_cost), 'Recorded pickup allocation'],
                        ['Last-mile Rider share', $money($earningsSummary->last_mile_cost), 'Recorded final-mile allocation'],
                        ['Net Logistics commission', $money($earningsSummary->net_logistics), 'Shipping margin after Rider shares, not platform product commission'],
                        ['Pending COD earnings', $money($earningsSummary->pending_earnings), 'Awaiting reconciliation / Admin receipt'],
                        ['Available earnings', $money($earningsSummary->available_earnings), 'Period earnings, not current withdrawable wallet balance'],
                        ['Recorded earning orders', (string) $earningsSummary->recorded_orders, 'All matching records, independent of screen pagination'],
                        ['Delivered without earning record', (string) max(0, $periodDelivered - (int) $earningsSummary->recorded_orders), 'Missing earnings are not estimated'],
                    ],
                ];
                $allocationTotal = max(0, (float) $earningsSummary->pickup_cost) + max(0, (float) $earningsSummary->last_mile_cost) + max(0, (float) $earningsSummary->net_logistics);
                $allocation = [];
                foreach ([['Pickup Rider share', $earningsSummary->pickup_cost], ['Last-mile Rider share', $earningsSummary->last_mile_cost], ['Logistics commission', $earningsSummary->net_logistics]] as [$label, $amount]) {
                    $allocation[] = [$label, $money($amount), number_format($allocationTotal > 0 ? max(0, (float) $amount) / $allocationTotal * 100 : 0, 1).'%'];
                }
                $sections[] = ['title' => 'Shipping Allocation', 'columns' => ['COMPONENT', 'AMOUNT', 'ALLOCATION'], 'widths' => [350, 210, 201.89], 'rows' => $allocation, 'bars' => true];
            }
            $sections[] = [
                'title' => 'Daily Delivery Totals - Full Selected Period',
                'columns' => ['DATE', 'DELIVERED ORDERS', 'DELIVERY FEES'],
                'widths' => [280, 240, 241.89],
                'rows' => $deliveryTrend->map(fn ($day) => [$day['date']->format('M d, Y'), (string) $day['count'], $money($day['fees'])])->all(),
            ];
            $sections[] = [
                'title' => 'Operational Pipeline - Current Snapshot',
                'columns' => ['STAGE', 'COUNT', 'DESCRIPTION'],
                'widths' => [230, 100, 431.89],
                'rows' => $pipeline->map(fn ($entry) => [$entry['label'], (string) $entry['count'], $entry['description']])->all(),
            ];
            $sections[] = [
                'title' => 'Last-Mile Rider Performance - All Ranked Riders',
                'columns' => ['RIDER', 'DELIVERED ORDERS', 'DELIVERY FEES'],
                'widths' => [340, 180, 241.89],
                'rows' => $topLastMileRiders->map(fn ($rider) => [trim($rider->first_name.' '.$rider->last_name), (string) $rider->delivered_count, $money($rider->delivered_fees)])->all(),
            ];
            $sections[] = [
                'title' => 'Completed Deliveries - All Matching Orders',
                'columns' => ['ORDER', 'SELLER', 'BUYER', 'PICKUP RIDER', 'DELIVERY FEE', 'DELIVERED (PHT)'],
                'widths' => [130, 150, 130, 130, 100, 121.89],
                'rows' => $recentDelivered->map(fn ($order) => [$order->order_number, $order->seller?->store_name ?: 'SARI Seller', $order->buyer_name ?: '-', $order->courier_name ?: '-', $money($order->delivery_fee), $order->delivered_at?->copy()->timezone('Asia/Manila')->format('M d, Y g:i A') ?: '-'])->all(),
            ];
            if (! $isHubOperator && $reportEarnings) {
                $sections[] = [
                    'title' => 'Per-Order Earnings - All Matching Records',
                    'columns' => ['ORDER', 'GROSS SHIPPING', 'PICKUP SHARE', 'LAST-MILE SHARE', 'NET COMMISSION', 'STATUS'],
                    'widths' => [150, 120, 115, 120, 125, 131.89],
                    'rows' => $reportEarnings->map(fn ($earning) => [$earning->order?->order_number ?: 'Order', $money($earning->gross_shipping_revenue), $money($earning->pickup_rider_cost), $money($earning->last_mile_rider_cost), $money($earning->net_logistics_amount), ucwords(str_replace('_', ' ', $earning->status))])->all(),
                ];
            }
            $pdf = LogisticsReportPdf::render(
                $hubFacility?->name ?: 'Current operating scope',
                $dateFrom->format('M d, Y').' - '.$dateTo->format('M d, Y'),
                $reportGeneratedAt->format('M d, Y g:i A'),
                $sections
            );
            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="SARI-Logistics-Report-'.$from.'-to-'.$to.'.pdf"',
                'Cache-Control' => 'private, no-store, max-age=0',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

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
                'periodFees',
                'earningsSummary',
                'reportEarnings',
                'printReport',
                'reportGeneratedAt'
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
