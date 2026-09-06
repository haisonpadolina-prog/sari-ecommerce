<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use App\Models\SellerAccount;
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
        |--------------------------------------------------------------------------
        | FAST REPORT AGGREGATES
        |--------------------------------------------------------------------------
        | Counts are calculated by MySQL instead of loading every order model
        | into PHP. Only the rows actually needed for the chart/products/table
        | are fetched below.
        */
        $aggregate = MarketplaceOrder::query()
            ->where('seller_account_id', $seller->id)
            ->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->selectRaw('COUNT(*) AS total_orders')
            ->selectRaw("SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) AS completed_orders")
            ->selectRaw("SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_orders")
            ->selectRaw("SUM(CASE WHEN status NOT IN ('delivered','cancelled') THEN 1 ELSE 0 END) AS active_orders")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'delivered' THEN subtotal ELSE 0 END), 0) AS gross_sales")
            ->first();

        $grossSales = round((float) ($aggregate->gross_sales ?? 0), 2);
        $totalOrders = (int) ($aggregate->total_orders ?? 0);
        $completedOrders = (int) ($aggregate->completed_orders ?? 0);
        $cancelledOrders = (int) ($aggregate->cancelled_orders ?? 0);
        $activeOrders = (int) ($aggregate->active_orders ?? 0);

        $commissionRate = 10.0;
        $platformCommission = round($grossSales * ($commissionRate / 100), 2);
        $netRevenue = round($grossSales - $platformCommission, 2);

        $completionRate = $totalOrders > 0
            ? round(($completedOrders / $totalOrders) * 100, 1)
            : 0.0;

        $cancellationRate = $totalOrders > 0
            ? round(($cancelledOrders / $totalOrders) * 100, 1)
            : 0.0;

        $averageOrderValue = $completedOrders > 0
            ? round($grossSales / $completedOrders, 2)
            : 0.0;

        [$previousFrom, $previousTo] = $this->previousPeriod($from, $to);

        $previousGrossSales = (float) MarketplaceOrder::query()
            ->where('seller_account_id', $seller->id)
            ->where('status', 'delivered')
            ->whereBetween('created_at', [
                $previousFrom->copy()->startOfDay(),
                $previousTo->copy()->endOfDay(),
            ])
            ->sum('subtotal');

        $salesChange = $previousGrossSales > 0
            ? round((($grossSales - $previousGrossSales) / $previousGrossSales) * 100, 1)
            : ($grossSales > 0 ? 100.0 : 0.0);

        /*
        | Delivered rows are the only rows needed for chart/product sales.
        | Select only four columns to keep report rendering lightweight.
        */
        $deliveredOrders = MarketplaceOrder::query()
            ->where('seller_account_id', $seller->id)
            ->where('status', 'delivered')
            ->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->orderBy('created_at')
            ->get([
                'id',
                'subtotal',
                'items',
                'created_at',
            ]);

        $topProducts = $this->topProducts($deliveredOrders);
        $chart = $this->buildChart($deliveredOrders, $from, $to);
        $customerRating = $this->sellerRating((int) $seller->id);

        $summary = [
            'gross_sales' => $grossSales,
            'commission_rate' => $commissionRate,
            'platform_commission' => $platformCommission,
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

        /*
        | The visible transaction table only needs the newest 25 rows.
        | Do not hydrate the entire report period just to display this table.
        */
        $transactions = MarketplaceOrder::query()
            ->where('seller_account_id', $seller->id)
            ->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->latest('created_at')
            ->limit(25)
            ->get([
                'id',
                'order_number',
                'buyer_name',
                'status',
                'subtotal',
                'payment_status',
                'created_at',
            ])
            ->map(function ($order) use ($commissionRate) {
                $eligibleForRevenue = $order->status === 'delivered';
                $gross = $eligibleForRevenue
                    ? (float) ($order->subtotal ?? 0)
                    : 0.0;

                $commission = round($gross * ($commissionRate / 100), 2);
                $net = round($gross - $commission, 2);

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'date' => $order->created_at,
                    'status' => (string) $order->status,
                    'status_label' => method_exists($order, 'statusLabel')
                        ? $order->statusLabel()
                        : ucwords(str_replace('_', ' ', (string) $order->status)),
                    'buyer_name' => (string) ($order->buyer_name ?? 'Buyer'),
                    'gross' => $gross,
                    'commission' => $commission,
                    'net' => $net,
                    'payment' => 'COD',
                    'payment_status' => strtoupper((string) ($order->payment_status ?? (
                        $order->status === 'delivered' ? 'PAID' : 'PENDING'
                    ))),
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
            ->where('seller_account_id', $seller->id)
            ->whereBetween('created_at', [
                $from->copy()->startOfDay(),
                $to->copy()->endOfDay(),
            ])
            ->latest('created_at')
            ->get();

        $deliveredOrders = $orders->where('status', 'delivered')->values();
        $commissionRate = 10.0;

        $grossSales = round(
            $deliveredOrders->sum(fn ($order) => (float) ($order->subtotal ?? 0)),
            2
        );
        $commission = round($grossSales * ($commissionRate / 100), 2);
        $net = round($grossSales - $commission, 2);

        $filename = sprintf(
            'sari-seller-%s-%s-to-%s.csv',
            $type,
            $from->format('Y-m-d'),
            $to->format('Y-m-d')
        );

        return response()->streamDownload(function () use (
            $orders,
            $deliveredOrders,
            $type,
            $from,
            $to,
            $grossSales,
            $commission,
            $net,
            $commissionRate
        ) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility.
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['SARI Seller Report']);
            fputcsv($handle, ['Report Type', ucwords(str_replace('_', ' ', $type))]);
            fputcsv($handle, ['Period', $from->format('M d, Y') . ' - ' . $to->format('M d, Y')]);
            fputcsv($handle, ['Gross Sales', number_format($grossSales, 2, '.', '')]);
            fputcsv($handle, ['Platform Commission (' . $commissionRate . '%)', number_format($commission, 2, '.', '')]);
            fputcsv($handle, ['Net Revenue', number_format($net, 2, '.', '')]);
            fputcsv($handle, []);

            if ($type === 'products') {
                fputcsv($handle, ['Product', 'Quantity Sold', 'Sales']);

                foreach ($this->topProducts($deliveredOrders, 100) as $product) {
                    fputcsv($handle, [
                        $product['name'],
                        $product['quantity'],
                        number_format($product['sales'], 2, '.', ''),
                    ]);
                }
            } else {
                fputcsv($handle, [
                    'Order Number',
                    'Date',
                    'Buyer',
                    'Status',
                    'Gross Sales',
                    'Commission',
                    'Net Revenue',
                    'Payment',
                ]);

                foreach ($orders as $order) {
                    $isDelivered = $order->status === 'delivered';
                    $gross = $isDelivered ? (float) ($order->subtotal ?? 0) : 0.0;
                    $rowCommission = round($gross * ($commissionRate / 100), 2);

                    fputcsv($handle, [
                        $order->order_number,
                        $order->created_at?->format('Y-m-d H:i:s'),
                        $order->buyer_name,
                        ucwords(str_replace('_', ' ', (string) $order->status)),
                        number_format($gross, 2, '.', ''),
                        number_format($rowCommission, 2, '.', ''),
                        number_format($gross - $rowCommission, 2, '.', ''),
                        'COD',
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

        // Keep the report bounded for fast, predictable queries.
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
                $key = $order->created_at?->format('Y-m-d');

                if ($key && isset($periods[$key])) {
                    $sales = (float) ($order->subtotal ?? 0);
                    $periods[$key]['sales'] += $sales;
                    $periods[$key]['net'] += $sales * .90;
                }
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
                $key = $order->created_at?->copy()->startOfWeek()->format('o-W');

                if ($key && isset($periods[$key])) {
                    $sales = (float) ($order->subtotal ?? 0);
                    $periods[$key]['sales'] += $sales;
                    $periods[$key]['net'] += $sales * .90;
                }
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
                $key = $order->created_at?->format('Y-m');

                if ($key && isset($periods[$key])) {
                    $sales = (float) ($order->subtotal ?? 0);
                    $periods[$key]['sales'] += $sales;
                    $periods[$key]['net'] += $sales * .90;
                }
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

    private function sellerRating(int $sellerId): ?float
    {
        if (
            !Schema::hasTable('product_reviews')
            || !Schema::hasTable('seller_products')
        ) {
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

        return $rating !== null
            ? round((float) $rating, 1)
            : null;
    }
}
