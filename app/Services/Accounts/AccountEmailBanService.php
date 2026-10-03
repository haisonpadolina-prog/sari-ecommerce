<?php

namespace App\Services\Accounts;

use App\Models\Accounts\AccountEmailBan;

class AccountEmailBanService
{
    public function normalize(string $email): string
    {
        return strtolower(trim($email));
    }

    public function isBanned(string $email): bool
    {
        $email = $this->normalize($email);

        return $email !== '' && AccountEmailBan::query()
            ->where('email', $email)
            ->whereNull('unbanned_at')
            ->exists();
    }

    public function ban(string $email, ?string $role, ?int $accountId, ?int $adminId, ?string $reason): AccountEmailBan
    {
        $email = $this->normalize($email);

        return AccountEmailBan::query()->updateOrCreate(
            ['email' => $email],
            [
                'role' => $role,
                'account_id' => $accountId,
                'banned_by_admin_account_id' => $adminId,
                'reason' => $reason,
                'banned_at' => now(),
                'unbanned_at' => null,
            ]
        );
    }

    public function unban(string $email): void
    {
        AccountEmailBan::query()
            ->where('email', $this->normalize($email))
            ->whereNull('unbanned_at')
            ->update(['unbanned_at' => now()]);
    }
}
