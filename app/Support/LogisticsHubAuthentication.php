<?php

namespace App\Support;

use App\Models\Accounts\LogisticsHubAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

final class LogisticsHubAuthentication
{
    public static function attempt(Request $request, string $email): ?RedirectResponse
    {
        $email = strtolower(trim($email));

        $hubAccount = LogisticsHubAccount::query()
            ->with(['logisticsAccount', 'facility'])
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (! $hubAccount) {
            return null;
        }

        // Preserve the existing login flow for an invalid password.
        if (! Hash::check(
            (string) $request->input('password'),
            (string) $hubAccount->password
        )) {
            return null;
        }

        if (
            $hubAccount->role !== 'hub_operator'
            || $hubAccount->account_status !== 'active'
        ) {
            return back()->withErrors([
                'email' => 'This Hub Operator account is not currently active or eligible.',
            ])->onlyInput('email');
        }

        $logistics = $hubAccount->logisticsAccount;

        if (! $logistics || $logistics->account_status !== 'active') {
            return back()->withErrors([
                'email' => 'The parent Logistics account is not currently active.',
            ])->onlyInput('email');
        }

        $facility = $hubAccount->facility;

        if (
            ! $facility
            || (int) $hubAccount->logistics_facility_id !== (int) $facility->id
            || (int) $hubAccount->logistics_account_id !== (int) $logistics->id
            || (int) $facility->logistics_account_id !== (int) $logistics->id
        ) {
            return back()->withErrors([
                'email' => 'The assigned hub is missing or does not belong to this Logistics network.',
            ])->onlyInput('email');
        }

        if (! $facility->is_active) {
            return back()->withErrors([
                'email' => 'The assigned Logistics hub is currently inactive.',
            ])->onlyInput('email');
        }

        /*
        |--------------------------------------------------------------------------
        | Successful, verified Hub Operator login
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | Clear old role and identity markers
        |--------------------------------------------------------------------------
        */

        $request->session()->forget([
            'is_admin',
            'admin_account_id',

            'is_seller',
            'seller_account_id',

            'is_buyer',
            'buyer_account_id',
            'buyer_social_account_id',
            'buyer_email',
            'buyer_name',
            'buyer_avatar',

            'is_courier',
            'courier_account_id',
            'courier_email',
            'courier_name',

            'is_logistics',
            'logistics_account_id',
            'logistics_email',
            'logistics_name',
            'logistics_role',

            'is_logistics_hub_operator',
            'logistics_hub_account_id',
            'logistics_facility_id',
            'logistics_hub_name',
            'logistics_hub_email',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Write consistent Logistics / Hub identity
        |--------------------------------------------------------------------------
        |
        | logistics_account_id is the parent provider.
        | logistics_hub_account_id is the individual operator.
        | logistics_facility_id is the assigned municipality hub.
        |
        */

        $request->session()->put([
            'is_logistics' => true,
            'logistics_role' => 'hub_operator',

            'logistics_account_id' => $logistics->id,
            'logistics_email' => $hubAccount->email,
            'logistics_name' => $hubAccount->name,

            'is_logistics_hub_operator' => true,
            'logistics_hub_account_id' => $hubAccount->id,
            'logistics_facility_id' => $facility->id,
            'logistics_hub_name' => $facility->name,
            'logistics_hub_email' => $hubAccount->email,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update successful login timestamp
        |--------------------------------------------------------------------------
        */

        $hubAccount->forceFill([
            'last_login_at' => now(),
        ])->save();

        return redirect()->route('logistics.dashboard');
    }
}
