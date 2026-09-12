<?php

namespace App\Http\Controllers;

use App\Models\CommissionRate;
use App\Models\MarketplaceOrder;
use App\Models\OrderCommission;
use App\Models\RiderPayoutRequest;
use App\Models\SellerAccount;
use App\Services\CommissionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminCommissionsController extends Controller
{
    public function __construct(
        private readonly CommissionService $commissions
    ) {
    }

    public function index(Request $request): View|RedirectResponse|StreamedResponse
    {
        if (!$request->session()->get('is_admin')) {
            return redirect()->route('login');
        }

        $period = $this->resolvePeriod($request);
        $filters = $this->resolveFilters($request);

        $currentRate = $this->commissions->currentRate()->loadMissing('changedByAdmin');

        $periodLedger = OrderCommission::query()
            ->whereBetween('earned_at', [$period['start'], $period['end']]);

        $previousStart = $period['start']->copy()->subDays($period['days']);
        $previousEnd = $period['start']->copy()->subSecond();

        $previousLedger = OrderCommission::query()
            ->whereBetween('earned_at', [$previousStart, $previousEnd]);

        $stats = [
            'rate' => (float) $currentRate->rate_percent,
            'delivered_orders' => (clone $periodLedger)->count(),
            'gross_sales' => (float) (clone $periodLedger)->sum('eligible_amount'),
            'commission' => (float) (clone $periodLedger)->sum('net_commission'),
        ];

        $previousStats = [
            'delivered_orders' => (clone $previousLedger)->count(),
            'gross_sales' => (float) (clone $previousLedger)->sum('eligible_amount'),
            'commission' => (float) (clone $previousLedger)->sum('net_commission'),
        ];

        $comparisons = [
            'delivered_orders' => $this->percentChange(
                (float) $stats['delivered_orders'],
                (float) $previousStats['delivered_orders']
            ),
            'gross_sales' => $this->percentChange(
                (float) $stats['gross_sales'],
                (float) $previousStats['gross_sales']
            ),
            'commission' => $this->percentChange(
                (float) $stats['commission'],
                (float) $previousStats['commission']
            ),
        ];

        $chartSeries = OrderCommission::query()
            ->selectRaw('DATE(earned_at) AS commission_date')
            ->selectRaw('SUM(net_commission) AS commission_total')
            ->whereBetween('earned_at', [$period['start'], $period['end']])
            ->groupByRaw('DATE(earned_at)')
            ->orderBy('commission_date')
            ->get()
            ->map(fn (OrderCommission $commission) => [
                'date' => (string) $commission->commission_date,
                'commission' => round((float) $commission->commission_total, 2),
            ])
            ->values();

        $filteredLedger = $this->applyLedgerFilters(
            OrderCommission::query()
                ->whereBetween('earned_at', [$period['start'], $period['end']]),
            $filters
        );

        if ($request->query('export') === 'csv') {
            return $this->exportCsv(clone $filteredLedger, $period);
        }

        $ledgerPage = (clone $filteredLedger)
            ->with([
                'order.seller',
                'seller',
                'sellerSettlement',
                'rate',
            ])
            ->orderByDesc('earned_at')
            ->orderByDesc('id')
            ->paginate(8)
            ->withQueryString();

        $ledgerPage->setCollection(
            $ledgerPage->getCollection()
                ->filter(fn (OrderCommission $commission) => $commission->order !== null)
                ->map(fn (OrderCommission $commission): array => $this->mapLedgerRow($commission))
                ->values()
        );

        $rows = $ledgerPage;

        $sellers = SellerAccount::query()
            ->whereIn(
                'id',
                OrderCommission::query()
                    ->whereBetween('earned_at', [$period['start'], $period['end']])
                    ->whereNotNull('seller_account_id')
                    ->select('seller_account_id')
            )
            ->orderBy('store_name')
            ->get(['id', 'store_name']);

        $statusOptions = OrderCommission::query()
            ->whereBetween('earned_at', [$period['start'], $period['end']])
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->filter()
            ->values();

        $topSellerRows = OrderCommission::query()
            ->select('seller_account_id')
            ->selectRaw('COUNT(*) AS order_count')
            ->selectRaw('SUM(eligible_amount) AS merchandise_total')
            ->selectRaw('SUM(net_commission) AS commission_total')
            ->whereBetween('earned_at', [$period['start'], $period['end']])
            ->groupBy('seller_account_id')
            ->orderByDesc('commission_total')
            ->limit(5)
            ->get()
            ->load('seller:id,store_name');

        $largestSellerCommission = max(
            1,
            (float) ($topSellerRows->max('commission_total') ?? 0)
        );

        $topSellers = $topSellerRows
            ->map(function (OrderCommission $row) use ($largestSellerCommission, $stats): array {
                $commission = (float) $row->commission_total;
                $periodCommission = max(0, (float) $stats['commission']);

                return [
                    'seller_id' => (int) $row->seller_account_id,
                    'name' => $row->seller?->store_name ?: 'SARI Seller',
                    'orders' => (int) $row->order_count,
                    'merchandise' => (float) $row->merchandise_total,
                    'commission' => $commission,
                    'share' => $periodCommission > 0
                        ? round(($commission / $periodCommission) * 100, 1)
                        : 0.0,
                    'bar' => round(($commission / $largestSellerCommission) * 100, 1),
                ];
            })
            ->values();

        $rawStatusCounts = MarketplaceOrder::query()
            ->whereBetween('created_at', [$period['start'], $period['end']])
            ->select('status')
            ->selectRaw('COUNT(*) AS total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusBreakdown = $this->buildStatusBreakdown($rawStatusCounts);

        $recentTransactions = OrderCommission::query()
            ->with(['order.seller', 'seller'])
            ->whereBetween('earned_at', [$period['start'], $period['end']])
            ->orderByDesc('earned_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->filter(fn (OrderCommission $commission) => $commission->order !== null)
            ->map(fn (OrderCommission $commission): array => [
                'order_number' => (string) $commission->order->order_number,
                'seller' => $commission->seller?->store_name
                    ?: $commission->order?->seller?->store_name
                    ?: 'SARI Seller',
                'commission' => (float) $commission->net_commission,
                'status' => (string) $commission->status,
                'earned_at' => $commission->earned_at,
            ])
            ->values();

        $rateHistory = CommissionRate::query()
            ->with('changedByAdmin')
            ->orderByDesc('effective_from')
            ->orderByDesc('id')
            ->limit(12)
            ->get();

        $payoutRequests = RiderPayoutRequest::query()
            ->with(['courier', 'items.earning.order'])
            ->latest()
            ->limit(50)
            ->get();

        return view('admin.commissions', compact(
            'rows',
            'stats',
            'comparisons',
            'currentRate',
            'chartSeries',
            'period',
            'filters',
            'sellers',
            'statusOptions',
            'topSellers',
            'statusBreakdown',
            'recentTransactions',
            'rateHistory',
            'payoutRequests'
        ));
    }

    public function approvePayout(Request $request, RiderPayoutRequest $payout): RedirectResponse
    {
        abort_unless($request->session()->get('is_admin'), 403);
        abort_unless($payout->status === 'pending', 422);

        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $payout->update([
            'status' => 'approved',
            'admin_note' => $validated['admin_note'] ?? null,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Rider payout approved. No external transfer is claimed until it is marked paid.');
    }

    public function markPayoutPaid(Request $request, RiderPayoutRequest $payout): RedirectResponse
    {
        abort_unless($request->session()->get('is_admin'), 403);
        abort_unless($payout->status === 'approved', 422);

        DB::transaction(function () use ($payout): void {
            $locked = RiderPayoutRequest::query()
                ->whereKey($payout->id)
                ->with('items.earning')
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless($locked->status === 'approved', 422);

            $paidAt = now();

            $locked->forceFill([
                'status' => 'paid',
                'paid_at' => $paidAt,
            ])->save();

            foreach ($locked->items as $item) {
                if (!$item->earning) {
                    continue;
                }

                $item->earning->forceFill([
                    'status' => 'paid',
                    'paid_at' => $paidAt,
                ])->save();
            }
        });

        return back()->with('success', 'Rider payout marked paid and linked rider earnings were closed as paid.');
    }

    public function rejectPayout(Request $request, RiderPayoutRequest $payout): RedirectResponse
    {
        abort_unless($request->session()->get('is_admin'), 403);
        abort_unless(in_array($payout->status, ['pending', 'approved'], true), 422);

        $validated = $request->validate([
            'admin_note' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        DB::transaction(function () use ($payout, $validated): void {
            $locked = RiderPayoutRequest::query()
                ->whereKey($payout->id)
                ->with('items.earning')
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(in_array($locked->status, ['pending', 'approved'], true), 422);

            $locked->forceFill([
                'status' => 'rejected',
                'admin_note' => $validated['admin_note'],
                'reviewed_at' => now(),
            ])->save();

            foreach ($locked->items as $item) {
                if (!$item->earning || $item->earning->status !== 'reserved') {
                    continue;
                }

                $item->earning->forceFill([
                    'status' => 'available',
                ])->save();
            }
        });

        return back()->with('success', 'Rider payout rejected. Linked rider earnings are available again.');
    }

    private function resolvePeriod(Request $request): array
    {
        $range = strtolower(trim((string) $request->query('range', '30')));
        $allowed = ['7', '30', '90', '365'];

        if ($range === 'custom') {
            try {
                $from = Carbon::createFromFormat('Y-m-d', (string) $request->query('from'));
                $to = Carbon::createFromFormat('Y-m-d', (string) $request->query('to'));

                if ($from && $to) {
                    $start = $from->startOfDay();
                    $end = $to->endOfDay();

                    if ($start->gt($end)) {
                        [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
                    }

                    return $this->periodPayload('custom', $start, $end);
                }
            } catch (\Throwable) {
                // Invalid custom dates fall back to the standard 30-day period.
            }
        }

        if (!in_array($range, $allowed, true)) {
            $range = '30';
        }

        $days = (int) $range;
        $end = now()->endOfDay();
        $start = now()->subDays($days - 1)->startOfDay();

        return $this->periodPayload($range, $start, $end);
    }

    private function periodPayload(string $range, Carbon $start, Carbon $end): array
    {
        $days = max(1, $start->copy()->startOfDay()->diffInDays($end->copy()->startOfDay()) + 1);

        return [
            'range' => $range,
            'start' => $start,
            'end' => $end,
            'from' => $start->format('Y-m-d'),
            'to' => $end->format('Y-m-d'),
            'days' => $days,
            'label' => $start->format('M j, Y') . ' - ' . $end->format('M j, Y'),
            'comparison_label' => 'vs. previous ' . $days . ($days === 1 ? ' day' : ' days'),
        ];
    }

    private function resolveFilters(Request $request): array
    {
        return [
            'search' => trim((string) $request->query('search', '')),
            'status' => strtolower(trim((string) $request->query('status', ''))),
            'seller' => max(0, (int) $request->query('seller', 0)),
        ];
    }

    private function applyLedgerFilters(Builder $query, array $filters): Builder
    {
        if ($filters['search'] !== '') {
            $search = $filters['search'];

            $query->where(function (Builder $builder) use ($search): void {
                $builder
                    ->whereHas('order', function (Builder $orders) use ($search): void {
                        $orders->where('order_number', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('seller', function (Builder $sellers) use ($search): void {
                        $sellers->where('store_name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if ($filters['seller'] > 0) {
            $query->where('seller_account_id', $filters['seller']);
        }

        return $query;
    }

    private function mapLedgerRow(OrderCommission $commission): array
    {
        $order = $commission->order;
        $seller = $commission->seller ?: $order?->seller;
        $settlement = $commission->sellerSettlement;

        return [
            'order' => $order,
            'seller' => $seller?->store_name ?: 'SARI Seller',
            'seller_id' => (int) $commission->seller_account_id,
            'subtotal' => (float) $commission->eligible_amount,
            'commission' => (float) $commission->net_commission,
            'gross_commission' => (float) $commission->gross_commission,
            'adjustment_total' => (float) $commission->adjustment_total,
            'rate' => (float) $commission->rate_percent,
            'status' => (string) $commission->status,
            'earned_at' => $commission->earned_at,
            'commission_record' => $commission,
            'seller_settlement' => $settlement,
            'seller_net' => $settlement ? (float) $settlement->seller_net_amount : null,
            'payment_status' => (string) ($order?->payment_status ?? ''),
            'delivery_fee' => (float) ($order?->delivery_fee ?? 0),
        ];
    }

    private function percentChange(float $current, float $previous): ?float
    {
        if (abs($previous) < 0.000001) {
            return abs($current) < 0.000001 ? 0.0 : null;
        }

        return round((($current - $previous) / abs($previous)) * 100, 1);
    }

    private function buildStatusBreakdown(Collection $rawStatusCounts): Collection
    {
        $count = fn (string $status): int => (int) ($rawStatusCounts[$status] ?? 0);

        $processing = collect([
            'preparing',
            'ready_for_pickup',
            'courier_accepted',
            'heading_pickup',
            'arrived_pickup',
            'in_transit',
            'arrived_buyer',
        ])->sum(fn (string $status): int => $count($status));

        $known = $count('delivered')
            + $processing
            + $count('cancelled')
            + $count('new');

        $total = (int) $rawStatusCounts->sum();
        $other = max(0, $total - $known);

        return collect([
            ['key' => 'delivered', 'label' => 'Delivered', 'count' => $count('delivered'), 'color' => '#35b968'],
            ['key' => 'processing', 'label' => 'Processing', 'count' => $processing, 'color' => '#3b82f6'],
            ['key' => 'cancelled', 'label' => 'Cancelled', 'count' => $count('cancelled'), 'color' => '#ef5b55'],
            ['key' => 'pending', 'label' => 'Pending', 'count' => $count('new'), 'color' => '#e2a40b'],
            ['key' => 'other', 'label' => 'Other', 'count' => $other, 'color' => '#94a3b8'],
        ])->filter(fn (array $row): bool => $row['count'] > 0 || $row['key'] !== 'other')->values();
    }

    private function exportCsv(Builder $query, array $period): StreamedResponse
    {
        $fileName = sprintf(
            'sari-commission-ledger-%s-to-%s.csv',
            $period['from'],
            $period['to']
        );

        return response()->streamDownload(function () use ($query): void {
            $output = fopen('php://output', 'w');

            fputcsv($output, [
                'Order Number',
                'Seller',
                'Eligible Amount',
                'Rate Percent',
                'Gross Commission',
                'Adjustment Total',
                'Net Commission',
                'Commission Status',
                'Payment Status',
                'Delivered At',
                'Seller Net Payable',
            ]);

            $query
                ->with(['order.seller', 'seller', 'sellerSettlement'])
                ->orderByDesc('earned_at')
                ->orderByDesc('id')
                ->get()
                ->each(function (OrderCommission $commission) use ($output): void {
                    if (!$commission->order) {
                        return;
                    }

                    $sellerName = $commission->seller?->store_name
                        ?: $commission->order?->seller?->store_name
                        ?: 'SARI Seller';

                    fputcsv($output, [
                        $this->csvSafe((string) $commission->order->order_number),
                        $this->csvSafe($sellerName),
                        number_format((float) $commission->eligible_amount, 2, '.', ''),
                        number_format((float) $commission->rate_percent, 4, '.', ''),
                        number_format((float) $commission->gross_commission, 2, '.', ''),
                        number_format((float) $commission->adjustment_total, 2, '.', ''),
                        number_format((float) $commission->net_commission, 2, '.', ''),
                        $this->csvSafe((string) $commission->status),
                        $this->csvSafe((string) $commission->order->payment_status),
                        $commission->order->delivered_at?->format('Y-m-d H:i:s') ?: '',
                        $commission->sellerSettlement
                            ? number_format((float) $commission->sellerSettlement->seller_net_amount, 2, '.', '')
                            : '',
                    ]);
                });

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    private function csvSafe(string $value): string
    {
        $trimmed = ltrim($value);

        if ($trimmed !== '' && in_array($trimmed[0], ['=', '+', '-', '@'], true)) {
            return "'" . $value;
        }

        return $value;
    }
}
