<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Services\Finance\CodCustodyMonitor;
use App\Support\CurrentLogisticsAccount;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Manager-only, auditable review actions. Never directly changes account_status. */
class LogisticsCodComplianceController extends Controller
{
    public function __construct(private readonly CodCustodyMonitor $monitor) {}

    public function index(Request $request): View
    {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $alerts = $this->monitor->logistics((int) $logistics->id);
        $caseIds = $alerts->pluck('case.id')->filter()->values();
        $events = DB::table('cod_custody_case_events')
            ->whereIn('cod_custody_case_id', $caseIds)
            ->latest()->limit(100)->get()->groupBy('cod_custody_case_id');
        $totalCents = $alerts->sum('overdue_cents');
        return view('finance.cod-compliance', compact('logistics','alerts','events','totalCents'));
    }

    /** Periodic dashboard warning; Manager auth enforced by finance route group. */
    public function status(Request $request): JsonResponse
    {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $alerts = $this->monitor->logistics((int) $logistics->id);
        return response()->json([
            'overdue_cents' => $alerts->sum('overdue_cents'),
            'riders_count' => $alerts->filter(fn ($a) => $a['overdue_cents'] > 0)->count(),
        ])->header('Cache-Control','private, no-store');
    }

    public function action(Request $request, CourierAccount $rider): RedirectResponse
    {
        $logistics = CurrentLogisticsAccount::resolve($request);
        abort_unless((int) $rider->logistics_account_id === (int) $logistics->id, 404);
        $validated = $request->validate([
            'action' => ['required','in:call_logged,warning,extend,restrict,release,review_suspension,resolve'],
            'note' => ['required','string','min:8','max:1500'],
            'extend_until' => ['required_if:action,extend','nullable','date'],
        ]);
        $action = $validated['action'];
        $current = $this->monitor->rider($rider);
        $case = $current['case'];
        $openCash = (int) $current['all_cents'] > 0;
        $requiresCash = in_array($action, ['call_logged','warning','extend','restrict','review_suspension'], true);
        if ($requiresCash && ! $openCash) {
            throw ValidationException::withMessages(['action' => 'No outstanding COD cash remains for this Rider. Refresh the monitoring page.']);
        }
        if ($action === 'restrict' && ! $current['is_review_eligible']) {
            throw ValidationException::withMessages(['action' => 'COD restrictions require at least 24 hours overdue and an incident review.']);
        }
        if ($action === 'review_suspension' && (! $case || ! $case->cod_restricted_at)) {
            throw ValidationException::withMessages(['action' => 'Log a warning and review COD assignment restriction before requesting full account suspension.']);
        }
        if ($action === 'release' && (! $case || ! $case->cod_restricted_at)) {
            throw ValidationException::withMessages(['action' => 'No current COD assignment restriction exists.']);
        }
        if ($action === 'resolve' && $openCash) {
            throw ValidationException::withMessages(['action' => 'Cannot resolve while COD cash remains outstanding. Verify the physical handover first.']);
        }
        $until = null;
        if ($action === 'extend') {
            $now = CarbonImmutable::now(config('cod_policy.timezone', 'Asia/Manila'));
            $until = CarbonImmutable::parse($validated['extend_until'], config('cod_policy.timezone', 'Asia/Manila'));
            if ($until->lessThanOrEqualTo($now) || $until->greaterThan($now->addHours((int) config('cod_policy.max_extension_hours',48)))) {
                throw ValidationException::withMessages(['extend_until' => 'Set a future extension within 48 hours. Document hub closure or the verified reason.']);
            }
        }
        DB::transaction(function () use ($logistics,$rider,$action,$validated,$until): void {
            $now = now();
            DB::table('cod_custody_cases')->upsert([[
                'logistics_account_id' => $logistics->id,
                'courier_account_id' => $rider->id,
                'status' => 'open',
                'created_at' => $now, 'updated_at' => $now,
            ]], ['logistics_account_id','courier_account_id'], ['updated_at']);
            $locked = DB::table('cod_custody_cases')
                ->where('logistics_account_id', $logistics->id)
                ->where('courier_account_id', $rider->id)
                ->lockForUpdate()->first();
            $updates = ['updated_at' => $now];
            if ($action === 'extend') {
                $updates['extended_until'] = $until->utc()->toDateTimeString();
                $updates['extension_applies_through'] = $now;
            }
            if ($action === 'restrict') $updates['cod_restricted_at'] = $now;
            if ($action === 'release') $updates['cod_restricted_at'] = null;
            if ($action === 'review_suspension') $updates['suspension_review_at'] = $now;
            if ($action === 'resolve') {
                $updates['status'] = 'resolved';
                $updates['resolved_at'] = $now;
                $updates['cod_restricted_at'] = null;
                $updates['suspension_review_at'] = null;
                $updates['extended_until'] = null;
                $updates['extension_applies_through'] = null;
            } else {
                $updates['status'] = 'open';
                $updates['resolved_at'] = null;
            }
            DB::table('cod_custody_cases')->where('id', $locked->id)->update($updates);
            DB::table('cod_custody_case_events')->insert([
                'cod_custody_case_id' => $locked->id,
                'logistics_actor_id' => (int) $logistics->id,
                'action' => $action,
                'note' => trim((string) $validated['note']),
                'created_at' => $now,'updated_at' => $now,
            ]);
        });
        return redirect()->route('logistics.cod-compliance.index')
            ->with('success', 'COD compliance action recorded in the audit log.');
    }
}
