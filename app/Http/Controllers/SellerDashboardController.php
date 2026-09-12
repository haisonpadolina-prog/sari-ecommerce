<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use App\Models\PlatformSetting;
use App\Models\ProductReview;
use App\Models\SellerAccount;
use App\Models\SellerSettlement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class SellerDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->session()->get('is_seller')) {
            return redirect()->route('login');
        }

        $sellerAccount = $request->attributes->get('sellerAccount');

        if (!($sellerAccount instanceof SellerAccount)) {
            $sellerAccount = $this->resolveSeller($request);
        }

        /*
        |--------------------------------------------------------------------------
        | PRODUCT SUMMARY
        |--------------------------------------------------------------------------
        | Keep product summary work in SQL, not in the Blade template.
        */
        $productStats = $sellerAccount->products()
            ->whereNull('archived_at')
            ->selectRaw('COUNT(id) AS total_count')
            ->selectRaw("COALESCE(SUM(CASE WHEN moderation_status = 'approved' THEN 1 ELSE 0 END), 0) AS approved_count")
            ->selectRaw('COALESCE(SUM(CASE WHEN stock <= 5 THEN 1 ELSE 0 END), 0) AS low_stock_count')
            ->first();

        /*
        | Add Product + latest seven products = maximum eight dashboard slots.
        */
        $products = $sellerAccount->products()
            ->whereNull('archived_at')
            ->with([
                'activeVariants',
                'reviews:id,seller_product_id,rating',
            ])
            ->latest()
            ->limit(7)
            ->get();

        $lowStockProducts = $sellerAccount->products()
            ->whereNull('archived_at')
            ->where('stock', '<=', 5)
            ->latest()
            ->limit(3)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | REAL ORDER WORKFLOW COUNTS
        |--------------------------------------------------------------------------
        | These statuses match the existing Seller/Courier MarketplaceOrder flow.
        */
        $orderAggregate = MarketplaceOrder::query()
            ->where('seller_account_id', $sellerAccount->id)
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END), 0) AS new_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'preparing' THEN 1 ELSE 0 END), 0) AS preparing_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'ready_for_pickup' THEN 1 ELSE 0 END), 0) AS ready_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN status IN ('courier_accepted','heading_pickup','arrived_pickup','in_transit','arrived_buyer') THEN 1 ELSE 0 END), 0) AS courier_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END), 0) AS delivered_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN status IN ('new','preparing','ready_for_pickup','courier_accepted','heading_pickup','arrived_pickup','in_transit','arrived_buyer') THEN 1 ELSE 0 END), 0) AS pending_count")
            ->first();

        $newOrdersToday = MarketplaceOrder::query()
            ->where('seller_account_id', $sellerAccount->id)
            ->where('status', 'new')
            ->whereDate('created_at', now()->toDateString())
            ->count();

        /*
        |--------------------------------------------------------------------------
        | REAL MONTHLY SALES / REVENUE
        |--------------------------------------------------------------------------
        | Seller sales use delivered product subtotal only.
        | Delivery fees are not counted as seller merchandise revenue.
        */
        $now = now();

        $currentMonthStart = $now->copy()->startOfMonth();
        $currentMonthEnd = $now->copy()->endOfMonth();

        $previousMonthReference = $now->copy()->subMonthNoOverflow();
        $previousMonthStart = $previousMonthReference->copy()->startOfMonth();
        $previousMonthEnd = $previousMonthReference->copy()->endOfMonth();

        $monthlySales = (float) SellerSettlement::query()
            ->where('seller_account_id', $sellerAccount->id)
            ->whereBetween('eligible_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('merchandise_amount');

        $previousMonthSales = (float) SellerSettlement::query()
            ->where('seller_account_id', $sellerAccount->id)
            ->whereBetween('eligible_at', [$previousMonthStart, $previousMonthEnd])
            ->sum('merchandise_amount');

        $monthlySalesChange = 0.0;
        $monthlySalesTrendLabel = 'No previous-month sales';

        if ($previousMonthSales > 0) {
            $monthlySalesChange = (($monthlySales - $previousMonthSales) / $previousMonthSales) * 100;
            $direction = $monthlySalesChange > 0 ? '▲' : ($monthlySalesChange < 0 ? '▼' : '•');

            $monthlySalesTrendLabel = sprintf(
                '%s %s%% vs last month',
                $direction,
                number_format(abs($monthlySalesChange), 1)
            );
        } elseif ($monthlySales > 0) {
            $monthlySalesTrendLabel = 'First delivered sales this month';
        }

        /*
        | Financial values come from the immutable settlement ledger. The
        | current configured rate is used only as a display fallback when the
        | selected period has no completed financial transactions.
        */
        $platformCommission = round((float) SellerSettlement::query()
            ->where('seller_account_id', $sellerAccount->id)
            ->whereBetween('eligible_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('platform_commission_amount'), 2);

        $estimatedRevenue = round((float) SellerSettlement::query()
            ->where('seller_account_id', $sellerAccount->id)
            ->whereBetween('eligible_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('seller_net_amount'), 2);

        $platformCommissionRate = $monthlySales > 0
            ? round(($platformCommission / $monthlySales) * 100, 4)
            : (float) PlatformSetting::valueOf('commission_rate', 10);

        $feedbackCount = 0;

        if (Schema::hasTable('product_reviews')) {
            $feedbackCount = ProductReview::query()
                ->where('seller_account_id', $sellerAccount->id)
                ->count();
        }

        $orderStats = [
            'new' => (int) ($orderAggregate->new_count ?? 0),
            'preparing' => (int) ($orderAggregate->preparing_count ?? 0),
            'ready' => (int) ($orderAggregate->ready_count ?? 0),
            'courier' => (int) ($orderAggregate->courier_count ?? 0),
            'delivered' => (int) ($orderAggregate->delivered_count ?? 0),
            'feedback' => $feedbackCount,
        ];

        $dashboardStats = [
            'pending_orders' => (int) ($orderAggregate->pending_count ?? 0),
            'new_today' => $newOrdersToday,
            'monthly_sales' => $monthlySales,
            'previous_month_sales' => $previousMonthSales,
            'monthly_sales_change' => $monthlySalesChange,
            'monthly_sales_trend_label' => $monthlySalesTrendLabel,
            'platform_commission_rate' => $platformCommissionRate,
            'platform_commission' => $platformCommission,
            'estimated_revenue' => $estimatedRevenue,
        ];

        /*
        |--------------------------------------------------------------------------
        | REAL SALES PERFORMANCE CHART
        |--------------------------------------------------------------------------
        | One compact delivered-order query powers all three dashboard views:
        | This Month, Last Month, and This Year.
        */
        $salesPerformance = $this->buildSalesPerformance((int) $sellerAccount->id);

        return view('seller.dashboard', compact(
            'sellerAccount',
            'products',
            'productStats',
            'lowStockProducts',
            'orderStats',
            'dashboardStats',
            'salesPerformance'
        ));
    }

    private function buildSalesPerformance(int $sellerId): array
    {
        $now = now();

        $currentMonth = $now->copy()->startOfMonth();
        $lastMonth = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $yearStart = $now->copy()->startOfYear();

        $queryStart = $lastMonth->lt($yearStart)
            ? $lastMonth->copy()
            : $yearStart->copy();

        $settlements = SellerSettlement::query()
            ->where('seller_account_id', $sellerId)
            ->whereBetween('eligible_at', [
                $queryStart->copy()->startOfDay(),
                $now->copy()->endOfDay(),
            ])
            ->orderBy('eligible_at')
            ->get([
                'merchandise_amount',
                'eligible_at',
            ]);

        return [
            'month' => $this->buildMonthlySalesDataset(
                $settlements,
                $currentMonth,
                $now->copy(),
                $currentMonth->format('F Y')
            ),
            'last_month' => $this->buildMonthlySalesDataset(
                $settlements,
                $lastMonth,
                $lastMonth->copy()->endOfMonth(),
                $lastMonth->format('F Y')
            ),
            'year' => $this->buildYearSalesDataset(
                $settlements,
                (int) $now->year
            ),
        ];
    }

    private function buildMonthlySalesDataset(
        Collection $settlements,
        Carbon $monthStart,
        Carbon $periodEnd,
        string $periodLabel
    ): array {
        $start = $monthStart->copy()->startOfMonth();
        $end = $monthStart->copy()->endOfMonth();

        if ($periodEnd->lt($end)) {
            $end = $periodEnd->copy()->endOfDay();
        }

        $labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'];
        $values = array_fill(0, 5, 0.0);
        $orderCount = 0;

        foreach ($settlements as $settlement) {
            if (!$settlement->eligible_at) {
                continue;
            }

            $eligibleAt = Carbon::parse($settlement->eligible_at);

            if ($eligibleAt->lt($start) || $eligibleAt->gt($end)) {
                continue;
            }

            $bucket = min(4, intdiv(max(1, (int) $eligibleAt->day) - 1, 7));

            $values[$bucket] += (float) ($settlement->merchandise_amount ?? 0);
            $orderCount++;
        }

        return $this->finalizeSalesDataset(
            $labels,
            $values,
            $orderCount,
            $periodLabel
        );
    }

    private function buildYearSalesDataset(Collection $settlements, int $year): array
    {
        $labels = [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
        ];

        $values = array_fill(0, 12, 0.0);
        $orderCount = 0;

        foreach ($settlements as $settlement) {
            if (!$settlement->eligible_at) {
                continue;
            }

            $eligibleAt = Carbon::parse($settlement->eligible_at);

            if ((int) $eligibleAt->year !== $year) {
                continue;
            }

            $bucket = max(0, min(11, (int) $eligibleAt->month - 1));

            $values[$bucket] += (float) ($settlement->merchandise_amount ?? 0);
            $orderCount++;
        }

        return $this->finalizeSalesDataset(
            $labels,
            $values,
            $orderCount,
            (string) $year
        );
    }

    private function finalizeSalesDataset(
        array $labels,
        array $values,
        int $orderCount,
        string $periodLabel
    ): array {
        $values = array_map(
            static fn ($value) => round((float) $value, 2),
            $values
        );

        $total = round(array_sum($values), 2);
        $averageOrder = $orderCount > 0
            ? round($total / $orderCount, 2)
            : 0.0;

        $bestIndex = 0;
        $bestValue = 0.0;

        foreach ($values as $index => $value) {
            if ($value > $bestValue) {
                $bestValue = (float) $value;
                $bestIndex = (int) $index;
            }
        }

        return [
            'period_label' => $periodLabel,
            'labels' => array_values($labels),
            'values' => array_values($values),
            'total' => $total,
            'order_count' => $orderCount,
            'average_order' => $averageOrder,
            'best_label' => $bestValue > 0
                ? ($labels[$bestIndex] ?? '—')
                : '—',
            'best_value' => round($bestValue, 2),
        ];
    }

    private function resolveSeller(Request $request): SellerAccount
    {
        $sellerId = (int) $request->session()->get('seller_account_id');

        abort_if($sellerId < 1, 403, 'Seller session required.');

        $seller = SellerAccount::findOrFail($sellerId);

        $seller->refreshSuspensionStatus();

        if (!$seller->realtime_token) {
            $seller->ensureRealtimeToken();
        }

        $request->attributes->set('sellerAccount', $seller);

        return $seller;
    }
}
