<?php

namespace App\Services\Finance;

use App\Models\Finance\FinanceWallet;
use App\Models\Finance\FinanceWalletEntry;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WalletService
{
    public const PLATFORM_OWNER_TYPE = 'platform';
    public const PLATFORM_OWNER_ID = 1;

    public function wallet(string $ownerType, int $ownerId): FinanceWallet
    {
        return FinanceWallet::query()->firstOrCreate(
            [
                'owner_type' => $ownerType,
                'owner_id' => $ownerId,
                'currency' => 'PHP',
            ],
            [
                'available_balance' => 0,
                'reserved_balance' => 0,
                'lifetime_credits' => 0,
                'lifetime_payouts' => 0,
            ]
        );
    }

    public function platformWallet(): FinanceWallet
    {
        return $this->wallet(self::PLATFORM_OWNER_TYPE, self::PLATFORM_OWNER_ID);
    }

    public function credit(
        FinanceWallet $wallet,
        float $amount,
        string $category,
        string $idempotencyKey,
        ?MarketplaceOrder $order = null,
        ?string $sourceType = null,
        ?int $sourceId = null,
        ?string $description = null,
        array $metadata = []
    ): FinanceWalletEntry {
        $amount = round($amount, 2);

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Wallet credit amount must be greater than zero.',
            ]);
        }

        return DB::transaction(function () use (
            $wallet,
            $amount,
            $category,
            $idempotencyKey,
            $order,
            $sourceType,
            $sourceId,
            $description,
            $metadata
        ): FinanceWalletEntry {
            $existing = FinanceWalletEntry::query()
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                return $existing;
            }

            $locked = FinanceWallet::query()
                ->whereKey($wallet->id)
                ->lockForUpdate()
                ->firstOrFail();

            $available = round((float) $locked->available_balance + $amount, 2);

            $locked->forceFill([
                'available_balance' => $available,
                'lifetime_credits' => round((float) $locked->lifetime_credits + $amount, 2),
            ])->save();

            return FinanceWalletEntry::query()->create([
                'finance_wallet_id' => $locked->id,
                'marketplace_order_id' => $order?->id,
                'idempotency_key' => $idempotencyKey,
                'entry_type' => 'credit',
                'category' => $category,
                'amount' => $amount,
                'available_delta' => $amount,
                'reserved_delta' => 0,
                'available_balance_after' => $available,
                'reserved_balance_after' => (float) $locked->reserved_balance,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'description' => $description,
                'metadata' => $metadata ?: null,
                'occurred_at' => now(),
            ]);
        });
    }

    public function reserve(
        FinanceWallet $wallet,
        float $amount,
        string $idempotencyKey,
        string $description
    ): FinanceWalletEntry {
        return $this->move(
            $wallet,
            $amount,
            $idempotencyKey,
            'reserve',
            'payout_reserve',
            -1,
            1,
            $description
        );
    }

    public function releaseReservation(
        FinanceWallet $wallet,
        float $amount,
        string $idempotencyKey,
        string $description
    ): FinanceWalletEntry {
        return $this->move(
            $wallet,
            $amount,
            $idempotencyKey,
            'release',
            'payout_release',
            1,
            -1,
            $description
        );
    }

    public function completePayout(
        FinanceWallet $wallet,
        float $amount,
        string $idempotencyKey,
        string $description,
        array $metadata = []
    ): FinanceWalletEntry {
        $amount = round($amount, 2);

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Payout amount must be greater than zero.',
            ]);
        }

        return DB::transaction(function () use (
            $wallet,
            $amount,
            $idempotencyKey,
            $description,
            $metadata
        ): FinanceWalletEntry {
            $existing = FinanceWalletEntry::query()
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                return $existing;
            }

            $locked = FinanceWallet::query()
                ->whereKey($wallet->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((float) $locked->reserved_balance + 0.0001 < $amount) {
                throw ValidationException::withMessages([
                    'payout' => 'Reserved wallet balance is insufficient for this payout.',
                ]);
            }

            $reserved = round((float) $locked->reserved_balance - $amount, 2);

            $locked->forceFill([
                'reserved_balance' => $reserved,
                'lifetime_payouts' => round((float) $locked->lifetime_payouts + $amount, 2),
            ])->save();

            return FinanceWalletEntry::query()->create([
                'finance_wallet_id' => $locked->id,
                'marketplace_order_id' => null,
                'idempotency_key' => $idempotencyKey,
                'entry_type' => 'payout',
                'category' => 'payout_paid',
                'amount' => $amount,
                'available_delta' => 0,
                'reserved_delta' => -$amount,
                'available_balance_after' => (float) $locked->available_balance,
                'reserved_balance_after' => $reserved,
                'description' => $description,
                'metadata' => $metadata ?: null,
                'occurred_at' => now(),
            ]);
        });
    }

    private function move(
        FinanceWallet $wallet,
        float $amount,
        string $idempotencyKey,
        string $entryType,
        string $category,
        int $availableDirection,
        int $reservedDirection,
        string $description
    ): FinanceWalletEntry {
        $amount = round($amount, 2);

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Wallet movement amount must be greater than zero.',
            ]);
        }

        return DB::transaction(function () use (
            $wallet,
            $amount,
            $idempotencyKey,
            $entryType,
            $category,
            $availableDirection,
            $reservedDirection,
            $description
        ): FinanceWalletEntry {
            $existing = FinanceWalletEntry::query()
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                return $existing;
            }

            $locked = FinanceWallet::query()
                ->whereKey($wallet->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($availableDirection < 0 && (float) $locked->available_balance + 0.0001 < $amount) {
                throw ValidationException::withMessages([
                    'payout' => 'Available wallet balance is insufficient.',
                ]);
            }

            if ($reservedDirection < 0 && (float) $locked->reserved_balance + 0.0001 < $amount) {
                throw ValidationException::withMessages([
                    'payout' => 'Reserved wallet balance is insufficient.',
                ]);
            }

            $availableDelta = round($amount * $availableDirection, 2);
            $reservedDelta = round($amount * $reservedDirection, 2);
            $available = round((float) $locked->available_balance + $availableDelta, 2);
            $reserved = round((float) $locked->reserved_balance + $reservedDelta, 2);

            $locked->forceFill([
                'available_balance' => $available,
                'reserved_balance' => $reserved,
            ])->save();

            return FinanceWalletEntry::query()->create([
                'finance_wallet_id' => $locked->id,
                'marketplace_order_id' => null,
                'idempotency_key' => $idempotencyKey,
                'entry_type' => $entryType,
                'category' => $category,
                'amount' => $amount,
                'available_delta' => $availableDelta,
                'reserved_delta' => $reservedDelta,
                'available_balance_after' => $available,
                'reserved_balance_after' => $reserved,
                'description' => $description,
                'occurred_at' => now(),
            ]);
        });
    }
}
