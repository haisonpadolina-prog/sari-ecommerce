<?php

namespace App\Support;

use App\Models\Accounts\LogisticsHubAccount;
use App\Services\Logistics\LogisticsActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Throwable;

final class LogisticsHubAuthentication
{
    public static function attempt(
        Request $request,
        string $email
    ): ?RedirectResponse {

        $email = strtolower(trim($email));

        /*
        |--------------------------------------------------------------------------
        | Find Hub Operator
        |--------------------------------------------------------------------------
        */

        $hubAccount = LogisticsHubAccount::query()
            ->with([
                'logisticsAccount',
                'facility',
            ])
            ->whereRaw(
                'LOWER(email) = ?',
                [$email]
            )
            ->first();

        if (! $hubAccount) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Password
        |--------------------------------------------------------------------------
        */

        if (! Hash::check(
            (string) $request->input('password'),
            (string) $hubAccount->password
        )) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Operator Status
        |--------------------------------------------------------------------------
        */

        if (
            $hubAccount->role !== 'hub_operator'
            || $hubAccount->account_status !== 'active'
        ) {
            return back()->withErrors([
                'email' =>
                    'This Hub Operator account is not currently active or eligible.',
            ])->onlyInput('email');
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Parent Logistics Provider
        |--------------------------------------------------------------------------
        */

        $logistics = $hubAccount->logisticsAccount;

        if (
            ! $logistics
            || $logistics->account_status !== 'active'
        ) {
            return back()->withErrors([
                'email' =>
                    'The parent Logistics account is not currently active.',
            ])->onlyInput('email');
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Assigned Facility
        |--------------------------------------------------------------------------
        */

        $facility = $hubAccount->facility;

        if (
            ! $facility
            || (int) $hubAccount->logistics_facility_id
                !== (int) $facility->id
            || (int) $hubAccount->logistics_account_id
                !== (int) $logistics->id
            || (int) $facility->logistics_account_id
                !== (int) $logistics->id
        ) {
            return back()->withErrors([
                'email' =>
                    'The assigned hub is missing or does not belong to this Logistics network.',
            ])->onlyInput('email');
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Facility Status
        |--------------------------------------------------------------------------
        */

        if (! $facility->is_active) {
            return back()->withErrors([
                'email' =>
                    'The assigned Logistics hub is currently inactive.',
            ])->onlyInput('email');
        }

        /*
        |--------------------------------------------------------------------------
        | Successful Hub Operator Login
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | Clear Previous Role Identity
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
        | Initialize Hub Operator Session
        |--------------------------------------------------------------------------
        |
        | The parent provider and individual Hub Operator
        | retain separate identities.
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
        | Update Last Login Timestamp
        |--------------------------------------------------------------------------
        */

        $hubAccount->forceFill([
            'last_login_at' => now(),
        ])->save();

        /*
        |--------------------------------------------------------------------------
        | Activity Logs — Successful Login
        |--------------------------------------------------------------------------
        |
        | Record only after:
        |
        | - Password verification
        | - Account status verification
        | - Provider and hub validation
        | - Successful session initialization
        |
        | Actor identity and facility are resolved directly
        | from the authenticated session by the logger.
        |
        | Do not store passwords or submitted credentials.
        |
        */

        try {
            app(LogisticsActivityLogger::class)->record(
                $request,
                'Authentication',
                'login',
                'Hub operator signed in'
            );

        } catch (Throwable $exception) {

            /*
             * Report audit failures without blocking a valid login.
             */

            report($exception);
        }

        /*
        |--------------------------------------------------------------------------
        | Redirect To Logistics Dashboard
        |--------------------------------------------------------------------------
        */

        return redirect()->route('logistics.dashboard');
    }
}
