<?php

namespace App\Support;

use App\Models\Accounts\LogisticsAccount;
use Illuminate\Http\Request;

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
}
