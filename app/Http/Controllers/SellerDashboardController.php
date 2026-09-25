<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use App\Models\PlatformSetting;
use App\Models\ProductReview;
use App\Models\SellerAccount;
use App\Models\SellerProductVersion;
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

        $sellerId = (int) $sellerAccount->id;
        $now = now();

        /*
        |--------------------------------------------------------------------------
        | PRODUCT SUMMARY — SQL ONLY
        |--------------------------------------------------------------------------
        */
        $productStats = $sellerAccount->products()
            ->whereNull('archived_at')
            ->selectRaw('COUNT(id) AS total_count')
            ->selectRaw("COALESCE(SUM(CASE WHEN moderation_status = 'approved' THEN 1 ELSE 0 END), 0) AS approved_count")
            ->selectRaw('COALESCE(SUM(CASE WHEN stock <= COALESCE(low_stock_threshold, 5) THEN 1 ELSE 0 END), 0) AS low_stock_count')
            ->first();

        /*
         * Keep dashboard first paint light: seven products maximum.
         * Reviews are not loaded here because the dashboard preview does not
         * need every ProductReview row.
         */
        $products = $sellerAccount->products()
            ->whereNull('archived_at')
            ->select([
                'id',
                'seller_account_id',
                'name',
                'category',
                'brand',
                'sku',
                'price',
                'stock',
                'low_stock_threshold',
                'discount',
                'flash_sale_ends_at',
                'free_shipping',
                'moderation_status',
                'image_path',
                'has_variants',
                'created_at',
                'updated_at',
            ])
            ->with([
                'activeVariants:id,seller_product_id,sku,option_values,price,stock,image_path,is_active',
            ])
            ->latest('id')
            ->limit(7)
            ->get();

        $lowStockProducts = $sellerAccount->products()
            ->whereNull('archived_at')
            ->whereRaw('stock <= COALESCE(low_stock_threshold, 5)')
            ->latest('id')
            ->limit(3)
            ->get([
                'id',
                'name',
                'sku',
                'stock',
                'low_stock_threshold',
            ]);

        /*
        |--------------------------------------------------------------------------
        | ORDER WORKFLOW — ONE AGGREGATE QUERY
        |--------------------------------------------------------------------------
        */
        $todayStart = $now->copy()->startOfDay();
        $tomorrowStart = $todayStart->copy()->addDay();

        $orderAggregate = MarketplaceOrder::query()
            ->where('seller_account_id', $sellerId)
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END), 0) AS new_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'preparing' THEN 1 ELSE 0 END), 0) AS preparing_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'ready_for_pickup' THEN 1 ELSE 0 END), 0) AS ready_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN status IN ('courier_accepted','heading_pickup','arrived_pickup','in_transit','arrived_buyer') THEN 1 ELSE 0 END), 0) AS courier_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END), 0) AS delivered_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN status IN ('new','preparing','ready_for_pickup','courier_accepted','heading_pickup','arrived_pickup','in_transit','arrived_buyer') THEN 1 ELSE 0 END), 0) AS pending_count")
            ->selectRaw(
                "COALESCE(SUM(CASE WHEN status = 'new' AND created_at >= ? AND created_at < ? THEN 1 ELSE 0 END), 0) AS new_today",
                [$todayStart, $tomorrowStart]
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | RECENT ACTIVITY — REAL BUYER + ORDER DATA
        |--------------------------------------------------------------------------
        */
        $recentOrders = MarketplaceOrder::query()
            ->where('seller_account_id', $sellerId)
            ->with([
                'buyer:id,first_name,last_name,email',
                'socialBuyer:id,name,email,avatar_url',
            ])
            ->latest('updated_at')
            ->latest('id')
            ->limit(6)
            ->get([
                'id',
                'order_number',
                'seller_account_id',
                'buyer_account_id',
                'buyer_social_account_id',
                'buyer_name',
                'buyer_email',
                'payment_method',
                'payment_status',
                'items',
                'total',
                'status',
                'ready_at',
                'created_at',
                'updated_at',
            ]);

        $recentOrdersFeed = $recentOrders
            ->map(fn (MarketplaceOrder $order) => $this->recentOrderActivity($order))
            ->values();

        /*
        |--------------------------------------------------------------------------
        | RECENT ACTIVITY — SELLER ACTIONS ONLY
        |--------------------------------------------------------------------------
        |
        | This is intentionally separate from buyer orders. It represents
        | actions performed by the seller: adding/editing products and marking
        | orders ready for pickup.
        */
        $productCreatedActivities = $products
            ->map(function ($product) {
                return [
                    'type' => 'product_created',
                    'title' => 'Product added',
                    'description' => 'Added “' . $product->name . '” to the catalog.',
                    'meta' => $product->sku ? 'SKU ' . $product->sku : 'Product catalog',
                    'url' => route('seller.products.index'),
                    'occurred_at' => $product->created_at,
                    'occurred_at_iso' => $product->created_at?->toIso8601String(),
                    'occurred_at_human' => $product->created_at?->diffForHumans() ?? 'Just now',
                ];
            });

        $productEditedActivities = collect();

        if (Schema::hasTable('seller_product_versions')) {
            $productEditedActivities = SellerProductVersion::query()
                ->whereIn(
                    'seller_product_id',
                    $sellerAccount->products()->select('id')
                )
                ->where('snapshot_reason', 'seller_edit')
                ->latest('created_at')
                ->limit(6)
                ->get([
                    'id',
                    'seller_product_id',
                    'name',
                    'changed_fields',
                    'created_at',
                ])
                ->map(function (SellerProductVersion $version) {
                    $changedFields = $version->changed_fields;

                    if (is_string($changedFields)) {
                        $decoded = json_decode($changedFields, true);
                        $changedFields = is_array($decoded) ? $decoded : [];
                    }

                    if (!is_array($changedFields)) {
                        $changedFields = [];
                    }

                    $fieldLabels = [
                        'name' => 'name',
                        'category' => 'category',
                        'brand' => 'brand',
                        'sku' => 'SKU',
                        'price' => 'price',
                        'stock' => 'stock',
                        'discount' => 'discount',
                        'free_shipping' => 'shipping',
                        'description' => 'description',
                        'specifications' => 'specifications',
                        'has_variants' => 'variants',
                    ];

                    $labels = collect($changedFields)
                        ->map(fn ($field) => $fieldLabels[$field] ?? str_replace('_', ' ', (string) $field))
                        ->filter()
                        ->unique()
                        ->take(3)
                        ->values();

                    $meta = $labels->isNotEmpty()
                        ? 'Updated ' . $labels->implode(', ')
                        : 'Product details updated';

                    return [
                        'type' => 'product_edited',
                        'title' => 'Product updated',
                        'description' => 'Edited “' . ($version->name ?: 'Product') . '”.',
                        'meta' => $meta,
                        'url' => route('seller.products.index'),
                        'occurred_at' => $version->created_at,
                        'occurred_at_iso' => $version->created_at?->toIso8601String(),
                        'occurred_at_human' => $version->created_at?->diffForHumans() ?? 'Just now',
                    ];
                });
        }

        $orderReadyActivities = $recentOrders
            ->filter(fn (MarketplaceOrder $order) => $order->ready_at !== null)
            ->map(function (MarketplaceOrder $order) {
                return [
                    'type' => 'order_ready',
                    'title' => 'Order ready for pickup',
                    'description' => 'Marked ' . $order->order_number . ' as ready for courier pickup.',
                    'meta' => $order->buyer_name
                        ? 'Buyer: ' . $order->buyer_name
                        : 'Seller fulfillment',
                    'url' => route('seller.orders'),
                    'occurred_at' => $order->ready_at,
                    'occurred_at_iso' => $order->ready_at?->toIso8601String(),
                    'occurred_at_human' => $order->ready_at?->diffForHumans() ?? 'Just now',
                ];
            });

        $sellerActivities = collect()
            ->concat($productCreatedActivities)
            ->concat($productEditedActivities)
            ->concat($orderReadyActivities)
            ->filter(fn ($activity) => !empty($activity['occurred_at']))
            ->sortByDesc(fn ($activity) => $activity['occurred_at']->getTimestamp())
            ->take(6)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | FINANCIAL SUMMARY — ONE SETTLEMENT AGGREGATE
        |--------------------------------------------------------------------------
        */
        $currentMonthStart = $now->copy()->startOfMonth();
        $currentMonthEnd = $now->copy()->endOfMonth();

        $previousMonthReference = $now->copy()->subMonthNoOverflow();
        $previousMonthStart = $previousMonthReference->copy()->startOfMonth();
        $previousMonthEnd = $previousMonthReference->copy()->endOfMonth();

        $financialAggregate = SellerSettlement::query()
            ->where('seller_account_id', $sellerId)
            ->whereBetween('eligible_at', [$previousMonthStart, $currentMonthEnd])
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN eligible_at >= ? AND eligible_at <= ? THEN merchandise_amount ELSE 0 END), 0) AS current_merchandise',
                [$currentMonthStart, $currentMonthEnd]
            )
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN eligible_at >= ? AND eligible_at <= ? THEN merchandise_amount ELSE 0 END), 0) AS previous_merchandise',
                [$previousMonthStart, $previousMonthEnd]
            )
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN eligible_at >= ? AND eligible_at <= ? THEN platform_commission_amount ELSE 0 END), 0) AS current_commission',
                [$currentMonthStart, $currentMonthEnd]
            )
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN eligible_at >= ? AND eligible_at <= ? THEN seller_net_amount ELSE 0 END), 0) AS current_net',
                [$currentMonthStart, $currentMonthEnd]
            )
            ->first();

        $monthlySales = round((float) ($financialAggregate->current_merchandise ?? 0), 2);
        $previousMonthSales = round((float) ($financialAggregate->previous_merchandise ?? 0), 2);
        $platformCommission = round((float) ($financialAggregate->current_commission ?? 0), 2);
        $estimatedRevenue = round((float) ($financialAggregate->current_net ?? 0), 2);

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

        $platformCommissionRate = $monthlySales > 0
            ? round(($platformCommission / $monthlySales) * 100, 4)
            : (float) PlatformSetting::valueOf('commission_rate', 10);

        $feedbackCount = 0;

        if (Schema::hasTable('product_reviews')) {
            $feedbackCount = ProductReview::query()
                ->where('seller_account_id', $sellerId)
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
            'new_today' => (int) ($orderAggregate->new_today ?? 0),
            'monthly_sales' => $monthlySales,
            'previous_month_sales' => $previousMonthSales,
            'monthly_sales_change' => $monthlySalesChange,
            'monthly_sales_trend_label' => $monthlySalesTrendLabel,
            'platform_commission_rate' => $platformCommissionRate,
            'platform_commission' => $platformCommission,
            'estimated_revenue' => $estimatedRevenue,
        ];

        $salesPerformance = $this->buildSalesPerformance($sellerId);

        return view('seller.dashboard', compact(
            'sellerAccount',
            'products',
            'productStats',
            'lowStockProducts',
            'orderStats',
            'dashboardStats',
            'salesPerformance',
            'sellerActivities',
            'recentOrdersFeed'
        ));
    }

    private function recentOrderActivity(MarketplaceOrder $order): array
    {
        $items = collect(is_array($order->items) ? $order->items : [])
            ->filter(fn ($item) => is_array($item))
            ->values();

        $firstItem = $items->first() ?: [];

        $firstName = trim((string) ($firstItem['name'] ?? 'Order items'));
        $firstQty = max(1, (int) ($firstItem['qty'] ?? 1));
        $totalQty = (int) $items->sum(
            fn (array $item) => max(1, (int) ($item['qty'] ?? 1))
        );

        $itemSummary = $firstName;

        if ($firstQty > 1) {
            $itemSummary .= ' ×' . $firstQty;
        }

        if ($items->count() > 1) {
            $itemSummary .= ' +' . ($items->count() - 1) . ' more';
        }

        $registeredBuyer = $order->buyer;
        $socialBuyer = $order->socialBuyer;

        $buyerName = trim((string) ($order->buyer_name ?? ''));

        if ($buyerName === '' && $registeredBuyer) {
            $buyerName = trim(
                (string) ($registeredBuyer->first_name ?? '') . ' ' .
                (string) ($registeredBuyer->last_name ?? '')
            );
        }

        if ($buyerName === '' && $socialBuyer) {
            $buyerName = trim((string) ($socialBuyer->name ?? ''));
        }

        if ($buyerName === '') {
            $buyerName = 'SARI Buyer';
        }

        $buyerEmail = trim((string) ($order->buyer_email ?? ''));

        if ($buyerEmail === '' && $registeredBuyer) {
            $buyerEmail = trim((string) ($registeredBuyer->email ?? ''));
        }

        if ($buyerEmail === '' && $socialBuyer) {
            $buyerEmail = trim((string) ($socialBuyer->email ?? ''));
        }

        $avatarUrl = $socialBuyer
            ? trim((string) ($socialBuyer->avatar_url ?? ''))
            : '';

        if ($avatarUrl === '' && $registeredBuyer) {
            $avatarUrl = trim((string) ($registeredBuyer->getAttribute('avatar_url') ?? ''));
        }

        $nameParts = preg_split('/\s+/', trim($buyerName)) ?: [];
        $initials = collect($nameParts)
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr((string) $part, 0, 1)))
            ->implode('');

        if ($initials === '') {
            $initials = 'SB';
        }

        $firstProductId = max(0, (int) ($firstItem['product_id'] ?? 0));

        return [
            'id' => (int) $order->id,
            'order_number' => (string) $order->order_number,
            'buyer_name' => $buyerName,
            'buyer_email' => $buyerEmail,
            'buyer_avatar_url' => $avatarUrl !== '' ? $avatarUrl : null,
            'buyer_initials' => $initials,
            'item_summary' => $itemSummary,
            'item_count' => $items->count(),
            'total_quantity' => max($items->count() > 0 ? 1 : 0, $totalQty),
            'product_image_url' => $firstProductId > 0
                ? route('seller.products.image', $firstProductId)
                : null,
            'total' => (float) ($order->total ?? 0),
            'payment_method' => (string) ($order->payment_method ?? ''),
            'payment_status' => (string) ($order->payment_status ?? ''),
            'status' => (string) ($order->status ?? ''),
            'status_label' => $order->statusLabel(),
            'is_new' => $order->status === 'new',
            'activity_at' => $order->updated_at?->toIso8601String(),
            'activity_human' => $order->updated_at?->diffForHumans() ?? 'Just now',
        ];
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
