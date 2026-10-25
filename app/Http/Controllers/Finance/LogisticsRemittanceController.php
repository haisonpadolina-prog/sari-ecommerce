<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\CodRemittanceBatch;
use App\Services\Finance\CodRemittanceService;
use App\Support\CurrentLogisticsAccount;
use App\Support\CurrentLogisticsHub;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogisticsRemittanceController extends Controller
{
    public function __construct(
        private readonly CodRemittanceService $remittances
    ) {
    }

    public function index(Request $request): View
    {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $facility = CurrentLogisticsHub::requireOperatingFacility($request);

        $batches = CodRemittanceBatch::query()
            ->with(['courier', 'facility', 'items.order'])
            ->where('logistics_account_id', $logistics->id)
            ->where('logistics_facility_id', $facility->id)
            ->latest('submitted_at')
            ->paginate(20);

        $base = CodRemittanceBatch::query()
            ->where('logistics_account_id', $logistics->id)
            ->where('logistics_facility_id', $facility->id);
        $stats = [
            'submitted' => (float) (clone $base)->where('status', 'submitted')->sum('expected_amount'),
            'verified' => (float) (clone $base)->where('status', 'verified')->sum('confirmed_amount'),
            'discrepancy' => (float) (clone $base)
                ->where('status', 'discrepancy')
                ->selectRaw('COALESCE(SUM(ABS(difference_amount)), 0) as total')
                ->value('total'),
            'pending_count' => (clone $base)->whereIn('status', ['submitted', 'discrepancy'])->count(),
        ];

        return view('logistics.remittances', compact('logistics', 'facility', 'batches', 'stats'));
    }

    public function show(Request $request, CodRemittanceBatch $batch): View
    {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $facility = CurrentLogisticsHub::requireOperatingFacility($request);

        abort_unless((int) $batch->logistics_account_id === (int) $logistics->id, 404);
        abort_unless((int) $batch->logistics_facility_id === (int) $facility->id, 403);

        $batch->load(['courier', 'facility', 'items.order']);

        return view('logistics.remittance-show', compact('logistics', 'facility', 'batch'));
    }

    public function verify(
        Request $request,
        CodRemittanceBatch $batch
    ): RedirectResponse {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $facility = CurrentLogisticsHub::requireOperatingFacility($request);

        abort_unless((int) $batch->logistics_account_id === (int) $logistics->id, 404);
        abort_unless((int) $batch->logistics_facility_id === (int) $facility->id, 403);

        $validated = $request->validate([
            'confirmed_amount' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'logistics_reference' => ['nullable', 'string', 'max:191'],
            'logistics_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $verified = $this->remittances->verifyBatch(
            $batch,
            $logistics,
            (float) $validated['confirmed_amount'],
            'logistics_manager',
            $logistics->id,
            $validated['logistics_reference'] ?? null,
            $validated['logistics_note'] ?? null
        );

        if ($verified->status === 'discrepancy') {
            $difference = (float) $verified->difference_amount;
            $direction = $difference < 0 ? 'short' : 'over';

            return back()->withErrors([
                'remittance' => 'Physical cash count is '.$direction.' by ₱'.number_format(abs($difference), 2).'. Rider custody remains unresolved and no central wallet release occurred.',
            ]);
        }

        $message = $facility->is_main_branch
            ? 'Physical COD cash verified at the Main Branch. Central reconciliation completed and affected wallets were released.'
            : 'Physical COD cash verified at '.$facility->name.'. Rider cash custody is cleared. The cash is now held by this branch and must be remitted to the Main Branch before affected wallets are released.';

        return redirect()
            ->route('logistics.remittances.index')
            ->with('success', $message);
    }
}
