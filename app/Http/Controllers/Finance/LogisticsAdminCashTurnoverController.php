<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\AdminCashTurnover;
use App\Services\Finance\AdminCashTurnoverService;
use App\Support\CurrentLogisticsAccount;
use App\Support\CurrentLogisticsHub;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogisticsAdminCashTurnoverController extends Controller
{
    public function __construct(
        private readonly AdminCashTurnoverService $turnovers
    ) {
    }

    public function index(Request $request): View
    {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $facility = CurrentLogisticsHub::requireOperatingFacility($request);
        abort_unless($facility->is_main_branch && (int) $facility->logistics_account_id === (int) $logistics->id, 403);

        $eligibleBatches = $this->turnovers->eligibleBatches($logistics, $facility);
        $batches = AdminCashTurnover::query()
            ->where('logistics_account_id', $logistics->id)
            ->with(['receivedByAdmin'])
            ->latest('submitted_at')
            ->paginate(20);

        $stats = [
            'ready' => (float) $eligibleBatches->sum('confirmed_amount'),
            'pending' => (float) AdminCashTurnover::query()
                ->where('logistics_account_id', $logistics->id)
                ->whereIn('status', ['submitted', 'discrepancy'])
                ->sum('expected_amount'),
            'received' => (float) AdminCashTurnover::query()
                ->where('logistics_account_id', $logistics->id)
                ->where('status', 'received')
                ->sum('confirmed_amount'),
        ];

        return view('logistics.admin-cash-turnovers', compact(
            'logistics', 'facility', 'eligibleBatches', 'batches', 'stats'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $facility = CurrentLogisticsHub::requireOperatingFacility($request);
        abort_unless($facility->is_main_branch && (int) $facility->logistics_account_id === (int) $logistics->id, 403);

        $validated = $request->validate([
            'rider_batch_ids' => ['required', 'array', 'min:1', 'max:250'],
            'rider_batch_ids.*' => ['required', 'integer', 'distinct'],
            'logistics_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $turnover = $this->turnovers->prepare(
            $logistics,
            $facility,
            $validated['rider_batch_ids'],
            $validated['logistics_note'] ?? null
        );

        return redirect()
            ->route('logistics.admin-turnovers.show', $turnover)
            ->with('success', 'Turnover '.$turnover->turnover_code.' prepared. Bring the FULL cash amount to SARI Admin for independent counting. Wallet earnings remain pending.');
    }

    public function show(Request $request, AdminCashTurnover $turnover): View
    {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $facility = CurrentLogisticsHub::requireOperatingFacility($request);
        abort_unless($facility->is_main_branch && (int) $facility->logistics_account_id === (int) $logistics->id, 403);
        abort_unless((int) $turnover->logistics_account_id === (int) $logistics->id, 404);

        $turnover->load([
            'logistics', 'mainFacility', 'receivedByAdmin', 'counts.admin',
            'items.codRemittanceBatch.courier',
        ]);

        return view('logistics.admin-cash-turnover-show', compact('logistics', 'facility', 'turnover'));
    }
}
