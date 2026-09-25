<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use App\Models\SellerAccount;
use App\Models\SellerSettlement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SellerReportController extends Controller
{
    public function index(Request $request)
    {
        $seller = $this->resolveSeller($request);
        [$from, $to, $type] = $this->validatedFilters($request);

        $rangeStart = $from->copy()->startOfDay();
        $rangeEnd = $to->copy()->endOfDay();

        /*
        | Operational counts use order creation date.
        | Financial values use settlement eligibility (delivered + paid) date.
        | Keeping the two concepts separate prevents revenue being recognized
        | just because an order was placed.
        */
        $orderAggregate = MarketplaceOrder::query()
            ->where('seller_account_id', $seller->id)
            ->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->selectRaw('COUNT(*) AS total_orders')
            ->selectRaw("SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) AS completed_orders")
            ->selectRaw("SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_orders")
            ->selectRaw("SUM(CASE WHEN status NOT IN ('delivered','cancelled') THEN 1 ELSE 0 END) AS active_orders")
            ->first();

        $totalOrders = (int) ($orderAggregate->total_orders ?? 0);
        $completedOrders = (int) ($orderAggregate->completed_orders ?? 0);
        $cancelledOrders = (int) ($orderAggregate->cancelled_orders ?? 0);
        $activeOrders = (int) ($orderAggregate->active_orders ?? 0);

        // One aggregate query instead of five separate SUM/COUNT queries.
        $financialAggregate = SellerSettlement::query()
            ->where('seller_account_id', $seller->id)
            ->whereBetween('eligible_at', [$rangeStart, $rangeEnd])
            ->selectRaw('COUNT(*) AS completed_count')
            ->selectRaw('COALESCE(SUM(merchandise_amount), 0) AS gross_sales')
            ->selectRaw('COALESCE(SUM(platform_commission_amount), 0) AS platform_commission')
            ->selectRaw('COALESCE(SUM(withholding_tax_amount), 0) AS withholding_tax')
            ->selectRaw('COALESCE(SUM(seller_net_amount), 0) AS net_revenue')
            ->first();

        $grossSales = round((float) ($financialAggregate->gross_sales ?? 0), 2);
        $platformCommission = round((float) ($financialAggregate->platform_commission ?? 0), 2);
        $withholdingTax = round((float) ($financialAggregate->withholding_tax ?? 0), 2);
        $netRevenue = round((float) ($financialAggregate->net_revenue ?? 0), 2);
        $financialCompletedCount = (int) ($financialAggregate->completed_count ?? 0);

        $effectiveCommissionRate = $grossSales > 0
            ? round(($platformCommission / $grossSales) * 100, 4)
            : 0.0;

        $completionRate = $totalOrders > 0
            ? round(($completedOrders / $totalOrders) * 100, 1)
            : 0.0;

        $cancellationRate = $totalOrders > 0
            ? round(($cancelledOrders / $totalOrders) * 100, 1)
            : 0.0;

        $averageOrderValue = $financialCompletedCount > 0
            ? round($grossSales / $financialCompletedCount, 2)
            : 0.0;

        [$previousFrom, $previousTo] = $this->previousPeriod($from, $to);

        $previousGrossSales = (float) SellerSettlement::query()
            ->where('seller_account_id', $seller->id)
            ->whereBetween('eligible_at', [
                $previousFrom->copy()->startOfDay(),
                $previousTo->copy()->endOfDay(),
            ])
            ->sum('merchandise_amount');

        $salesChange = $previousGrossSales > 0
            ? round((($grossSales - $previousGrossSales) / $previousGrossSales) * 100, 1)
            : ($grossSales > 0 ? null : 0.0);

        $deliveredOrders = MarketplaceOrder::query()
            ->with(['sellerSettlement', 'commission'])
            ->where('seller_account_id', $seller->id)
            ->where('status', 'delivered')
            ->where('payment_status', 'paid')
            ->whereBetween('delivered_at', [$rangeStart, $rangeEnd])
            ->orderBy('delivered_at')
            ->get([
                'id',
                'order_number',
                'seller_account_id',
                'buyer_name',
                'status',
                'payment_method',
                'payment_status',
                'subtotal',
                'items',
                'delivered_at',
                'created_at',
            ]);

        $topProducts = $this->topProducts($deliveredOrders);
        $chart = $this->buildChart($deliveredOrders, $from, $to);
        $customerRating = $this->sellerRating((int) $seller->id);

        $summary = [
            'gross_sales' => $grossSales,
            'commission_rate' => $effectiveCommissionRate,
            'platform_commission' => $platformCommission,
            'withholding_tax' => $withholdingTax,
            'net_revenue' => $netRevenue,
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'cancelled_orders' => $cancelledOrders,
            'active_orders' => $activeOrders,
            'completion_rate' => $completionRate,
            'cancellation_rate' => $cancellationRate,
            'average_order_value' => $averageOrderValue,
            'sales_change' => $salesChange,
            'customer_rating' => $customerRating,
        ];

        $transactions = $deliveredOrders
            ->sortByDesc('delivered_at')
            ->take(25)
            ->map(function (MarketplaceOrder $order): array {
                $settlement = $order->sellerSettlement;
                $commission = $order->commission;

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'date' => $order->delivered_at,
                    'status' => (string) $order->status,
                    'status_label' => $order->statusLabel(),
                    'buyer_name' => (string) ($order->buyer_name ?? 'Buyer'),
                    'gross' => (float) ($settlement?->merchandise_amount ?? $order->subtotal ?? 0),
                    'commission' => (float) ($settlement?->platform_commission_amount ?? $commission?->net_commission ?? 0),
                    'net' => (float) ($settlement?->seller_net_amount ?? 0),
                    'payment' => strtoupper((string) ($order->payment_method ?: 'COD')),
                    'payment_status' => strtoupper((string) $order->payment_status),
                ];
            })
            ->values();

        return view('seller.reports', [
            'seller' => $seller,
            'from' => $from,
            'to' => $to,
            'reportType' => $type,
            'summary' => $summary,
            'chart' => $chart,
            'topProducts' => $topProducts,
            'transactions' => $transactions,
            'previousFrom' => $previousFrom,
            'previousTo' => $previousTo,
        ]);
    }

    public function downloadCsv(Request $request): StreamedResponse
    {
        $seller = $this->resolveSeller($request);
        [$from, $to, $type] = $this->validatedFilters($request);

        $orders = MarketplaceOrder::query()
            ->with(['sellerSettlement', 'commission'])
            ->where('seller_account_id', $seller->id)
            ->where('status', 'delivered')
            ->where('payment_status', 'paid')
            ->whereBetween('delivered_at', [
                $from->copy()->startOfDay(),
                $to->copy()->endOfDay(),
            ])
            ->latest('delivered_at')
            ->get();

        $grossSales = round((float) $orders->sum(
            fn ($order) => (float) ($order->sellerSettlement?->merchandise_amount ?? $order->subtotal ?? 0)
        ), 2);
        $commission = round((float) $orders->sum(
            fn ($order) => (float) ($order->sellerSettlement?->platform_commission_amount ?? $order->commission?->net_commission ?? 0)
        ), 2);
        $withholding = round((float) $orders->sum(
            fn ($order) => (float) ($order->sellerSettlement?->withholding_tax_amount ?? 0)
        ), 2);
        $net = round((float) $orders->sum(
            fn ($order) => (float) ($order->sellerSettlement?->seller_net_amount ?? 0)
        ), 2);
        $effectiveRate = $grossSales > 0 ? round(($commission / $grossSales) * 100, 4) : 0.0;

        $filename = sprintf(
            'sari-seller-%s-%s-to-%s.csv',
            $type,
            $from->format('Y-m-d'),
            $to->format('Y-m-d')
        );

        return response()->streamDownload(function () use (
            $orders,
            $type,
            $from,
            $to,
            $grossSales,
            $commission,
            $withholding,
            $net,
            $effectiveRate
        ) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['SARI Seller Financial Report']);
            fputcsv($handle, ['Report Type', ucwords(str_replace('_', ' ', $type))]);
            fputcsv($handle, ['Financial Recognition Period', $from->format('M d, Y') . ' - ' . $to->format('M d, Y')]);
            fputcsv($handle, ['Basis', 'Delivered + paid orders, recognized by delivered_at']);
            fputcsv($handle, ['Gross Merchandise Sales', number_format($grossSales, 2, '.', '')]);
            fputcsv($handle, ['Effective Platform Commission Rate', number_format($effectiveRate, 4, '.', '') . '%']);
            fputcsv($handle, ['Platform Commission', number_format($commission, 2, '.', '')]);
            fputcsv($handle, ['Withholding Tax Recorded', number_format($withholding, 2, '.', '')]);
            fputcsv($handle, ['Seller Net Payable', number_format($net, 2, '.', '')]);
            fputcsv($handle, []);

            if ($type === 'products') {
                fputcsv($handle, ['Product', 'Quantity Sold', 'Sales']);

                foreach ($this->topProducts($orders, 100) as $product) {
                    fputcsv($handle, [
                        $product['name'],
                        $product['quantity'],
                        number_format($product['sales'], 2, '.', ''),
                    ]);
                }
            } else {
                fputcsv($handle, [
                    'Order Number',
                    'Delivered At',
                    'Buyer',
                    'Gross Merchandise',
                    'Applied Rate',
                    'Platform Commission',
                    'Withholding Tax',
                    'Seller Net Payable',
                    'Payment Method',
                    'Payment Status',
                    'Settlement Status',
                ]);

                foreach ($orders as $order) {
                    $settlement = $order->sellerSettlement;
                    $rowCommission = (float) ($settlement?->platform_commission_amount ?? $order->commission?->net_commission ?? 0);
                    $rowGross = (float) ($settlement?->merchandise_amount ?? $order->subtotal ?? 0);
                    $rowRate = (float) ($order->commission?->rate_percent ?? ($rowGross > 0 ? ($rowCommission / $rowGross) * 100 : 0));

                    fputcsv($handle, [
                        $order->order_number,
                        $order->delivered_at?->format('Y-m-d H:i:s'),
                        $order->buyer_name,
                        number_format($rowGross, 2, '.', ''),
                        number_format($rowRate, 4, '.', '') . '%',
                        number_format($rowCommission, 2, '.', ''),
                        number_format((float) ($settlement?->withholding_tax_amount ?? 0), 2, '.', ''),
                        number_format((float) ($settlement?->seller_net_amount ?? 0), 2, '.', ''),
                        strtoupper((string) $order->payment_method),
                        strtoupper((string) $order->payment_status),
                        $settlement?->status ?? 'not_recorded',
                    ]);
                }
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function resolveSeller(Request $request): SellerAccount
    {
        abort_unless($request->session()->get('is_seller'), 403, 'Seller session required.');

        $sellerId = (int) $request->session()->get('seller_account_id');
        abort_if($sellerId < 1, 403, 'Seller session required.');

        $seller = $request->attributes->get('sellerAccount');

        if (!($seller instanceof SellerAccount)) {
            $seller = SellerAccount::findOrFail($sellerId);
            $request->attributes->set('sellerAccount', $seller);
        }

        return $seller;
    }

    private function validatedFilters(Request $request): array
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'type' => ['nullable', 'in:all,sales,financial,orders,products'],
        ]);

        $to = isset($validated['to'])
            ? Carbon::parse($validated['to'])->startOfDay()
            : now()->startOfDay();

        $from = isset($validated['from'])
            ? Carbon::parse($validated['from'])->startOfDay()
            : $to->copy()->subDays(29);

        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        if ($from->diffInDays($to) > 730) {
            $from = $to->copy()->subDays(730);
        }

        return [
            $from,
            $to,
            (string) ($validated['type'] ?? 'all'),
        ];
    }

    private function previousPeriod(Carbon $from, Carbon $to): array
    {
        $days = $from->diffInDays($to) + 1;
        $previousTo = $from->copy()->subDay();
        $previousFrom = $previousTo->copy()->subDays($days - 1);

        return [$previousFrom, $previousTo];
    }

    private function topProducts(Collection $deliveredOrders, int $limit = 5): array
    {
        $products = [];

        foreach ($deliveredOrders as $order) {
            $items = $this->normalizeItems($order->items ?? []);

            foreach ($items as $item) {
                $name = trim((string) ($item['name'] ?? 'Product'));
                $key = (string) ($item['product_id'] ?? strtolower($name));

                if (!isset($products[$key])) {
                    $products[$key] = [
                        'name' => $name ?: 'Product',
                        'quantity' => 0,
                        'sales' => 0.0,
                    ];
                }

                $qty = max(1, (int) ($item['qty'] ?? 1));
                $unit = (float) ($item['price'] ?? 0);
                $line = (float) ($item['line_total'] ?? ($unit * $qty));

                $products[$key]['quantity'] += $qty;
                $products[$key]['sales'] += $line;
            }
        }

        usort($products, fn ($a, $b) => $b['sales'] <=> $a['sales']);

        return array_values(array_slice($products, 0, $limit));
    }

    private function normalizeItems(mixed $items): array
    {
        if (is_array($items)) {
            return $items;
        }

        if (is_string($items)) {
            $decoded = json_decode($items, true);
            return is_array($decoded) ? $decoded : [];
        }

        if ($items instanceof Collection) {
            return $items->all();
        }

        return [];
    }

    private function buildChart(Collection $deliveredOrders, Carbon $from, Carbon $to): array
    {
        $days = $from->diffInDays($to) + 1;

        if ($days <= 14) {
            $mode = 'day';
            $cursor = $from->copy();
            $periods = [];

            while ($cursor->lte($to)) {
                $key = $cursor->format('Y-m-d');
                $periods[$key] = [
                    'label' => $cursor->format('M j'),
                    'sales' => 0.0,
                    'net' => 0.0,
                ];
                $cursor->addDay();
            }

            foreach ($deliveredOrders as $order) {
                $key = $order->delivered_at?->format('Y-m-d');
                $this->addOrderToChartPeriod($periods, $key, $order);
            }
        } elseif ($days <= 120) {
            $mode = 'week';
            $periods = [];
            $cursor = $from->copy()->startOfWeek();

            while ($cursor->lte($to)) {
                $key = $cursor->format('o-W');
                $periods[$key] = [
                    'label' => $cursor->format('M j'),
                    'sales' => 0.0,
                    'net' => 0.0,
                ];
                $cursor->addWeek();
            }

            foreach ($deliveredOrders as $order) {
                $key = $order->delivered_at?->copy()->startOfWeek()->format('o-W');
                $this->addOrderToChartPeriod($periods, $key, $order);
            }
        } else {
            $mode = 'month';
            $periods = [];
            $cursor = $from->copy()->startOfMonth();

            while ($cursor->lte($to)) {
                $key = $cursor->format('Y-m');
                $periods[$key] = [
                    'label' => $cursor->format('M Y'),
                    'sales' => 0.0,
                    'net' => 0.0,
                ];
                $cursor->addMonth();
            }

            foreach ($deliveredOrders as $order) {
                $key = $order->delivered_at?->format('Y-m');
                $this->addOrderToChartPeriod($periods, $key, $order);
            }
        }

        return [
            'mode' => $mode,
            'labels' => array_values(array_column($periods, 'label')),
            'sales' => array_values(array_map(
                fn ($row) => round((float) $row['sales'], 2),
                $periods
            )),
            'net' => array_values(array_map(
                fn ($row) => round((float) $row['net'], 2),
                $periods
            )),
        ];
    }

    private function addOrderToChartPeriod(array &$periods, ?string $key, MarketplaceOrder $order): void
    {
        if (!$key || !isset($periods[$key])) {
            return;
        }

        $settlement = $order->sellerSettlement;
        $periods[$key]['sales'] += (float) ($settlement?->merchandise_amount ?? $order->subtotal ?? 0);
        $periods[$key]['net'] += (float) ($settlement?->seller_net_amount ?? 0);
    }

    private function sellerRating(int $sellerId): ?float
    {
        if (!Schema::hasTable('product_reviews') || !Schema::hasTable('seller_products')) {
            return null;
        }

        $rating = DB::table('product_reviews')
            ->join(
                'seller_products',
                'seller_products.id',
                '=',
                'product_reviews.seller_product_id'
            )
            ->where('seller_products.seller_account_id', $sellerId)
            ->avg('product_reviews.rating');

        return $rating !== null ? round((float) $rating, 1) : null;
    }
}
