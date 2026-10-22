<?php

namespace App\Services\Registration;

use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Accounts\SocialAccount;
use App\Models\Registration\RegistrationApplication;

class AccountRegistrationPolicy
{
    public function normalizeEmail(?string $email): string
    {
        return mb_strtolower(trim((string) $email));
    }

    public function normalizeMobile(?string $mobile): string
    {
        $mobile = preg_replace('/\s+/', '', trim((string) $mobile)) ?? '';

        if (preg_match('/^\+639(\d{9})$/', $mobile, $matches)) {
            return '09' . $matches[1];
        }

        return $mobile;
    }

    public function emailInUse(string $email, ?int $exceptApplicationId = null): bool
    {
        $email = $this->normalizeEmail($email);

        foreach ([AdminAccount::class, BuyerAccount::class, CourierAccount::class, LogisticsAccount::class, SellerAccount::class, SocialAccount::class] as $model) {
            if ($model::query()->whereRaw('LOWER(email) = ?', [$email])->exists()) {
                return true;
            }
        }

        return RegistrationApplication::query()
            ->when($exceptApplicationId, fn ($q) => $q->whereKeyNot($exceptApplicationId))
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereIn('status', ['unverified', 'pending', 'approved'])
            ->exists();
    }

    public function mobileInUse(string $mobile, ?int $exceptApplicationId = null): bool
    {
        $mobile = $this->normalizeMobile($mobile);

        foreach ([BuyerAccount::class, CourierAccount::class, LogisticsAccount::class, SellerAccount::class] as $model) {
            if ($model::query()->where('contact_no', $mobile)->exists()) {
                return true;
            }
        }

        return RegistrationApplication::query()
            ->when($exceptApplicationId, fn ($q) => $q->whereKeyNot($exceptApplicationId))
            ->where('contact_no', $mobile)
            ->whereIn('status', ['unverified', 'pending', 'approved'])
            ->exists();
    }

    public function tinInUse(string $tin, ?int $exceptApplicationId = null): bool
    {
        return RegistrationApplication::query()
            ->when($exceptApplicationId, fn ($q) => $q->whereKeyNot($exceptApplicationId))
            ->whereIn('role', ['seller', 'logistics'])
            ->where('tin', $tin)
            ->whereIn('status', ['unverified', 'pending', 'approved'])
            ->exists()
            || SellerAccount::query()->where('tin', $tin)->exists()
            || LogisticsAccount::query()->where('tin', $tin)->exists();
    }
}
