<?php

namespace App\Support;

use App\Models\LogisticsAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

final class CurrentLogisticsAccount
{
    public static function resolve(Request $request): LogisticsAccount
    {
        $id = (int) $request->session()->get('logistics_account_id', 0);
        $email = strtolower(trim((string) $request->session()->get('logistics_email', '')));

        $logistics = $id > 0
            ? LogisticsAccount::query()->find($id)
            : null;

        if (!$logistics && $email !== '') {
            $logistics = LogisticsAccount::query()->where('email', $email)->first();
        }

        if (
            !$logistics
            && $email === 'logistics@gmail.com'
            && app()->environment(['local', 'testing'])
        ) {
            $logistics = self::developmentProvider();
        }

        abort_unless(
            $logistics && $logistics->account_status === 'active',
            403,
            'A database-backed active Logistics account is required.'
        );

        $request->session()->put('logistics_account_id', $logistics->id);
        $request->session()->put('logistics_email', $logistics->email);
        $request->session()->put('logistics_name', $logistics->displayName());

        return $logistics;
    }

    public static function ensureDevelopmentProvider(): ?LogisticsAccount
    {
        if (!app()->environment(['local', 'testing'])) {
            return null;
        }

        if (LogisticsAccount::query()->where('account_status', 'active')->exists()) {
            return LogisticsAccount::query()
                ->where('account_status', 'active')
                ->oldest('id')
                ->first();
        }

        return self::developmentProvider();
    }

    private static function developmentProvider(): LogisticsAccount
    {
        return LogisticsAccount::query()->firstOrCreate(
            ['email' => 'logistics@gmail.com'],
            [
                'registration_application_id' => null,
                'last_name' => 'Logistics',
                'first_name' => 'SARI',
                'middle_initial' => null,
                'sex' => 'N/A',
                'contact_no' => '09000000000',
                'birthday' => '2000-01-01',
                'age' => 26,
                'province_code' => 'DEV',
                'province_name' => 'Philippines',
                'municipality_code' => 'DEV',
                'municipality_name' => 'SARI Network',
                'barangay_code' => 'DEV',
                'barangay_name' => 'Development',
                'street_address' => 'Local development Logistics provider',
                'business_name' => 'SARI Logistics Development Center',
                'password' => Hash::make('logistics123'),
                'id_path' => null,
                'business_permit_path' => null,
                'account_status' => 'active',
                'approved_at' => now(),
            ]
        );
    }
}
