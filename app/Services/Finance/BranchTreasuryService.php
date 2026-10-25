<?php

namespace App\Services\Finance;

use App\Models\Accounts\LogisticsAccount;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Finance\BranchRemittanceBatch;
use App\Models\Finance\BranchRemittanceItem;
use App\Models\Finance\CodRemittanceBatch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BranchTreasuryService
{
    public function __construct(
        private readonly FinanceReleaseService $release
    ) {
    }

    public function mainBranch(LogisticsAccount $logistics): ?LogisticsFacility
    {
        return LogisticsFacility::query()
            ->forLogistics((int) $logistics->id)
            ->active()
            ->mainBranch()
            ->first();
    }

    /** @return Collection<int, CodRemittanceBatch> */
    public function eligibleRiderBatches(
        LogisticsAccount $logistics,
        LogisticsFacility $origin
    ): Collection {
        if ((int) $origin->logistics_account_id !== (int) $logistics->id) {
            return collect();
        }

        if ($origin->is_main_branch) {
            return collect();
        }

        return CodRemittanceBatch::query()
            ->with(['courier', 'items.order'])
            ->where('logistics_account_id', $logistics->id)
            ->where('logistics_facility_id', $origin->id)
            ->where('remittance_method', 'cash_hub')
            ->where('status', 'verified')
            ->whereNull('central_reconciled_at')
            ->whereDoesntHave('branchRemittanceItem')
            ->orderBy('verified_at')
            ->get();
    }

    public function prepareBatch(
        LogisticsAccount $logistics,
        LogisticsFacility $origin,
        array $riderBatchIds,
        string $preparedByType,
        int $preparedById,
        ?string $note = null
    ): BranchRemittanceBatch {
        abort_unless((int) $origin->logistics_account_id === (int) $logistics->id, 404);

        if ($origin->is_main_branch) {
            throw ValidationException::withMessages([
                'rider_batch_ids' => 'The Main Branch is already the central treasury and does not remit cash to itself.',
            ]);
        }

        $main = $this->mainBranch($logistics);

        if (! $main) {
            throw ValidationException::withMessages([
                'main_branch' => 'Set one active sorting facility as the Main Branch before preparing branch treasury remittance.',
            ]);
        }

        $ids = collect($riderBatchIds)
            ->map(fn (mixed $id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            throw ValidationException::withMessages([
                'rider_batch_ids' => 'Select at least one verified Rider cash batch to remit to the Main Branch.',
            ]);
        }

        return DB::transaction(function () use (
            $logistics,
            $origin,
            $main,
            $ids,
            $preparedByType,
            $preparedById,
            $note
        ): BranchRemittanceBatch {
            $riderBatches = CodRemittanceBatch::query()
                ->with(['courier', 'items.order'])
                ->whereIn('id', $ids->all())
                ->where('logistics_account_id', $logistics->id)
                ->where('logistics_facility_id', $origin->id)
                ->where('remittance_method', 'cash_hub')
                ->where('status', 'verified')
                ->whereNull('central_reconciled_at')
                ->whereDoesntHave('branchRemittanceItem')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($riderBatches->count() !== $ids->count()) {
                throw ValidationException::withMessages([
                    'rider_batch_ids' => 'One or more Rider cash batches are no longer eligible for Main Branch remittance.',
                ]);
            }

            $riderBatches = $ids->map(fn (int $id): CodRemittanceBatch => $riderBatches->get($id));

            $expected = round((float) $riderBatches->sum(
                fn (CodRemittanceBatch $batch): float => (float) ($batch->confirmed_amount ?? $batch->expected_amount)
            ), 2);

            $batch = BranchRemittanceBatch::query()->create([
                'logistics_account_id' => $logistics->id,
                'origin_facility_id' => $origin->id,
                'destination_facility_id' => $main->id,
                'batch_code' => $this->batchCode(),
                'origin_name_snapshot' => $origin->name,
                'destination_name_snapshot' => $main->name,
                'expected_amount' => $expected,
                'submitted_amount' => $expected,
                'difference_amount' => 0,
                'currency' => 'PHP',
                'status' => 'submitted',
                'branch_note' => $note,
                'prepared_by_type' => $preparedByType,
                'prepared_by_id' => $preparedById,
                'submitted_at' => now(),
            ]);

            foreach ($riderBatches as $riderBatch) {
                BranchRemittanceItem::query()->create([
                    'branch_remittance_batch_id' => $batch->id,
                    'cod_remittance_batch_id' => $riderBatch->id,
                    'expected_amount' => (float) ($riderBatch->confirmed_amount ?? $riderBatch->expected_amount),
                    'branch_received_at' => $riderBatch->verified_at,
                ]);
            }

            return $batch->fresh([
                'originFacility',
                'destinationFacility',
                'items.codRemittanceBatch.courier',
                'items.codRemittanceBatch.items.order',
            ]);
        });
    }

    public function verifyAtMainBranch(
        BranchRemittanceBatch $batch,
        LogisticsAccount $logistics,
        LogisticsFacility $currentFacility,
        float $confirmedAmount,
        string $verifierType,
        int $verifierId,
        ?string $note = null
    ): BranchRemittanceBatch {
        return DB::transaction(function () use (
            $batch,
            $logistics,
            $currentFacility,
            $confirmedAmount,
            $verifierType,
            $verifierId,
            $note
        ): BranchRemittanceBatch {
            $locked = BranchRemittanceBatch::query()
                ->with(['items.codRemittanceBatch.items.order'])
                ->whereKey($batch->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless((int) $locked->logistics_account_id === (int) $logistics->id, 404);
            abort_unless((int) $locked->destination_facility_id === (int) $currentFacility->id, 403);
            abort_unless($currentFacility->is_main_branch, 403, 'Only the Main Branch can centrally reconcile branch cash.');

            if (! in_array($locked->status, ['submitted', 'discrepancy'], true)) {
                throw ValidationException::withMessages([
                    'branch_remittance' => 'This Branch Treasury batch can no longer be reconciled.',
                ]);
            }

            $confirmed = round($confirmedAmount, 2);
            $expected = round((float) $locked->expected_amount, 2);
            $difference = round($confirmed - $expected, 2);
            $isExact = abs($difference) < 0.01;

            $locked->forceFill([
                'confirmed_amount' => $confirmed,
                'difference_amount' => $difference,
                'status' => $isExact ? 'verified' : 'discrepancy',
                'main_branch_note' => $note,
                'verified_by_type' => $verifierType,
                'verified_by_id' => $verifierId,
                'verified_at' => $isExact ? now() : null,
            ])->save();

            if (! $isExact) {
                return $locked->fresh(['originFacility', 'destinationFacility', 'items.codRemittanceBatch']);
            }

            foreach ($locked->items as $item) {
                $riderBatch = $item->codRemittanceBatch;

                if (! $riderBatch) {
                    continue;
                }

                $riderBatch->forceFill([
                    'central_reconciled_at' => now(),
                ])->save();

                foreach ($riderBatch->items as $riderItem) {
                    if ($riderItem->order) {
                        $this->release->releaseOrder(
                            $riderItem->order,
                            'branch_main_cash_handover:'.$locked->batch_code
                        );
                    }
                }

                $item->forceFill([
                    'central_released_at' => now(),
                ])->save();
            }

            return $locked->fresh([
                'originFacility',
                'destinationFacility',
                'items.codRemittanceBatch.courier',
                'items.codRemittanceBatch.items.order',
            ]);
        });
    }

    private function batchCode(): string
    {
        do {
            $code = 'BR-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
        } while (BranchRemittanceBatch::query()->where('batch_code', $code)->exists());

        return $code;
    }
}
