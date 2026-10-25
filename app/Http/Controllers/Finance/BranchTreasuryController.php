<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\BranchRemittanceBatch;
use App\Services\Finance\BranchTreasuryService;
use App\Support\CurrentLogisticsAccount;
use App\Support\CurrentLogisticsHub;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchTreasuryController extends Controller
{
    public function __construct(
        private readonly BranchTreasuryService $treasury
    ) {
    }

    public function index(Request $request): View
    {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $facility = CurrentLogisticsHub::requireOperatingFacility($request);
        $mainBranch = $this->treasury->mainBranch($logistics);

        $eligibleRiderBatches = $this->treasury->eligibleRiderBatches(
            $logistics,
            $facility
        );

        $base = BranchRemittanceBatch::query()
            ->where('logistics_account_id', $logistics->id)
            ->when(
                $facility->is_main_branch,
                fn ($query) => $query->where('destination_facility_id', $facility->id),
                fn ($query) => $query->where('origin_facility_id', $facility->id)
            );

        $batches = (clone $base)
            ->with([
                'originFacility',
                'destinationFacility',
                'items.codRemittanceBatch.courier',
            ])
            ->latest('submitted_at')
            ->paginate(20);

        $stats = [
            'branch_cash_ready' => round((float) $eligibleRiderBatches->sum(
                fn ($batch): float => (float) ($batch->confirmed_amount ?? $batch->expected_amount)
            ), 2),
            'submitted' => (float) (clone $base)->where('status', 'submitted')->sum('expected_amount'),
            'verified' => (float) (clone $base)->where('status', 'verified')->sum('confirmed_amount'),
            'discrepancy' => (float) (clone $base)
                ->where('status', 'discrepancy')
                ->selectRaw('COALESCE(SUM(ABS(difference_amount)), 0) as total')
                ->value('total'),
        ];

        return view('logistics.branch-treasury', compact(
            'logistics',
            'facility',
            'mainBranch',
            'eligibleRiderBatches',
            'batches',
            'stats'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $facility = CurrentLogisticsHub::requireOperatingFacility($request);

        $validated = $request->validate([
            'rider_batch_ids' => ['required', 'array', 'min:1'],
            'rider_batch_ids.*' => ['required', 'integer', 'distinct'],
            'branch_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $batch = $this->treasury->prepareBatch(
            $logistics,
            $facility,
            $validated['rider_batch_ids'],
            'logistics_manager',
            (int) $logistics->id,
            $validated['branch_note'] ?? null
        );

        return redirect()
            ->route('logistics.branch-treasury.index')
            ->with(
                'success',
                'Branch cash batch '.$batch->batch_code.' prepared for physical turnover to '.$batch->destination_name_snapshot.'. Present its QR at the Main Branch.'
            );
    }

    public function show(
        Request $request,
        BranchRemittanceBatch $batch
    ): View {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $facility = CurrentLogisticsHub::requireOperatingFacility($request);

        abort_unless((int) $batch->logistics_account_id === (int) $logistics->id, 404);
        abort_unless((int) $batch->destination_facility_id === (int) $facility->id, 403);
        abort_unless($facility->is_main_branch, 403, 'Switch to the Main Branch to count and reconcile this cash batch.');

        $batch->load([
            'originFacility',
            'destinationFacility',
            'items.codRemittanceBatch.courier',
            'items.codRemittanceBatch.items.order',
        ]);

        return view('logistics.branch-treasury-show', compact(
            'logistics',
            'facility',
            'batch'
        ));
    }

    public function verify(
        Request $request,
        BranchRemittanceBatch $batch
    ): RedirectResponse {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $facility = CurrentLogisticsHub::requireOperatingFacility($request);

        $validated = $request->validate([
            'confirmed_amount' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'main_branch_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $verified = $this->treasury->verifyAtMainBranch(
            $batch,
            $logistics,
            $facility,
            (float) $validated['confirmed_amount'],
            'logistics_manager',
            (int) $logistics->id,
            $validated['main_branch_note'] ?? null
        );

        if ($verified->status === 'discrepancy') {
            $difference = (float) $verified->difference_amount;
            $direction = $difference < 0 ? 'short' : 'over';

            return back()->withErrors([
                'branch_remittance' => 'Main Branch cash count is '.$direction.' by ₱'.number_format(abs($difference), 2).'. No affected wallets were released.',
            ]);
        }

        return redirect()
            ->route('logistics.branch-treasury.index')
            ->with(
                'success',
                'Main Branch received and verified the full physical cash batch. Affected Seller, platform, Logistics, and Rider wallets were released.'
            );
    }
}
