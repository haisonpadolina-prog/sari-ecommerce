<?php

namespace App\Services\Finance;

use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Finance\CodRemittanceBatch;
use App\Models\Finance\CodRemittanceItem;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CodRemittanceService
{
    public function __construct(
        private readonly FinanceReleaseService $release,
        private readonly FinancialFlowService $financialFlow
    ) {
    }

    /** @return Collection<int, MarketplaceOrder> */
    public function eligibleOrdersForRider(CourierAccount $rider): Collection
    {
        $lastMileOrderIds = DB::table('last_mile_assignment_orders as link')
            ->join(
                'last_mile_assignments as assignment',
                'assignment.id',
                '=',
                'link.last_mile_assignment_id'
            )
            ->where('assignment.courier_account_id', $rider->id)
            ->pluck('link.marketplace_order_id');

        return MarketplaceOrder::query()
            ->where('status', 'delivered')
            ->whereRaw('UPPER(payment_method) = ?', ['COD'])
            ->whereRaw('LOWER(payment_status) = ?', ['paid'])
            ->whereDoesntHave('codRemittanceItem')
            ->whereDoesntHave('riderEarnings', function (Builder $query): void {
                $query->whereIn('status', ['reserved', 'paid']);
            })
            ->whereDoesntHave('sellerSettlement', function (Builder $query): void {
                $query->where('status', 'paid');
            })
            ->where(function (Builder $query) use ($rider, $lastMileOrderIds): void {
                if ($lastMileOrderIds->isNotEmpty()) {
                    $query->whereIn('id', $lastMileOrderIds);
                } else {
                    $query->whereRaw('1 = 0');
                }

                $query->orWhere(function (Builder $direct) use ($rider): void {
                    $direct
                        ->whereDoesntHave('logisticsParcel')
                        ->whereRaw('LOWER(courier_email) = ?', [strtolower((string) $rider->email)]);
                });
            })
            ->orderBy('delivered_at')
            ->get();
    }

    public function submitBatch(
        CourierAccount $rider,
        array $orderIds,
        ?float $submittedAmount = null,
        string $method = 'cash_hub',
        ?string $reference = null,
        ?string $note = null,
        ?int $facilityId = null
    ): CodRemittanceBatch {
        $orderIds = collect($orderIds)
            ->map(fn (mixed $id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values();

        if ($orderIds->isEmpty()) {
            throw ValidationException::withMessages([
                'order_ids' => 'Select at least one delivered COD parcel to remit.',
            ]);
        }

        if ($method !== 'cash_hub') {
            throw ValidationException::withMessages([
                'remittance_method' => 'COD remittance is physical cash only. Bring the full Buyer collection to the assigned Logistics hub.',
            ]);
        }

        return DB::transaction(function () use (
            $rider,
            $orderIds,
            $note,
            $facilityId
        ): CodRemittanceBatch {
            $eligible = $this->eligibleOrdersForRider($rider)->keyBy('id');
            $orders = $orderIds->map(function (int $id) use ($eligible): MarketplaceOrder {
                $order = $eligible->get($id);

                if (! $order) {
                    throw ValidationException::withMessages([
                        'order_ids' => 'One or more selected COD parcels are not eligible for remittance.',
                    ]);
                }

                return $order;
            });

            $providerIds = $orders
                ->pluck('logistics_account_id')
                ->filter()
                ->unique();

            if ($providerIds->count() > 1) {
                throw ValidationException::withMessages([
                    'order_ids' => 'A remittance batch cannot mix different Logistics providers.',
                ]);
            }

            $providerId = $providerIds->first() ?: $rider->logistics_account_id;

            if (! $providerId) {
                throw ValidationException::withMessages([
                    'logistics_facility_id' => 'A Logistics provider and physical hub are required for COD cash remittance.',
                ]);
            }

            if ($rider->logistics_account_id && (int) $providerId !== (int) $rider->logistics_account_id) {
                throw ValidationException::withMessages([
                    'order_ids' => 'The COD parcels do not belong to your Logistics provider.',
                ]);
            }

            $facility = $this->resolveFacility((int) $providerId, $facilityId);

            foreach ($orders as $order) {
                $this->financialFlow->recordCompletedOrder(
                    $order,
                    'cod_physical_cash_remittance_preparation',
                    null
                );
            }

            $expected = round((float) $orders->sum(
                fn (MarketplaceOrder $order): float => (float) $order->total
            ), 2);

            // The Rider does not declare or deduct an amount. SARI computes the
            // full COD custody amount from the selected delivered parcels.
            $batch = CodRemittanceBatch::query()->create([
                'logistics_account_id' => (int) $providerId,
                'logistics_facility_id' => $facility->id,
                'courier_account_id' => $rider->id,
                'batch_code' => $this->batchCode(),
                'hub_name_snapshot' => $facility->name,
                'hub_address_snapshot' => $facility->address,
                'expected_amount' => $expected,
                'submitted_amount' => $expected,
                'difference_amount' => 0,
                'currency' => 'PHP',
                'remittance_method' => 'cash_hub',
                'status' => 'submitted',
                'rider_reference' => null,
                'rider_note' => $note,
                'submitted_at' => now(),
            ]);

            foreach ($orders as $order) {
                CodRemittanceItem::query()->create([
                    'cod_remittance_batch_id' => $batch->id,
                    'marketplace_order_id' => $order->id,
                    'expected_amount' => (float) $order->total,
                    'collected_at' => $order->delivered_at,
                ]);
            }

            return $batch->fresh(['items.order', 'courier', 'logistics', 'facility']);
        });
    }

    public function verifyBatch(
        CodRemittanceBatch $batch,
        LogisticsAccount $logistics,
        float $confirmedAmount,
        string $verifierType,
        int $verifierId,
        ?string $reference = null,
        ?string $note = null
    ): CodRemittanceBatch {
        return DB::transaction(function () use (
            $batch,
            $logistics,
            $confirmedAmount,
            $verifierType,
            $verifierId,
            $reference,
            $note
        ): CodRemittanceBatch {
            $locked = CodRemittanceBatch::query()
                ->with(['items.order', 'facility'])
                ->whereKey($batch->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless((int) $locked->logistics_account_id === (int) $logistics->id, 404);

            if ($locked->remittance_method !== 'cash_hub') {
                throw ValidationException::withMessages([
                    'remittance' => 'Only physical COD cash handover can be reconciled in the current SARI remittance flow.',
                ]);
            }

            if (! in_array($locked->status, ['submitted', 'discrepancy'], true)) {
                throw ValidationException::withMessages([
                    'remittance' => 'This remittance batch can no longer be verified.',
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
                'logistics_reference' => $reference,
                'logistics_note' => $note,
                'verified_by_type' => $verifierType,
                'verified_by_id' => $verifierId,
                'verified_at' => $isExact ? now() : null,
            ])->save();

            if (! $isExact) {
                return $locked->fresh(['items.order', 'courier', 'facility']);
            }

            // Rider -> Branch verification clears Rider cash custody. Wallets
            // are released only when the cash is centrally reconciled at the
            // Main Branch. If the Rider physically remits directly to the Main
            // Branch, central custody is already satisfied and no second branch
            // transfer is required.
            $facility = $locked->facility;

            if ($facility?->is_main_branch) {
                foreach ($locked->items as $item) {
                    if ($item->order) {
                        $this->release->releaseOrder(
                            $item->order,
                            'cod_cash_main_branch:'.$locked->batch_code
                        );
                    }
                }

                $locked->forceFill([
                    'central_reconciled_at' => now(),
                ])->save();
            }

            return $locked->fresh(['items.order', 'courier', 'facility']);
        });
    }

    private function resolveFacility(int $providerId, ?int $facilityId): LogisticsFacility
    {
        $query = LogisticsFacility::query()
            ->active()
            ->forLogistics($providerId);

        if ($facilityId) {
            $facility = (clone $query)->whereKey($facilityId)->first();

            if (! $facility) {
                throw ValidationException::withMessages([
                    'logistics_facility_id' => 'Choose an active hub that belongs to your Logistics provider.',
                ]);
            }

            return $facility;
        }

        $facilities = $query->limit(2)->get();

        if ($facilities->count() === 1) {
            return $facilities->first();
        }

        throw ValidationException::withMessages([
            'logistics_facility_id' => $facilities->isEmpty()
                ? 'No active Logistics hub is available for physical COD remittance.'
                : 'Choose the Logistics hub where you will physically turn over the COD cash.',
        ]);
    }

    private function batchCode(): string
    {
        do {
            $code = 'REM-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
        } while (CodRemittanceBatch::query()->where('batch_code', $code)->exists());

        return $code;
    }
}
