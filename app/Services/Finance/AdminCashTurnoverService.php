<?php

namespace App\Services\Finance;

use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Finance\AdminCashTurnover;
use App\Models\Finance\AdminCashTurnoverCount;
use App\Models\Finance\AdminCashTurnoverItem;
use App\Models\Finance\CodRemittanceBatch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminCashTurnoverService
{
    public function __construct(
        private readonly FinanceReleaseService $release
    ) {
    }

    /** @return Collection<int, CodRemittanceBatch> */
    public function eligibleBatches(LogisticsAccount $logistics, LogisticsFacility $main): Collection
    {
        $this->requireMainBranch($logistics, $main);

        return $this->eligibleQuery($logistics, $main)
            ->with(['courier', 'items.order', 'facility'])
            ->orderBy('central_reconciled_at')
            ->get();
    }

    /**
     * Logistics -> SARI Admin physical cash handover. Amounts are calculated
     * from exact, already-centralized Rider cash batches, never typed by Logistics.
     */
    public function prepare(
        LogisticsAccount $logistics,
        LogisticsFacility $main,
        array $riderBatchIds,
        ?string $note = null
    ): AdminCashTurnover {
        $this->requireMainBranch($logistics, $main);

        $ids = collect($riderBatchIds)
            ->map(fn (mixed $id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            throw ValidationException::withMessages([
                'rider_batch_ids' => 'Select at least one centrally reconciled COD batch for physical turnover to SARI Admin.',
            ]);
        }

        return DB::transaction(function () use ($logistics, $main, $ids, $note): AdminCashTurnover {
            $eligible = $this->eligibleQuery($logistics, $main)
                ->whereIn('id', $ids->all())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($eligible->count() !== $ids->count()) {
                throw ValidationException::withMessages([
                    'rider_batch_ids' => 'One or more batches are not eligible, belong to another Logistics provider, or are already included in a turnover.',
                ]);
            }

            $batches = $ids->map(fn (int $id): CodRemittanceBatch => $eligible->get($id));
            $expectedCents = $batches->sum(fn (CodRemittanceBatch $batch): int => $this->cents($batch->confirmed_amount));

            if ($expectedCents <= 0) {
                throw ValidationException::withMessages([
                    'rider_batch_ids' => 'Cash turnover must contain a positive verified COD collection.',
                ]);
            }

            $turnover = AdminCashTurnover::query()->create([
                'logistics_account_id' => $logistics->id,
                'main_facility_id' => $main->id,
                'turnover_code' => $this->turnoverCode(),
                'expected_amount' => $expectedCents / 100,
                'submitted_amount' => $expectedCents / 100,
                'difference_amount' => 0,
                'currency' => 'PHP',
                'status' => 'submitted',
                'logistics_note' => $note,
                'prepared_by_logistics_account_id' => $logistics->id,
                'submitted_at' => now(),
            ]);

            foreach ($batches as $batch) {
                AdminCashTurnoverItem::query()->create([
                    'admin_cash_turnover_id' => $turnover->id,
                    'cod_remittance_batch_id' => $batch->id,
                    'expected_amount' => $batch->confirmed_amount,
                ]);
            }

            return $turnover->fresh(['logistics', 'mainFacility', 'items.codRemittanceBatch']);
        });
    }

    /**
     * Only an authorized SARI Admin may record the physical count. A shortage
     * or overage remains in Logistics custody; no earnings become withdrawable.
     * Every attempt is stored, and wallet release happens atomically on exact receipt.
     */
    public function receive(
        AdminCashTurnover $turnover,
        AdminAccount $admin,
        float $confirmedAmount,
        ?string $note = null
    ): AdminCashTurnover {
        abort_unless($admin->canManagePlatformSettings(), 403, 'SARI treasury authorization required.');

        if (! is_finite($confirmedAmount) || $confirmedAmount < 0 || $confirmedAmount > 999999999.99) {
            throw ValidationException::withMessages([
                'confirmed_amount' => 'Enter a valid, non-negative actual physical cash count.',
            ]);
        }

        return DB::transaction(function () use ($turnover, $admin, $confirmedAmount, $note): AdminCashTurnover {
            $locked = AdminCashTurnover::query()
                ->with([
                    'mainFacility',
                    'items.codRemittanceBatch.items.order',
                    'items.codRemittanceBatch.branchRemittanceItem.batch',
                ])
                ->whereKey($turnover->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($locked->status, ['submitted', 'discrepancy'], true)) {
                throw ValidationException::withMessages([
                    'turnover' => 'This cash turnover has already been received and cannot be counted again.',
                ]);
            }

            $expectedCents = $this->cents($locked->expected_amount);
            $confirmedCents = $this->cents($confirmedAmount);
            $differenceCents = $confirmedCents - $expectedCents;
            $isExact = $differenceCents === 0;

            if (! $isExact && trim((string) $note) === '') {
                throw ValidationException::withMessages([
                    'admin_note' => 'Record a reason when the physical cash count is short or over.',
                ]);
            }

            if ($locked->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'turnover' => 'This turnover has no COD batches and cannot be received.',
                ]);
            }

            // Re-validate custody at receipt time, not just when the Logistics
            // Manager initially assembled the turnover. This check protects
            // against stale or altered COD records.
            $itemTotalCents = 0;
            foreach ($locked->items as $item) {
                $batch = $item->codRemittanceBatch;

                $branchTransfer = $batch?->branchRemittanceItem?->batch;
                $arrivedAtMain = $batch
                    && ((int) $batch->logistics_facility_id === (int) $locked->main_facility_id
                        || ($branchTransfer
                            && $branchTransfer->status === 'verified'
                            && (int) $branchTransfer->logistics_account_id === (int) $locked->logistics_account_id
                            && (int) $branchTransfer->destination_facility_id === (int) $locked->main_facility_id));

                if (! $arrivedAtMain
                    || (int) $batch->logistics_account_id !== (int) $locked->logistics_account_id
                    || $batch->status !== 'verified'
                    || $batch->remittance_method !== 'cash_hub'
                    || ! $batch->central_reconciled_at
                    || $this->cents($batch->confirmed_amount) !== $this->cents($batch->expected_amount)
                    || $this->cents($item->expected_amount) !== $this->cents($batch->confirmed_amount)
                    || $batch->items->isEmpty()
                    || $batch->items->sum(fn ($riderItem): int => $this->cents($riderItem->expected_amount)) !== $this->cents($batch->expected_amount)) {
                    throw ValidationException::withMessages([
                        'turnover' => 'A COD batch failed verification. No cash receipt or earnings were released.',
                    ]);
                }

                $itemTotalCents += $this->cents($item->expected_amount);
            }

            if ($itemTotalCents !== $expectedCents) {
                throw ValidationException::withMessages([
                    'turnover' => 'The cash turnover total no longer matches its COD batches.',
                ]);
            }

            // Append-only physical count log, including unsuccessful counts.
            AdminCashTurnoverCount::query()->create([
                'admin_cash_turnover_id' => $locked->id,
                'admin_account_id' => $admin->id,
                'confirmed_amount' => $confirmedCents / 100,
                'difference_amount' => $differenceCents / 100,
                'result' => $isExact ? 'received' : 'discrepancy',
                'note' => $note,
                'counted_at' => now(),
            ]);

            $locked->forceFill([
                'confirmed_amount' => $confirmedCents / 100,
                'difference_amount' => $differenceCents / 100,
                'status' => $isExact ? 'received' : 'discrepancy',
                'admin_note' => $note,
                'received_by_admin_account_id' => $isExact ? $admin->id : null,
                'received_at' => $isExact ? now() : null,
            ])->save();

            if (! $isExact) {
                return $locked->fresh(['logistics', 'mainFacility', 'counts.admin']);
            }

            // The Admin receipt and every related wallet credit commit together.
            // If one credit fails, the entire receipt rolls back.
            foreach ($locked->items as $item) {
                $batch = $item->codRemittanceBatch;

                foreach ($batch->items as $riderItem) {
                    if (! $riderItem->order) {
                        throw ValidationException::withMessages([
                            'turnover' => 'An order is missing from this COD collection. Cash receipt was not finalized.',
                        ]);
                    }

                    $this->release->releaseOrder(
                        $riderItem->order,
                        'sari_admin_cash_receipt:'.$locked->turnover_code
                    );
                }

                // Existing Branch Treasury column now indicates actual release,
                // not just the preceding physical Main Branch reconciliation.
                if ($batch->branchRemittanceItem) {
                    $batch->branchRemittanceItem->forceFill([
                        'central_released_at' => now(),
                    ])->save();
                }
            }

            return $locked->fresh(['logistics', 'mainFacility', 'receivedByAdmin', 'items', 'counts.admin']);
        });
    }

    private function eligibleQuery(LogisticsAccount $logistics, LogisticsFacility $main): Builder
    {
        return CodRemittanceBatch::query()
            ->where('logistics_account_id', $logistics->id)
            ->where('remittance_method', 'cash_hub')
            ->where('status', 'verified')
            ->whereNotNull('verified_at')
            ->whereNotNull('central_reconciled_at')
            ->whereColumn('expected_amount', 'confirmed_amount')
            ->whereDoesntHave('adminCashTurnoverItem')
            ->where(function (Builder $query) use ($main): void {
                // Rider cash handed directly to Main Branch.
                $query->where('logistics_facility_id', $main->id)
                    // Or an exact, verified regular-Branch -> Main handover.
                    ->orWhereHas('branchRemittanceItem.batch', function (Builder $branch) use ($main): void {
                        $branch->where('status', 'verified')
                            ->where('logistics_account_id', $main->logistics_account_id)
                            ->where('destination_facility_id', $main->id);
                    });
            });
    }

    private function requireMainBranch(LogisticsAccount $logistics, LogisticsFacility $main): void
    {
        abort_unless(
            (int) $main->logistics_account_id === (int) $logistics->id
                && $main->is_main_branch
                && $main->is_active,
            403,
            'Switch to your active Logistics Main Branch to prepare a SARI Admin cash turnover.'
        );
    }

    private function cents(mixed $amount): int
    {
        return (int) round((float) $amount * 100);
    }

    private function turnoverCode(): string
    {
        do {
            $code = 'AT-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
        } while (AdminCashTurnover::query()->where('turnover_code', $code)->exists());

        return $code;
    }
}
