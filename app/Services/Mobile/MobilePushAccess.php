<?php

namespace App\Services\Mobile;

use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Services\Accounts\AccountEmailBanService;
use Laravel\Sanctum\PersonalAccessToken;

/** Background delivery must enforce account access without an HTTP middleware pipeline. */
final class MobilePushAccess
{
    public function __construct(private readonly AccountEmailBanService $emailBans) {}

    public function allows(object $device): bool
    {
        $token = PersonalAccessToken::query()->find($device->personal_access_token_id);
        if (!$token || ($token->expires_at && $token->expires_at->isPast())) {
            return false;
        }

        $expiration = (int) config('sanctum.expiration', 0);
        if ($expiration > 0 && (!$token->created_at
            || $token->created_at->lte(now()->subMinutes($expiration)))) {
            return false;
        }

        $account = $token->tokenable;
        $role = match (true) {
            $account instanceof BuyerAccount => 'buyer',
            $account instanceof CourierAccount => 'rider',
            $account instanceof LogisticsAccount => 'logistics',
            default => null,
        };

        if ($role === null || $role !== $device->account_role
            || (int) $account->id !== (int) $device->account_id
            || !$token->can('mobile:'.$role)
            || strtolower((string) $account->account_status) !== 'active'
            || $this->emailBans->isBanned((string) $account->email)) {
            return false;
        }

        if ($account instanceof CourierAccount && $account->logistics_account_id) {
            $provider = $account->logistics;
            if (!$provider || strtolower((string) $provider->account_status) !== 'active'
                || $this->emailBans->isBanned((string) $provider->email)) {
                return false;
            }
        }

        return true;
    }
}
