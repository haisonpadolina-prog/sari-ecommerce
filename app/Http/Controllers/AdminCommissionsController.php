<?php

namespace App\Http\Controllers;

use App\Models\OrderCommission;
use App\Models\RiderPayoutRequest;
use App\Services\CommissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminCommissionsController extends Controller
{
    public function __construct(
        private readonly CommissionService $commissions
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_admin')) {
            return redirect()->route('login');
        }

        $rate = (float) $this->commissions->currentRate()->rate_percent;

        $ledger = OrderCommission::query()
            ->with(['order.seller', 'order.sellerSettlement'])
            ->latest('earned_at')
            ->get();

        $rows = $ledger
            ->filter(fn (OrderCommission $commission) => $commission->order !== null)
            ->map(function (OrderCommission $commission): array {
                return [
                    'order' => $commission->order,
                    'seller' => $commission->order?->seller?->store_name ?: 'SARI Seller',
                    'subtotal' => (float) $commission->eligible_amount,
                    'commission' => (float) $commission->net_commission,
                    'rate' => (float) $commission->rate_percent,
                    'commission_record' => $commission,
                    'seller_settlement' => $commission->order?->sellerSettlement,
                ];
            })
            ->values();

        $stats = [
            'rate' => $rate,
            'delivered_orders' => $rows->count(),
            'gross_sales' => (float) $ledger->sum(fn (OrderCommission $row) => (float) $row->eligible_amount),
            'commission' => (float) $ledger->sum(fn (OrderCommission $row) => (float) $row->net_commission),
        ];

        $payoutRequests = RiderPayoutRequest::query()
            ->with(['courier', 'items.earning.order'])
            ->latest()
            ->limit(50)
            ->get();

        return view('admin.commissions', compact('rows', 'stats', 'payoutRequests'));
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

            // A rejected payout does not erase the earned delivery fee.
            // It simply makes those exact ledger rows available for a later request.
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
}
