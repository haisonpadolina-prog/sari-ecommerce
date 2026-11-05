<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Accounts\AdminAccount;
use App\Models\Finance\AdminCashTurnover;
use App\Services\Finance\AdminCashTurnoverService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCashTurnoverController extends Controller
{
    public function __construct(
        private readonly AdminCashTurnoverService $turnovers
    ) {
    }

    public function index(Request $request): View
    {
        $this->treasuryAdmin($request);

        $batches = AdminCashTurnover::query()
            ->with(['logistics', 'mainFacility', 'receivedByAdmin'])
            ->orderByRaw("CASE WHEN status = 'discrepancy' THEN 0 WHEN status = 'submitted' THEN 1 ELSE 2 END")
            ->latest('submitted_at')
            ->paginate(25);

        $stats = [
            'awaiting' => (float) AdminCashTurnover::query()
                ->whereIn('status', ['submitted', 'discrepancy'])->sum('expected_amount'),
            'received' => (float) AdminCashTurnover::query()
                ->where('status', 'received')->sum('confirmed_amount'),
            'discrepancies' => AdminCashTurnover::query()
                ->where('status', 'discrepancy')->count(),
        ];

        return view('admin.cash-turnovers', compact('batches', 'stats'));
    }

    public function show(Request $request, AdminCashTurnover $turnover): View
    {
        $this->treasuryAdmin($request);

        $turnover->load([
            'logistics', 'mainFacility', 'receivedByAdmin', 'counts.admin',
            'items.codRemittanceBatch.courier',
        ]);

        return view('admin.cash-turnover-show', compact('turnover'));
    }

    public function receive(Request $request, AdminCashTurnover $turnover): RedirectResponse
    {
        $admin = $this->treasuryAdmin($request);
        $validated = $request->validate([
            'confirmed_amount' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $counted = $this->turnovers->receive(
            $turnover,
            $admin,
            (float) $validated['confirmed_amount'],
            $validated['admin_note'] ?? null
        );

        if ($counted->status === 'discrepancy') {
            return redirect()
                ->route('admin.finance.cash-turnovers.show', $counted)
                ->with('warning', 'Cash discrepancy recorded. NO Seller, Platform, Rider, or Logistics wallet credit was released.');
        }

        return redirect()
            ->route('admin.finance.cash-turnovers.show', $counted)
            ->with('success', 'Full physical cash received and reconciled by SARI Admin. Stakeholder earnings were released to internal wallets once.');
    }

    private function treasuryAdmin(Request $request): AdminAccount
    {
        abort_unless($request->session()->get('is_admin'), 403, 'Admin session required.');

        $id = (int) $request->session()->get('admin_account_id', 0);
        abort_if($id <= 0, 403, 'Database-backed Admin account required.');

        $admin = AdminAccount::query()->findOrFail($id);
        abort_unless($admin->canManagePlatformSettings(), 403, 'SARI Admin treasury permission required.');

        return $admin;
    }
}
