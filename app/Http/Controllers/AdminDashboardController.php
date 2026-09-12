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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_admin')) {
            return redirect()->route('login');
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

        $commissionRate = Schema::hasTable('platform_settings')
            ? (float) PlatformSetting::valueOf('commission_rate', 10)
            : 10.0;

        $now = now();
        $currentStart = $now->copy()->startOfMonth();
        $currentEnd = $now->copy()->endOfMonth();
        $previousReference = $now->copy()->subMonthNoOverflow();
        $previousStart = $previousReference->copy()->startOfMonth();
        $previousEnd = $previousReference->copy()->endOfMonth();

        $currentMonthOrders = MarketplaceOrder::query()
            ->whereBetween('created_at', [$currentStart, $currentEnd])
            ->count();

        $previousMonthOrders = MarketplaceOrder::query()
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->count();

        $currentMonthSales = (float) SellerSettlement::query()
            ->whereBetween('eligible_at', [$currentStart, $currentEnd])
            ->sum('merchandise_amount');

        $previousMonthSales = (float) SellerSettlement::query()
            ->whereBetween('eligible_at', [$previousStart, $previousEnd])
            ->sum('merchandise_amount');

        $salesGrowth = $this->percentageChange($currentMonthSales, $previousMonthSales);
        $ordersGrowth = $this->percentageChange((float) $currentMonthOrders, (float) $previousMonthOrders);

        $currentMonthCommission = round((float) OrderCommission::query()
            ->whereBetween('earned_at', [$currentStart, $currentEnd])
            ->sum('net_commission'), 2);

        $platformCommissionAll = round((float) OrderCommission::query()
            ->sum('net_commission'), 2);

        $weeklySalesRows = SellerSettlement::query()
            ->whereBetween('eligible_at', [$currentStart, $currentEnd])
            ->get(['merchandise_amount', 'eligible_at']);

        $weeklyCommissionRows = OrderCommission::query()
            ->whereBetween('earned_at', [$currentStart, $currentEnd])
            ->get(['net_commission', 'earned_at']);

        [$weeklySales, $weeklyCommission] = $this->weeklySeries(
            $weeklySalesRows,
            $weeklyCommissionRows
        );

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
            'sales' => [
                'month_orders' => $currentMonthOrders,
                'orders_growth' => $ordersGrowth,
                'current_sales' => $currentMonthSales,
                'previous_sales' => $previousMonthSales,
                'growth' => $salesGrowth,
                'commission' => $currentMonthCommission,
                'commission_rate' => $commissionRate,
                'weekly_sales' => $weeklySales,
                'weekly_commission' => $weeklyCommission,
            ],
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

    private function percentageChange(float $current, float $previous): float
    {
        if ($previous > 0) {
            return round((($current - $previous) / $previous) * 100, 1);
        }

        return $current > 0 ? 100.0 : 0.0;
    }

    private function weeklySeries(Collection $salesRows, Collection $commissionRows): array
    {
        $sales = array_fill(0, 5, 0.0);
        $commission = array_fill(0, 5, 0.0);

        foreach ($salesRows as $row) {
            if (!$row->eligible_at) {
                continue;
            }

            $day = max(1, (int) $row->eligible_at->day);
            $bucket = min(4, intdiv($day - 1, 7));
            $sales[$bucket] += (float) ($row->merchandise_amount ?? 0);
        }

        foreach ($commissionRows as $row) {
            if (!$row->earned_at) {
                continue;
            }

            $day = max(1, (int) $row->earned_at->day);
            $bucket = min(4, intdiv($day - 1, 7));
            $commission[$bucket] += (float) ($row->net_commission ?? 0);
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
