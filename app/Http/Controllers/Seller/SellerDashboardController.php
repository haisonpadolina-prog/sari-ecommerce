<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;

use App\Models\Orders\MarketplaceOrder;
use App\Models\Platform\PlatformSetting;
use App\Models\Catalog\ProductReview;
use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\SellerProductVersion;
use App\Models\Finance\SellerSettlement;
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
                'image_path',
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
        $topSellingProduct = $this->buildTopSellingProduct($sellerId);

        return view('seller.dashboard', compact(
            'sellerAccount',
            'products',
            'productStats',
            'lowStockProducts',
            'orderStats',
            'dashboardStats',
            'salesPerformance',
            'topSellingProduct',
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

    /**
     * Sales figures come from eligible seller settlements, consistent with the
     * existing financial summary. Read in batches rather than loading every
     * historical settlement model into memory.
     */
    private function buildSalesPerformance(int $sellerId): array
    {
        $now = now();
        $byMonth = [];

        SellerSettlement::query()
            ->where('seller_account_id', $sellerId)
            ->whereNotNull('eligible_at')
            ->where('eligible_at', '<=', $now)
            ->select(['id', 'merchandise_amount', 'eligible_at'])
            ->chunkById(300, function ($settlements) use (&$byMonth): void {
                foreach ($settlements as $settlement) {
                    if (!$settlement->eligible_at) {
                        continue;
                    }

                    $key = $settlement->eligible_at->format('Y-m');
                    $byMonth[$key] ??= ['sales' => 0.0, 'orders' => 0];
                    $byMonth[$key]['sales'] += (float) ($settlement->merchandise_amount ?? 0);
                    $byMonth[$key]['orders']++;
                }
            });

        $sixMonthStart = $now->copy()->startOfMonth()->subMonths(5);
        $yearStart = $now->copy()->startOfMonth()->subMonths(11);

        return [
            'six_months' => $this->buildRollingSalesDataset($byMonth, $sixMonthStart, 6, 'Last 6 months'),
            'year' => $this->buildRollingSalesDataset($byMonth, $yearStart, 12, 'Last 12 months'),
            'all_time' => $this->buildAllTimeSalesDataset($byMonth, $now),
        ];
    }

    private function buildRollingSalesDataset(
        array $byMonth,
        Carbon $start,
        int $monthCount,
        string $label
    ): array {
        $labels = [];
        $values = [];
        $orderCount = 0;

        for ($index = 0; $index < $monthCount; $index++) {
            $month = $start->copy()->addMonths($index);
            $entry = $byMonth[$month->format('Y-m')] ?? ['sales' => 0, 'orders' => 0];
            $labels[] = $month->format('M') . ($monthCount > 12 ? " '" . $month->format('y') : '');
            $values[] = (float) $entry['sales'];
            $orderCount += (int) $entry['orders'];
        }

        return $this->finalizeSalesDataset($labels, $values, $orderCount, $label);
    }

    private function buildAllTimeSalesDataset(array $byMonth, Carbon $now): array
    {
        if ($byMonth === []) {
            return $this->buildRollingSalesDataset(
                [],
                $now->copy()->startOfMonth()->subMonths(5),
                6,
                'All time'
            );
        }

        ksort($byMonth);
        $firstMonth = Carbon::createFromFormat('!Y-m', (string) array_key_first($byMonth));
        $monthCount = ((int) $now->year - (int) $firstMonth->year) * 12
            + (int) $now->month - (int) $firstMonth->month + 1;

        if ($monthCount <= 24) {
            $data = $this->buildRollingSalesDataset($byMonth, $firstMonth, $monthCount, 'All time');
            $month = $firstMonth->copy();
            $data['labels'] = array_map(function () use (&$month) {
                $label = $month->format("M 'y");
                $month->addMonth();
                return $label;
            }, $data['labels']);
            return $data;
        }

        // Keep long-lived stores readable: annual rather than microscopic monthly ticks.
        $yearTotals = [];
        foreach ($byMonth as $month => $entry) {
            $year = substr($month, 0, 4);
            $yearTotals[$year] ??= ['sales' => 0.0, 'orders' => 0];
            $yearTotals[$year]['sales'] += (float) $entry['sales'];
            $yearTotals[$year]['orders'] += (int) $entry['orders'];
        }
        ksort($yearTotals);

        return $this->finalizeSalesDataset(
            array_keys($yearTotals),
            array_map(static fn ($row) => (float) $row['sales'], array_values($yearTotals)),
            array_sum(array_column($yearTotals, 'orders')),
            'All time · yearly'
        );
    }

    /**
     * Best seller means the most units in delivered MarketplaceOrder item
     * snapshots. Counts reflect actual sold quantity, not catalog categories.
     * The ring is top-product units / all delivered product units.
     */
    private function buildTopSellingProduct(int $sellerId): array
    {
        $unitsByProduct = [];
        $allDeliveredUnits = 0;

        MarketplaceOrder::query()
            ->where('seller_account_id', $sellerId)
            ->where('status', 'delivered')
            ->select(['id', 'items'])
            ->chunkById(250, function ($orders) use (&$unitsByProduct, &$allDeliveredUnits): void {
                foreach ($orders as $order) {
                    foreach ((is_array($order->items) ? $order->items : []) as $item) {
                        if (!is_array($item)) {
                            continue;
                        }

                        $productId = (int) ($item['product_id'] ?? 0);
                        $quantity = (int) ($item['qty'] ?? 0);
                        if ($productId <= 0 || $quantity <= 0) {
                            continue;
                        }

                        $name = trim((string) ($item['name'] ?? ''));
                        $unitsByProduct[$productId] ??= [
                            'id' => $productId,
                            'name' => $name !== '' ? $name : 'Product #' . $productId,
                            'units' => 0,
                            'sales' => 0.0,
                        ];
                        $unitsByProduct[$productId]['units'] += $quantity;
                        $unitsByProduct[$productId]['sales'] += (float) ($item['seller_line_total'] ?? $item['line_total'] ?? 0);
                        $allDeliveredUnits += $quantity;
                    }
                }
            });

        if ($unitsByProduct === []) {
            return ['name' => null, 'units' => 0, 'share' => 0.0, 'all_units' => 0];
        }

        $ranking = array_values($unitsByProduct);
        usort($ranking, static function (array $a, array $b): int {
            return ($b['units'] <=> $a['units'])
                ?: ($b['sales'] <=> $a['sales'])
                ?: ($a['id'] <=> $b['id']);
        });

        $winner = $ranking[0];
        return [
            'name' => (string) $winner['name'],
            'units' => (int) $winner['units'],
            'share' => $allDeliveredUnits > 0
                ? round(((int) $winner['units'] / $allDeliveredUnits) * 100, 2)
                : 0.0,
            'all_units' => $allDeliveredUnits,
        ];
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
