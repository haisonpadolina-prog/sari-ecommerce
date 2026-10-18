<?php

namespace App\Support;

use App\Models\Accounts\LogisticsAccount;
use Illuminate\Http\Request;

final class CurrentLogisticsAccount
{
    /**
     * Resolve the parent Logistics provider without changing the signed-in
     * user's identity. For a Hub Operator, this returns the provider only so
     * operational queries can retain their provider boundary.
     */
    public static function resolve(Request $request): LogisticsAccount
    {
        $session = $request->session();

        abort_unless(
            (bool) $session->get('is_logistics', false),
            403,
            'A signed-in Logistics account is required.'
        );

        $providerId = (int) $session->get('logistics_account_id', 0);

        abort_unless(
            $providerId > 0,
            403,
            'A database-backed Logistics account is required.'
        );

        // No email lookup or demo-provider fallback in protected requests.
        $provider = LogisticsAccount::query()
            ->whereKey($providerId)
            ->where('account_status', 'active')
            ->first();

        abort_unless(
            $provider,
            403,
            'An active Logistics provider account is required.'
        );

        $role = trim((string) $session->get('logistics_role', ''));
        $isHubOperator = (bool) $session->get(
            'is_logistics_hub_operator',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | Detect Hub Operator identity
        |--------------------------------------------------------------------------
        |
        | Even a stale or false Hub marker must never silently convert
        | an operator's session into a Manager session.
        |
        */

        $hubIdentityKeys = [
            'is_logistics_hub_operator',
            'logistics_hub_account_id',
            'logistics_facility_id',
            'logistics_hub_name',
            'logistics_hub_email',
        ];

        $hasHubIdentity = $role === 'hub_operator';

        foreach ($hubIdentityKeys as $key) {
            if ($session->has($key)) {
                $hasHubIdentity = true;
                break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Hub Operator resolution
        |--------------------------------------------------------------------------
        */

        if ($hasHubIdentity) {
            abort_unless(
                $isHubOperator && ($role === '' || $role === 'hub_operator'),
                403,
                'The Logistics Hub Operator identity is invalid.'
            );

            /*
             * CurrentLogisticsHub validates the active operator, assigned
             * facility, role and relationship to the parent provider.
             */

            $hubAccount = CurrentLogisticsHub::requireAccount($request);
            $facility = $hubAccount->facility;

            abort_unless(
                $facility
                    && (int) $hubAccount->logistics_account_id === (int) $provider->id
                    && (int) $hubAccount->logistics_facility_id === (int) $facility->id
                    && (int) $facility->logistics_account_id === (int) $provider->id,
                403,
                'The assigned Logistics Hub identity is invalid.'
            );

            /*
            |--------------------------------------------------------------------------
            | Verify actual operator email
            |--------------------------------------------------------------------------
            |
            | The provider's email is not the signed-in operator's email.
            |
            */

            $actualHubEmail = strtolower(trim((string) $hubAccount->email));

            $sessionEmail = strtolower(trim(
                (string) $session->get('logistics_email', '')
            ));

            $sessionHubEmail = strtolower(trim(
                (string) $session->get('logistics_hub_email', '')
            ));

            abort_unless(
                $actualHubEmail !== ''
                    && hash_equals($actualHubEmail, $sessionEmail)
                    && hash_equals($actualHubEmail, $sessionHubEmail),
                403,
                'Your Hub Operator session identity could not be verified.'
            );

            /*
            |--------------------------------------------------------------------------
            | Refresh identity using the Hub Operator account only
            |--------------------------------------------------------------------------
            */

            $session->put([
                'logistics_role' => 'hub_operator',
                'logistics_email' => $hubAccount->email,
                'logistics_name' => $hubAccount->name,
                'logistics_hub_name' => $facility->name,
            ]);

            /*
             * Return the parent provider for operational queries.
             * This does not change the signed-in user to Manager.
             */

            return $provider;
        }

        /*
        |--------------------------------------------------------------------------
        | Main Logistics Manager resolution
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $role === '' || $role === 'manager',
            403,
            'This Logistics session has an invalid role.'
        );

        $actualProviderEmail = strtolower(trim((string) $provider->email));

        $sessionEmail = strtolower(trim(
            (string) $session->get('logistics_email', '')
        ));

        abort_unless(
            $actualProviderEmail !== ''
                && hash_equals($actualProviderEmail, $sessionEmail),
            403,
            'Your Logistics Manager session identity could not be verified.'
        );

        /*
         * Refresh Manager identity only when there is no Hub identity.
         */

        $session->put([
            'logistics_role' => 'manager',
            'logistics_email' => $provider->email,
            'logistics_name' => $provider->displayName(),
        ]);

        return $provider;
    }

    /**
     * Retained for RiderRegistrationController compatibility.
     *
     * Visiting public Rider registration must not create an active
     * provider with a known, hardcoded password.
     */
    public static function ensureDevelopmentProvider(): ?LogisticsAccount
    {
        if (! app()->environment(['local', 'testing'])) {
            return null;
        }

        return LogisticsAccount::query()
            ->where('account_status', 'active')
            ->oldest('id')
            ->first();
    }
}
