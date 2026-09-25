<?php

namespace App\Http\Controllers;

use App\Models\BuyerAccount;
use App\Models\CourierAccount;
use App\Models\LogisticsAccount;
use App\Models\MarketplaceOrder;
use App\Models\OrderCommission;
use App\Models\PlatformComplaint;
use App\Models\PlatformSetting;
use App\Models\RegistrationApplication;
use App\Models\SellerAccount;
use App\Models\SellerProduct;
use App\Models\SellerSettlement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse|JsonResponse
    {
        if (!$request->session()->get('is_admin')) {
            if ($request->expectsJson() || $request->boolean('sales_fragment')) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('login');
        }

        // Lightweight async endpoint for the Sales Overview period switcher.
        // This intentionally returns only the Sales Overview panel so the rest
        // of the admin dashboard never reloads when the period changes.
        if ($request->boolean('sales_fragment')) {
            $salesAnalytics = $this->buildSalesAnalytics($request, now());

            return response()->json([
                'period' => $salesAnalytics['period'],
                'period_label' => $salesAnalytics['period_label'],
                'html' => view('admin.partials.sales-overview', [
                    'liveSales' => $salesAnalytics,
                ])->render(),
            ]);
        }

        $summary = RegistrationApplication::query()
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending")
            ->selectRaw("SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved")
            ->selectRaw("SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected")
            ->selectRaw("SUM(CASE WHEN role = 'buyer' AND status = 'pending' THEN 1 ELSE 0 END) AS buyers")
            ->selectRaw("SUM(CASE WHEN role = 'seller' AND status = 'pending' THEN 1 ELSE 0 END) AS sellers")
            ->selectRaw("SUM(CASE WHEN role = 'courier' AND status = 'pending' THEN 1 ELSE 0 END) AS couriers")
            ->selectRaw("SUM(CASE WHEN role = 'logistics' AND status = 'pending' THEN 1 ELSE 0 END) AS logistics")
            ->selectRaw("SUM(CASE WHEN role = 'rider' AND status = 'pending' THEN 1 ELSE 0 END) AS riders")
            ->first();

        $registrationStats = [
            'total' => (int) ($summary->total ?? 0),
            'pending' => (int) ($summary->pending ?? 0),
            'approved' => (int) ($summary->approved ?? 0),
            'rejected' => (int) ($summary->rejected ?? 0),
            'buyers' => (int) ($summary->buyers ?? 0),
            'sellers' => (int) ($summary->sellers ?? 0),
            'couriers' => (int) ($summary->couriers ?? 0),
            'logistics' => (int) ($summary->logistics ?? 0),
            'riders' => (int) ($summary->riders ?? 0),
        ];

        $recentRegistrationApplications = RegistrationApplication::query()
            ->latest('created_at')
            ->limit(8)
            ->get();

        $orders = MarketplaceOrder::query();

        $activeBuyers = BuyerAccount::query()->where('account_status', 'active')->count();
        $activeSellers = SellerAccount::query()->where('account_status', 'active')->count();
        $activeRiders = CourierAccount::query()->where('account_status', 'active')->count();
        $activeLogistics = LogisticsAccount::query()->where('account_status', 'active')->count();

        $openComplaints = Schema::hasTable('platform_complaints')
            ? PlatformComplaint::query()->where('status', 'open')->count()
            : 0;

        $adminOperations = [
            'buyers' => $activeBuyers,
            'sellers' => $activeSellers,
            'riders' => $activeRiders,
            'logistics' => $activeLogistics,
            'orders_total' => (clone $orders)->count(),
            'orders_active' => (clone $orders)->whereNotIn('status', ['delivered', 'cancelled'])->count(),
            'ready_pickup' => (clone $orders)->where('status', 'ready_for_pickup')->count(),
            'in_transit' => (clone $orders)->whereIn('status', [
                'courier_accepted',
                'heading_pickup',
                'arrived_pickup',
                'in_transit',
                'arrived_buyer',
            ])->count(),
            'delivered_today' => (clone $orders)
                ->where('status', 'delivered')
                ->whereDate('delivered_at', now()->toDateString())
                ->count(),
            'gmv' => (float) SellerSettlement::query()->sum('merchandise_amount'),
            'open_complaints' => $openComplaints,
        ];

        $recentOrders = MarketplaceOrder::query()
            ->with('seller')
            ->latest('created_at')
            ->limit(6)
            ->get();

        $salesAnalytics = $this->buildSalesAnalytics($request, now());
        $commissionRate = (float) ($salesAnalytics['commission_rate'] ?? 0);

        $platformCommissionAll = round((float) OrderCommission::query()
            ->sum('net_commission'), 2);

        $activeDeliveryStatuses = [
            'courier_accepted',
            'heading_pickup',
            'arrived_pickup',
            'in_transit',
            'arrived_buyer',
        ];

        $activeRiderEmails = MarketplaceOrder::query()
            ->whereIn('status', $activeDeliveryStatuses)
            ->whereNotNull('courier_email')
            ->distinct()
            ->pluck('courier_email');

        $onlineRiders = Schema::hasColumn('courier_accounts', 'availability_status')
            ? CourierAccount::query()
                ->where('account_status', 'active')
                ->where('availability_status', 'online')
                ->count()
            : $activeRiders;

        $ridersOnDelivery = CourierAccount::query()
            ->where('account_status', 'active')
            ->whereIn('email', $activeRiderEmails)
            ->count();

        $idleRiders = max(0, $onlineRiders - $ridersOnDelivery);
        $offlineRiders = max(0, $activeRiders - $onlineRiders);

        $deliveredCount = MarketplaceOrder::query()->where('status', 'delivered')->count();
        $cancelledCount = MarketplaceOrder::query()->where('status', 'cancelled')->count();
        $terminalCount = $deliveredCount + $cancelledCount;
        $orderSuccessRate = $terminalCount > 0
            ? round(($deliveredCount / $terminalCount) * 100, 1)
            : 100.0;

        $fulfillmentPool = $deliveredCount + $adminOperations['in_transit'] + $adminOperations['ready_pickup'];
        $fulfillmentRate = $fulfillmentPool > 0
            ? round(($deliveredCount / $fulfillmentPool) * 100, 1)
            : 100.0;

        $sellerTotal = SellerAccount::query()->count();
        $sellerActiveRate = $sellerTotal > 0
            ? round(($activeSellers / $sellerTotal) * 100, 1)
            : 100.0;

        $reviewedRegistrations = $registrationStats['approved'] + $registrationStats['rejected'];
        $registrationApprovalRate = $reviewedRegistrations > 0
            ? round(($registrationStats['approved'] / $reviewedRegistrations) * 100, 1)
            : 100.0;

        $healthScore = (int) round(
            ($orderSuccessRate + $fulfillmentRate + $sellerActiveRate + $registrationApprovalRate) / 4
        );

        $riskLabel = match (true) {
            $openComplaints >= 10 || $adminOperations['ready_pickup'] >= 20 => 'High',
            $openComplaints >= 3 || $adminOperations['ready_pickup'] >= 8 => 'Moderate',
            default => 'Low',
        };

        $today = now()->toDateString();
        $journey = [
            'new' => MarketplaceOrder::query()->where('status', 'new')->whereDate('created_at', $today)->count(),
            'preparing' => MarketplaceOrder::query()->where('status', 'preparing')->count(),
            'ready' => $adminOperations['ready_pickup'],
            'pickup' => MarketplaceOrder::query()->whereIn('status', ['courier_accepted', 'heading_pickup', 'arrived_pickup'])->count(),
            'in_transit' => MarketplaceOrder::query()->whereIn('status', ['in_transit', 'arrived_buyer'])->count(),
            'delivered' => $adminOperations['delivered_today'],
        ];

        $categoryData = $this->categoryBreakdown();

        $activity = collect();

        foreach ($recentRegistrationApplications->take(4) as $application) {
            $activity->push([
                'title' => ucfirst((string) $application->role) . ' registration',
                'body' => $application->fullName() . ' submitted an account application.',
                'time' => $application->created_at?->diffForHumans() ?? 'Recently',
                'kind' => 'registration',
                'timestamp' => $application->created_at?->timestamp ?? 0,
            ]);
        }

        foreach ($recentOrders->take(4) as $order) {
            $activity->push([
                'title' => 'Order ' . $order->statusLabel(),
                'body' => $order->order_number . ' · ' . ($order->seller?->store_name ?: 'SARI Seller'),
                'time' => $order->updated_at?->diffForHumans() ?? 'Recently',
                'kind' => 'order',
                'timestamp' => $order->updated_at?->timestamp ?? 0,
            ]);
        }

        $platformActivity = $activity
            ->sortByDesc('timestamp')
            ->take(4)
            ->values()
            ->map(fn (array $item) => collect($item)->except('timestamp')->all())
            ->all();

        $focus = [
            [
                'count' => $registrationStats['pending'],
                'title' => 'Registrations awaiting review',
                'severity' => $registrationStats['pending'] >= 10 ? 'high' : 'medium',
                'role' => 'Registrations',
                'detail' => 'Pending Buyer, Seller, Logistics and Rider applications are waiting for review.',
                'action' => 'Open the registration center and review the oldest pending applications first.',
                'url' => route('admin.registrations', ['status' => 'pending']),
            ],
            [
                'count' => $registrationStats['sellers'],
                'title' => 'Seller applications pending',
                'severity' => $registrationStats['sellers'] >= 5 ? 'medium' : 'low',
                'role' => 'Sellers',
                'detail' => 'Seller applications require document and business verification before activation.',
                'action' => 'Review seller registration documents and approve or reject each application.',
                'url' => route('admin.registrations', ['status' => 'pending', 'role' => 'seller']),
            ],
            [
                'count' => $openComplaints,
                'title' => 'Open complaints',
                'severity' => $openComplaints >= 5 ? 'high' : ($openComplaints > 0 ? 'medium' : 'low'),
                'role' => 'Support',
                'detail' => 'These complaints are still open and may require an administrator response.',
                'action' => 'Open the complaint center and resolve the oldest or highest-impact cases first.',
                'url' => route('admin.complaints'),
            ],
            [
                'count' => $adminOperations['ready_pickup'],
                'title' => 'Orders waiting for rider assignment',
                'severity' => $adminOperations['ready_pickup'] >= 10 ? 'high' : ($adminOperations['ready_pickup'] > 0 ? 'medium' : 'low'),
                'role' => 'Logistics',
                'detail' => 'Seller orders marked Ready for Pickup are waiting in the Logistics queue.',
                'action' => 'Open Logistics delivery assignment and make sure available riders are assigned.',
                'url' => url('/logistics/delivery-assignment'),
            ],
            [
                'count' => $adminOperations['in_transit'],
                'title' => 'Active deliveries in progress',
                'severity' => 'low',
                'role' => 'Riders',
                'detail' => 'These orders are currently assigned or moving through the Rider delivery flow.',
                'action' => 'Open the platform report or Logistics monitoring page to review delivery progress.',
                'url' => url('/logistics/delivery-monitoring'),
            ],
        ];

        $adminDashboardLive = [
            'kpis' => [
                'total_users' => $activeBuyers + $activeSellers + $activeRiders + $activeLogistics,
                'pending_registrations' => $registrationStats['pending'],
                'active_sellers' => $activeSellers,
                'total_orders' => $adminOperations['orders_total'],
                'open_complaints' => $openComplaints,
                'platform_commission' => $platformCommissionAll,
                'commission_rate' => $commissionRate,
            ],
            'roles' => [
                'sellers' => ['value' => $activeSellers, 'issue' => $registrationStats['sellers'] . ' pending'],
                'buyers' => ['value' => $activeBuyers, 'issue' => $registrationStats['buyers'] . ' pending'],
                'logistics' => ['value' => $activeLogistics, 'issue' => $registrationStats['logistics'] . ' pending'],
                'riders' => ['value' => $activeRiders, 'issue' => $registrationStats['riders'] . ' pending'],
            ],
            'riders' => [
                'total' => $activeRiders,
                'online' => $onlineRiders,
                'on_delivery' => $ridersOnDelivery,
                'idle' => $idleRiders,
                'offline' => $offlineRiders,
                'overloaded' => 0,
                'active_logistics' => $activeLogistics,
                'ready_pickup' => $adminOperations['ready_pickup'],
                'in_transit' => $adminOperations['in_transit'],
                'open_issues' => $openComplaints,
            ],
            'journey' => $journey,
            'sales' => $salesAnalytics,
            'health' => [
                'score' => $healthScore,
                'risk' => $riskLabel,
                'admin_queue' => $registrationStats['pending'] + $openComplaints,
                'order_success' => $orderSuccessRate,
                'fulfillment' => $fulfillmentRate,
                'seller_active' => $sellerActiveRate,
                'registration_approval' => $registrationApprovalRate,
            ],
            'focus' => $focus,
            'recent_registrations' => $recentRegistrationApplications->take(5)->map(function ($application) {
                return [
                    'name' => $application->fullName(),
                    'role' => ucfirst((string) $application->role),
                    'status' => ucfirst((string) $application->status),
                    'time' => $application->created_at?->diffForHumans() ?? 'Recently',
                    'initial' => mb_strtoupper(mb_substr((string) $application->first_name, 0, 1)) ?: 'S',
                ];
            })->values()->all(),
            'categories' => $categoryData,
            'activity' => $platformActivity,
            'urls' => [
                'registrations' => route('admin.registrations'),
                'users' => route('admin.users'),
                'sellers' => route('admin.seller-accounts.control'),
                'complaints' => route('admin.complaints'),
                'reports' => route('admin.reports'),
                'seller_compliance' => route('admin.seller-compliance'),
                'logistics_monitoring' => url('/logistics/delivery-monitoring'),
                'logistics_assignment' => url('/logistics/delivery-assignment'),
            ],
        ];

        return view('admin.dashboard', compact(
            'registrationStats',
            'recentRegistrationApplications',
            'adminOperations',
            'recentOrders',
            'adminDashboardLive'
        ));
    }

    private function buildSalesAnalytics(Request $request, Carbon $now): array
    {
        $commissionRate = Schema::hasTable('platform_settings')
            ? (float) PlatformSetting::valueOf('commission_rate', 10)
            : 10.0;

        $salesPeriod = strtolower((string) $request->query('sales_period', 'this_month'));
        $allowedSalesPeriods = ['this_month', 'last_month', 'last_3_months', 'this_year'];

        if (!in_array($salesPeriod, $allowedSalesPeriods, true)) {
            $salesPeriod = 'this_month';
        }

        $salesPeriodConfig = $this->salesPeriodConfig($salesPeriod, $now);
        $currentStart = $salesPeriodConfig['start'];
        $currentEnd = $salesPeriodConfig['end'];
        $previousStart = $salesPeriodConfig['comparison_start'];
        $previousEnd = $salesPeriodConfig['comparison_end'];

        $currentPeriodOrders = MarketplaceOrder::query()
            ->whereBetween('created_at', [$currentStart, $currentEnd])
            ->count();

        $previousPeriodOrders = MarketplaceOrder::query()
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->count();

        $currentPeriodSales = (float) SellerSettlement::query()
            ->whereBetween('eligible_at', [$currentStart, $currentEnd])
            ->sum('merchandise_amount');

        $previousPeriodSales = (float) SellerSettlement::query()
            ->whereBetween('eligible_at', [$previousStart, $previousEnd])
            ->sum('merchandise_amount');

        $salesGrowth = $this->percentageChange($currentPeriodSales, $previousPeriodSales);
        $ordersGrowth = $this->percentageChange((float) $currentPeriodOrders, (float) $previousPeriodOrders);

        $currentPeriodCommission = round((float) OrderCommission::query()
            ->whereBetween('earned_at', [$currentStart, $currentEnd])
            ->sum('net_commission'), 2);

        $salesRows = SellerSettlement::query()
            ->whereBetween('eligible_at', [$currentStart, $currentEnd])
            ->get(['merchandise_amount', 'eligible_at']);

        $commissionRows = OrderCommission::query()
            ->whereBetween('earned_at', [$currentStart, $currentEnd])
            ->get(['net_commission', 'earned_at']);

        $salesBuckets = $this->salesSeriesBuckets(
            $salesPeriodConfig['series_mode'],
            $currentStart,
            $currentEnd
        );

        [$salesSeries, $commissionSeries] = $this->salesSeries(
            $salesRows,
            $commissionRows,
            $salesBuckets
        );

        return [
            'period' => $salesPeriod,
            'period_label' => $salesPeriodConfig['label'],
            'subtitle' => $salesPeriodConfig['subtitle'],
            'comparison_label' => $salesPeriodConfig['comparison_label'],
            'chart_copy' => $salesPeriodConfig['chart_copy'],
            'orders' => $currentPeriodOrders,
            // Keep legacy keys for compatibility with older dashboard snapshots.
            'month_orders' => $currentPeriodOrders,
            'orders_growth' => $ordersGrowth,
            'current_sales' => $currentPeriodSales,
            'previous_sales' => $previousPeriodSales,
            'growth' => $salesGrowth,
            'commission' => $currentPeriodCommission,
            'commission_rate' => $commissionRate,
            'series_sales' => $salesSeries,
            'series_commission' => $commissionSeries,
            'series_labels' => array_map(
                static fn (array $bucket): string => $bucket['label'],
                $salesBuckets
            ),
            'series_tooltip_labels' => array_map(
                static fn (array $bucket): string => $bucket['tooltip'],
                $salesBuckets
            ),
            'weekly_sales' => $salesSeries,
            'weekly_commission' => $commissionSeries,
        ];
    }

    private function percentageChange(float $current, float $previous): float
    {
        if ($previous > 0) {
            return round((($current - $previous) / $previous) * 100, 1);
        }

        return $current > 0 ? 100.0 : 0.0;
    }

    private function salesPeriodConfig(string $period, Carbon $now): array
    {
        return match ($period) {
            'last_month' => (function () use ($now): array {
                $reference = $now->copy()->subMonthNoOverflow();
                $comparison = $reference->copy()->subMonthNoOverflow();

                return [
                    'label' => 'Last Month',
                    'subtitle' => 'Last month marketplace activity',
                    'comparison_label' => 'vs previous month',
                    'chart_copy' => 'Weekly snapshot — each point represents one week',
                    'series_mode' => 'weekly',
                    'start' => $reference->copy()->startOfMonth(),
                    'end' => $reference->copy()->endOfMonth(),
                    'comparison_start' => $comparison->copy()->startOfMonth(),
                    'comparison_end' => $comparison->copy()->endOfMonth(),
                ];
            })(),
            'last_3_months' => (function () use ($now): array {
                $start = $now->copy()->subMonthsNoOverflow(2)->startOfMonth();
                $comparisonEnd = $start->copy()->subSecond();
                $comparisonStart = $start->copy()->subMonthsNoOverflow(3)->startOfMonth();

                return [
                    'label' => 'Last 3 Months',
                    'subtitle' => 'Marketplace activity across the last 3 months',
                    'comparison_label' => 'vs previous 3 months',
                    'chart_copy' => 'Monthly snapshot — each point represents one month',
                    'series_mode' => 'monthly',
                    'start' => $start,
                    'end' => $now->copy(),
                    'comparison_start' => $comparisonStart,
                    'comparison_end' => $comparisonEnd,
                ];
            })(),
            'this_year' => (function () use ($now): array {
                $previousYearReference = $now->copy()->subYearNoOverflow();

                return [
                    'label' => 'This Year',
                    'subtitle' => 'Year-to-date marketplace activity',
                    'comparison_label' => 'vs same period last year',
                    'chart_copy' => 'Monthly snapshot — each point represents one month',
                    'series_mode' => 'monthly',
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy(),
                    'comparison_start' => $previousYearReference->copy()->startOfYear(),
                    'comparison_end' => $previousYearReference->copy(),
                ];
            })(),
            default => (function () use ($now): array {
                $previousReference = $now->copy()->subMonthNoOverflow();

                return [
                    'label' => 'This Month',
                    'subtitle' => 'Monthly marketplace activity',
                    'comparison_label' => 'vs last month',
                    'chart_copy' => 'Weekly snapshot — each point represents one week',
                    'series_mode' => 'weekly',
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy(),
                    'comparison_start' => $previousReference->copy()->startOfMonth(),
                    'comparison_end' => $previousReference->copy()->endOfMonth(),
                ];
            })(),
        };
    }

    private function salesSeriesBuckets(string $mode, Carbon $start, Carbon $end): array
    {
        if ($mode === 'monthly') {
            $buckets = [];
            $cursor = $start->copy()->startOfMonth();
            $lastMonth = $end->copy()->startOfMonth();

            while ($cursor->lte($lastMonth)) {
                $bucketStart = $cursor->copy()->startOfMonth();
                $bucketEnd = $cursor->isSameMonth($end)
                    ? $end->copy()
                    : $cursor->copy()->endOfMonth();

                $buckets[] = [
                    'label' => $cursor->format('M'),
                    'tooltip' => $cursor->format('M Y'),
                    'start' => $bucketStart,
                    'end' => $bucketEnd,
                ];

                $cursor->addMonthNoOverflow()->startOfMonth();
            }

            return $buckets;
        }

        $buckets = [];
        $monthStart = $start->copy()->startOfMonth();
        $monthEnd = $start->copy()->endOfMonth();

        for ($index = 0; $index < 5; $index++) {
            $bucketStart = $monthStart->copy()->addDays($index * 7);
            $bucketEnd = $bucketStart->copy()->addDays(6)->endOfDay();

            if ($bucketEnd->gt($monthEnd)) {
                $bucketEnd = $monthEnd->copy();
            }

            $buckets[] = [
                'label' => 'Week ' . ($index + 1),
                'tooltip' => 'Week ' . ($index + 1),
                'start' => $bucketStart,
                'end' => $bucketEnd,
            ];
        }

        return $buckets;
    }

    private function salesSeries(Collection $salesRows, Collection $commissionRows, array $buckets): array
    {
        $sales = array_fill(0, count($buckets), 0.0);
        $commission = array_fill(0, count($buckets), 0.0);

        foreach ($salesRows as $row) {
            if (!$row->eligible_at) {
                continue;
            }

            foreach ($buckets as $index => $bucket) {
                if ($row->eligible_at->betweenIncluded($bucket['start'], $bucket['end'])) {
                    $sales[$index] += (float) ($row->merchandise_amount ?? 0);
                    break;
                }
            }
        }

        foreach ($commissionRows as $row) {
            if (!$row->earned_at) {
                continue;
            }

            foreach ($buckets as $index => $bucket) {
                if ($row->earned_at->betweenIncluded($bucket['start'], $bucket['end'])) {
                    $commission[$index] += (float) ($row->net_commission ?? 0);
                    break;
                }
            }
        }

        return [
            array_map(static fn ($value) => round((float) $value, 2), $sales),
            array_map(static fn ($value) => round((float) $value, 2), $commission),
        ];
    }

    private function categoryBreakdown(): array
    {
        if (!Schema::hasTable('seller_products') || !Schema::hasColumn('seller_products', 'category')) {
            return ['total' => 0, 'items' => []];
        }

        $rows = SellerProduct::query()
            ->whereNull('archived_at')
            ->select('category', DB::raw('COUNT(*) AS product_count'))
            ->groupBy('category')
            ->orderByDesc('product_count')
            ->limit(4)
            ->get();

        $total = (int) SellerProduct::query()->whereNull('archived_at')->count();

        return [
            'total' => $total,
            'items' => $rows->map(function ($row) use ($total) {
                $count = (int) $row->product_count;

                return [
                    'name' => trim((string) $row->category) ?: 'Uncategorized',
                    'count' => $count,
                    'percent' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
                ];
            })->values()->all(),
        ];
    }
}
