<?php

namespace App\Services\Finance;

use App\Models\Accounts\CourierAccount;
use App\Models\Finance\CodRemittanceBatch;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Read-only COD custody ledger view. A prepared batch DOES NOT clear custody;
 * verified exact physical cash handover does. Never deduct rider earnings.
 */
class CodCustodyMonitor
{
    public function __construct(private readonly CodRemittanceService $remittances) {}

    private function deadline(mixed $collectedAt): CarbonImmutable
    {
        $tz = config('cod_policy.timezone', 'Asia/Manila');
        $cutoff = config('cod_policy.cutoff_time', '20:00');
        $local = CarbonImmutable::parse($collectedAt, config('app.timezone', 'UTC'))->setTimezone($tz);
        $due = $local->startOfDay()->setTimeFromTimeString($cutoff);
        // After-cutoff handovers fall due on the next day, not retroactively.
        return $local->greaterThan($due) ? $due->addDay() : $due;
    }

    /** @return array<string,mixed> */
    public function rider(CourierAccount $rider): array
    {
        $now = CarbonImmutable::now(config('cod_policy.timezone', 'Asia/Manila'));
        $entries = collect();

        // The existing eligibility service is the canonical source of
        // delivered, paid COD orders assigned to this particular Rider.
        foreach ($this->remittances->eligibleOrdersForRider($rider) as $order) {
            $entries->push([
                'source' => 'unbatched', 'reference' => (string) $order->order_number,
                'amount_cents' => (int) round(((float) $order->total) * 100),
                'collected_at' => $order->delivered_at ?: $order->updated_at,
                'due_at' => $this->deadline($order->delivered_at ?: $order->updated_at),
                'facility' => null,
            ]);
        }

        // Submitted and discrepancy batches still belong to the Rider until
        // EXACT cash verification; don't net an unverified partial count.
        $batches = CodRemittanceBatch::query()
            ->where('courier_account_id', $rider->id)
            ->where('logistics_account_id', (int) $rider->logistics_account_id)
            ->whereIn('status', ['submitted','discrepancy'])
            ->with(['items','facility'])
            ->get();
        foreach ($batches as $batch) {
            $earliest = $batch->items->pluck('collected_at')->filter()->sort()->first();
            $entries->push([
                'source' => $batch->status,
                'reference' => (string) $batch->batch_code,
                'amount_cents' => (int) round(((float) $batch->expected_amount) * 100),
                'collected_at' => $earliest ?: $batch->submitted_at ?: $batch->created_at,
                'due_at' => $this->deadline($earliest ?: $batch->submitted_at ?: $batch->created_at),
                'facility' => $batch->facility?->name,
            ]);
        }

        $case = DB::table('cod_custody_cases')
            ->where('courier_account_id', $rider->id)
            ->where('logistics_account_id', (int) $rider->logistics_account_id)
            ->first();
        $extension = $case?->extended_until
            ? CarbonImmutable::parse($case->extended_until, config('app.timezone','UTC'))->setTimezone(config('cod_policy.timezone','Asia/Manila'))
            : null;
        $extensionScope = $case?->extension_applies_through
            ? CarbonImmutable::parse($case->extension_applies_through, config('app.timezone', 'UTC'))
            : null;
        $dueEntries = $entries->map(function (array $e) use ($extension, $extensionScope, $now): array {
            // Extensions cover already-collected cash only, never future COD.
            $collected = CarbonImmutable::parse($e['collected_at'], config('app.timezone', 'UTC'));
            $inScope = $extensionScope && $collected->lessThanOrEqualTo($extensionScope);
            $due = ($extension && $inScope && $extension->greaterThan($e['due_at']))
                ? $extension : $e['due_at'];
            $e['effective_due_at'] = $due;
            $e['overdue'] = $due->lessThan($now);
            $e['overdue_hours'] = $e['overdue'] ? $due->diffInMinutes($now) / 60 : 0;
            return $e;
        });
        $overdue = $dueEntries->filter(fn ($e) => $e['overdue']);
        return [
            'rider_id' => (int) $rider->id,
            'name' => trim($rider->first_name.' '.$rider->last_name) ?: 'Rider #'.$rider->id,
            'phone' => (string) ($rider->contact_no ?? ''),
            'all_cents' => $dueEntries->sum('amount_cents'),
            'overdue_cents' => $overdue->sum('amount_cents'),
            'unbatched_cents' => $dueEntries->where('source','unbatched')->sum('amount_cents'),
            'overdue_count' => $overdue->count(),
            'max_overdue_hours' => (float) ($overdue->max('overdue_hours') ?? 0),
            'earliest_due_at' => $dueEntries->sortBy('effective_due_at')->first()['effective_due_at'] ?? null,
            'entries' => $dueEntries->values(),
            'case' => $case,
            'has_restriction' => (bool) ($case?->cod_restricted_at && $case?->status === 'open'),
            'is_review_eligible' => (float) ($overdue->max('overdue_hours') ?? 0) >= (float) config('cod_policy.restriction_review_after_hours', 24),
        ];
    }

    /** @return Collection<int,array<string,mixed>> */
    public function logistics(int $logisticsId): Collection
    {
        // The case list is scoped to Riders of this logistics provider; no
        // other provider's cash or Rider contact information is exposed.
        $rows = collect();
        CourierAccount::query()
            ->where('logistics_account_id', $logisticsId)
            ->orderBy('id')
            ->chunkById(50, function ($riders) use (&$rows): void {
                foreach ($riders as $rider) {
                    $snapshot = $this->rider($rider);
                    if ($snapshot['overdue_cents'] > 0 || $snapshot['has_restriction'] || ($snapshot['case']?->suspension_review_at)) {
                        $rows->push($snapshot);
                    }
                }
            });
        return $rows->sortByDesc(fn ($r) => [$r['has_restriction'] ? 1 : 0, $r['overdue_cents']])->values();
    }
}
