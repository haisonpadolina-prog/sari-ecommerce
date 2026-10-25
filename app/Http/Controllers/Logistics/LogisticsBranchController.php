<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Delivery\LogisticsFacility;
use App\Services\Logistics\LogisticsActivityLogger;
use App\Support\CurrentLogisticsAccount;
use App\Support\CurrentLogisticsHub;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogisticsBranchController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        if (CurrentLogisticsHub::isHubOperator($request)) {
            return redirect()->route('logistics.dashboard');
        }

        $facilities = LogisticsFacility::query()
            ->where('logistics_account_id', $logistics->id)
            ->where('is_active', true)
            ->orderByDesc('is_main_branch')
            ->orderBy('municipality_name')
            ->orderBy('name')
            ->get();

        $selectedFacility = CurrentLogisticsHub::operatingFacility($request);

        return view('logistics.branch-select', compact(
            'logistics',
            'facilities',
            'selectedFacility'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $logistics = CurrentLogisticsAccount::resolve($request);

        abort_if(
            CurrentLogisticsHub::isHubOperator($request),
            403,
            'Hub Operators use their assigned branch and cannot switch facilities.'
        );

        $validated = $request->validate([
            'facility_id' => ['required', 'integer'],
        ]);

        $facility = LogisticsFacility::query()
            ->whereKey((int) $validated['facility_id'])
            ->where('logistics_account_id', $logistics->id)
            ->where('is_active', true)
            ->first();

        if (! $facility) {
            return back()
                ->withErrors([
                    'facility_id' => 'Select an active sorting facility that belongs to your Logistics network.',
                ])
                ->withInput();
        }

        $request->session()->put([
            'logistics_operating_facility_id' => (int) $facility->id,
            'logistics_operating_facility_name' => (string) $facility->name,
            'logistics_operating_facility_code' => (string) ($facility->code ?? ''),
            'logistics_operating_session_id' => (string) $request->session()->getId(),
        ]);

        try {
            app(LogisticsActivityLogger::class)->record(
                $request,
                'Branch Access',
                'selected',
                'Selected operating branch: '.$facility->name,
                'logistics_facility',
                (int) $facility->id,
                (int) $facility->id
            );
        } catch (\Throwable $exception) {
            report($exception);
        }

        $intended = (string) $request->session()->pull(
            'logistics_branch_intended_url',
            ''
        );

        if ($intended !== '' && str_starts_with($intended, $request->getSchemeAndHttpHost())) {
            return redirect()->to($intended);
        }

        return redirect()
            ->route('logistics.dashboard')
            ->with('success', 'Operating branch set to '.$facility->name.'.');
    }

    public function change(Request $request): RedirectResponse
    {
        abort_if(
            CurrentLogisticsHub::isHubOperator($request),
            403,
            'Hub Operators cannot switch their assigned branch.'
        );

        CurrentLogisticsHub::clearOperatingContext($request);

        return redirect()->route('logistics.branch.select');
    }
}
