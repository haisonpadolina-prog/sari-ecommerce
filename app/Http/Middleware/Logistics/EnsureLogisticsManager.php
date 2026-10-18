<?php

namespace App\Http\Middleware\Logistics;

use App\Models\Accounts\LogisticsAccount;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLogisticsManager
{
    public function handle(Request $request, Closure $next): Response
    {
        /*
        |--------------------------------------------------------------------------
        | Require an authenticated Logistics session
        |--------------------------------------------------------------------------
        |
        | This is intentionally checked again even though the route group
        | already runs EnsureLogisticsAuthenticated.
        |
        */

        abort_unless(
            (bool) $request->session()->get('is_logistics', false),
            403,
            'A Logistics Manager account is required.'
        );

        /*
        |--------------------------------------------------------------------------
        | Never treat a Hub Operator as a Manager
        |--------------------------------------------------------------------------
        |
        | Check the presence of every Hub identity key, not only the boolean.
        | Even an incomplete or stale Hub session must not gain Manager access.
        |
        */

        $hubIdentityKeys = [
            'is_logistics_hub_operator',
            'logistics_hub_account_id',
            'logistics_facility_id',
            'logistics_hub_name',
            'logistics_hub_email',
        ];

        foreach ($hubIdentityKeys as $key) {
            abort_if(
                $request->session()->has($key),
                403,
                'This page is available only to the Logistics Manager.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Allow a future explicit role without breaking existing Manager logins
        |--------------------------------------------------------------------------
        |
        | The current login flow does not write logistics_role yet.
        | Once it is introduced, only the manager value is permitted here.
        |
        */

        if ($request->session()->has('logistics_role')) {
            abort_unless(
                $request->session()->get('logistics_role') === 'manager',
                403,
                'This page is available only to the Logistics Manager.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Revalidate a real, active provider identity
        |--------------------------------------------------------------------------
        |
        | Do not use an email-only fallback or auto-create a development
        | account inside a protected Manager request.
        |
        */

        $providerId = (int) $request->session()->get(
            'logistics_account_id',
            0
        );

        abort_if(
            $providerId <= 0,
            403,
            'A database-backed Logistics Manager account is required.'
        );

        $provider = LogisticsAccount::query()
            ->whereKey($providerId)
            ->where('account_status', 'active')
            ->first();

        abort_unless(
            $provider,
            403,
            'This Logistics Manager account is unavailable.'
        );

        $sessionEmail = strtolower(trim(
            (string) $request->session()->get('logistics_email', '')
        ));

        $providerEmail = strtolower(trim((string) $provider->email));

        abort_unless(
            $sessionEmail !== ''
                && $providerEmail !== ''
                && $sessionEmail === $providerEmail,
            403,
            'Your Logistics Manager identity could not be verified.'
        );

        return $next($request);
    }
}
