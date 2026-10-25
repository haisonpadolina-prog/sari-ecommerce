<?php

namespace App\Services\Finance;

use App\Models\Finance\FinancePayoutRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PayoutService
{
    public function __construct(
        private readonly WalletService $wallets
    ) {
    }

    public function request(
        string $ownerType,
        int $ownerId,
        float $amount,
        string $method,
        string $accountName,
        string $accountReference
    ): FinancePayoutRequest {
        $amount = round($amount, 2);
        $method = strtolower(trim($method));
        $accountName = trim($accountName);
        $accountReference = trim($accountReference);

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Withdrawal amount must be greater than zero.',
            ]);
        }

        if (! in_array($method, ['gcash', 'maya', 'bank', 'manual_cash'], true)) {
            throw ValidationException::withMessages([
                'payout_method' => 'Choose a supported payout method.',
            ]);
        }

        if ($accountName === '' || $accountReference === '') {
            throw ValidationException::withMessages([
                'destination' => 'Payout account name and destination are required.',
            ]);
        }

        return DB::transaction(function () use (
            $ownerType,
            $ownerId,
            $amount,
            $method,
            $accountName,
            $accountReference
        ): FinancePayoutRequest {
            $wallet = $this->wallets->wallet($ownerType, $ownerId);

            $request = FinancePayoutRequest::query()->create([
                'finance_wallet_id' => $wallet->id,
                'request_code' => $this->requestCode(),
                'owner_type' => $ownerType,
                'owner_id' => $ownerId,
                'amount' => $amount,
                'currency' => 'PHP',
                'payout_method' => $method,
                'destination_label' => $this->maskedLabel($method, $accountReference),
                'destination_details' => [
                    'account_name' => $accountName,
                    'account_reference' => $accountReference,
                ],
                'status' => 'pending',
                'requested_at' => now(),
            ]);

            $this->wallets->reserve(
                $wallet,
                $amount,
                'finance-payout:'.$request->id.':reserve',
                'Funds reserved for payout request '.$request->request_code.'.'
            );

            return $request->fresh('wallet');
        });
    }

    public function approve(FinancePayoutRequest $payout, int $adminId, ?string $note = null): FinancePayoutRequest
    {
        return DB::transaction(function () use ($payout, $adminId, $note): FinancePayoutRequest {
            $locked = FinancePayoutRequest::query()
                ->whereKey($payout->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status !== 'pending') {
                throw ValidationException::withMessages([
                    'payout' => 'Only pending payout requests can be approved.',
                ]);
            }

            $locked->forceFill([
                'status' => 'approved',
                'reviewed_by_admin_id' => $adminId,
                'admin_note' => $note,
                'reviewed_at' => now(),
            ])->save();

            return $locked->fresh();
        });
    }

    public function reject(FinancePayoutRequest $payout, int $adminId, string $note): FinancePayoutRequest
    {
        $note = trim($note);

        if ($note === '') {
            throw ValidationException::withMessages([
                'admin_note' => 'A rejection reason is required.',
            ]);
        }

        return DB::transaction(function () use ($payout, $adminId, $note): FinancePayoutRequest {
            $locked = FinancePayoutRequest::query()
                ->with('wallet')
                ->whereKey($payout->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($locked->status, ['pending', 'approved'], true)) {
                throw ValidationException::withMessages([
                    'payout' => 'This payout request can no longer be rejected.',
                ]);
            }

            $this->wallets->releaseReservation(
                $locked->wallet,
                (float) $locked->amount,
                'finance-payout:'.$locked->id.':release',
                'Reserved funds returned after payout request '.$locked->request_code.' was rejected.'
            );

            $locked->forceFill([
                'status' => 'rejected',
                'reviewed_by_admin_id' => $adminId,
                'admin_note' => $note,
                'reviewed_at' => now(),
            ])->save();

            return $locked->fresh();
        });
    }

    public function markPaid(
        FinancePayoutRequest $payout,
        int $adminId,
        string $externalReference,
        ?string $note = null
    ): FinancePayoutRequest {
        $externalReference = trim($externalReference);

        if ($externalReference === '') {
            throw ValidationException::withMessages([
                'external_reference' => 'An external transfer or cash-release reference is required.',
            ]);
        }

        return DB::transaction(function () use (
            $payout,
            $adminId,
            $externalReference,
            $note
        ): FinancePayoutRequest {
            $locked = FinancePayoutRequest::query()
                ->with('wallet')
                ->whereKey($payout->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status !== 'approved') {
                throw ValidationException::withMessages([
                    'payout' => 'Only approved payout requests can be marked paid.',
                ]);
            }

            $this->wallets->completePayout(
                $locked->wallet,
                (float) $locked->amount,
                'finance-payout:'.$locked->id.':paid',
                'Payout request '.$locked->request_code.' confirmed as externally paid.',
                [
                    'external_reference' => $externalReference,
                    'payout_method' => $locked->payout_method,
                ]
            );

            $locked->forceFill([
                'status' => 'paid',
                'reviewed_by_admin_id' => $adminId,
                'external_reference' => $externalReference,
                'admin_note' => $note ?: $locked->admin_note,
                'reviewed_at' => $locked->reviewed_at ?: now(),
                'paid_at' => now(),
            ])->save();

            return $locked->fresh();
        });
    }

    private function requestCode(): string
    {
        do {
            $code = 'PAY-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
        } while (FinancePayoutRequest::query()->where('request_code', $code)->exists());

        return $code;
    }

    private function maskedLabel(string $method, string $reference): string
    {
        $clean = preg_replace('/\s+/', '', $reference) ?: $reference;
        $lastFour = mb_substr($clean, -4);

        return strtoupper(str_replace('_', ' ', $method)).' ••••'.$lastFour;
    }
}
