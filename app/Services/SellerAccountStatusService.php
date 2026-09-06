<?php

namespace App\Services;

use App\Models\SellerAccount;
use App\Models\SellerProduct;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SellerAccountStatusService
{
    public function refresh(SellerAccount $seller): SellerAccount
    {
        $status = (string) ($seller->account_status ?: 'active');

        // Ban/deactivation are permanent until an Admin explicitly changes them.
        if (in_array($status, ['banned', 'deactivated'], true)) {
            return $seller;
        }

        $expired = false;

        if ($seller->suspended_until) {
            $until = $seller->suspended_until instanceof \DateTimeInterface
                ? Carbon::instance($seller->suspended_until)
                : Carbon::parse($seller->suspended_until);

            $expired = now()->greaterThanOrEqualTo($until);
        }

        if ($expired) {
            $seller->forceFill([
                'warning_count' => 0,
                'suspended_until' => null,
                'suspension_reason' => null,
            ])->save();

            return $seller->refresh();
        }

        /*
        | Compatibility fallback:
        | If an older suspension-expiry routine already cleared the date/reason
        | but left warning_count stuck at 3, normalize it to 0.
        */
        if (
            (int) ($seller->warning_count ?? 0) >= 3
            && !$seller->suspended_until
            && !$seller->suspension_reason
        ) {
            $seller->forceFill([
                'warning_count' => 0,
            ])->save();

            return $seller->refresh();
        }

        return $seller;
    }

    public function status(SellerAccount $seller): string
    {
        return (string) ($seller->account_status ?: 'active');
    }

    public function isBanned(SellerAccount $seller): bool
    {
        return $this->status($seller) === 'banned';
    }

    public function isDeactivated(SellerAccount $seller): bool
    {
        return $this->status($seller) === 'deactivated';
    }

    public function isTerminated(SellerAccount $seller): bool
    {
        return $this->isBanned($seller) || $this->isDeactivated($seller);
    }

    public function isSuspended(SellerAccount $seller): bool
    {
        if (!$seller->suspended_until) {
            return false;
        }

        $until = $seller->suspended_until instanceof \DateTimeInterface
            ? Carbon::instance($seller->suspended_until)
            : Carbon::parse($seller->suspended_until);

        return now()->lessThan($until);
    }

    public function isSellingRestricted(SellerAccount $seller): bool
    {
        $seller = $this->refresh($seller);

        return $this->isTerminated($seller)
            || (int) ($seller->warning_count ?? 0) >= 3
            || $this->isSuspended($seller);
    }

    public function suspend30(
        SellerAccount $seller,
        ?string $reason = null
    ): SellerAccount {
        $seller->forceFill([
            'account_status' => 'active',
            'suspended_until' => now()->addDays(30),
            'suspension_reason' => $reason
                ?: 'Manual 30-day suspension issued by SARI Administrator.',
        ])->save();

        return $seller->refresh();
    }

    public function liftSuspension(SellerAccount $seller): SellerAccount
    {
        $seller->forceFill([
            'warning_count' => 0,
            'suspended_until' => null,
            'suspension_reason' => null,
        ])->save();

        return $seller->refresh();
    }

    public function ban(
        SellerAccount $seller,
        string $reason
    ): SellerAccount {
        return DB::transaction(function () use ($seller, $reason) {
            $seller->forceFill([
                'account_status' => 'banned',
                'banned_at' => now(),
                'ban_reason' => $reason,
                'deactivated_at' => null,
                'deactivation_reason' => null,
                'suspended_until' => null,
                'suspension_reason' => null,
            ])->save();

            // A permanently banned seller should not keep active public listings.
            SellerProduct::query()
                ->where('seller_account_id', $seller->id)
                ->whereNull('archived_at')
                ->update([
                    'moderation_status' => 'removed',
                    'admin_review_note' => 'Listing removed because the seller account was banned. Reason: ' . $reason,
                    'reviewed_at' => now(),
                ]);

            return $seller->refresh();
        });
    }

    public function unban(SellerAccount $seller): SellerAccount
    {
        $seller->forceFill([
            'account_status' => 'active',
            'warning_count' => 0,
            'banned_at' => null,
            'ban_reason' => null,
            'suspended_until' => null,
            'suspension_reason' => null,
        ])->save();

        /*
        | Removed products are NOT auto-approved again.
        | Admin can explicitly review/approve any listing that should return.
        */

        return $seller->refresh();
    }

    public function deactivate(
        SellerAccount $seller,
        string $reason
    ): SellerAccount {
        return DB::transaction(function () use ($seller, $reason) {
            $seller->forceFill([
                'account_status' => 'deactivated',
                'deactivated_at' => now(),
                'deactivation_reason' => $reason,
                'suspended_until' => null,
                'suspension_reason' => null,
            ])->save();

            SellerProduct::query()
                ->where('seller_account_id', $seller->id)
                ->whereNull('archived_at')
                ->update([
                    'moderation_status' => 'removed',
                    'admin_review_note' => 'Listing removed because the seller account was deactivated. Reason: ' . $reason,
                    'reviewed_at' => now(),
                ]);

            return $seller->refresh();
        });
    }

    public function restore(SellerAccount $seller): SellerAccount
    {
        $seller->forceFill([
            'account_status' => 'active',
            'warning_count' => 0,
            'banned_at' => null,
            'ban_reason' => null,
            'deactivated_at' => null,
            'deactivation_reason' => null,
            'suspended_until' => null,
            'suspension_reason' => null,
        ])->save();

        // Products remain removed until Admin explicitly reviews them again.
        return $seller->refresh();
    }
}
