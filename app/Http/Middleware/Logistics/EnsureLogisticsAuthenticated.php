<?php

namespace App\Http\Middleware\Logistics;

use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\LogisticsHubAccount;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLogisticsAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        /*
        |--------------------------------------------------------------------------
        | Require Logistics Session
        |--------------------------------------------------------------------------
        */

        if (! (bool) $request->session()->get('is_logistics', false)) {
            return $this->denyUnauthenticated($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Parent Logistics Provider
        |--------------------------------------------------------------------------
        |
        | Every Logistics session must identify a real database-backed
        | provider. Do not grant access using an email-only fallback.
        |
        */

        $providerId = (int) $request->session()->get(
            'logistics_account_id',
            0
        );

        if ($providerId <= 0) {
            return $this->revokeInvalidSession($request);
        }

        $provider = LogisticsAccount::query()
            ->whereKey($providerId)
            ->where('account_status', 'active')
            ->first();

        if (! $provider) {
            return $this->revokeInvalidSession($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Detect Hub Operator Identity
        |--------------------------------------------------------------------------
        |
        | Any surviving Hub identity must be validated as a Hub Operator.
        | Never silently reinterpret a partial Hub session as a Manager.
        |
        */

        $isHubOperator = (bool) $request->session()->get(
            'is_logistics_hub_operator',
            false
        );

        $hubAccountId = (int) $request->session()->get(
            'logistics_hub_account_id',
            0
        );

        $facilityId = (int) $request->session()->get(
            'logistics_facility_id',
            0
        );

        $hasHubIdentity = $isHubOperator
            || $hubAccountId > 0
            || $facilityId > 0
            || $request->session()->has('logistics_hub_email')
            || $request->session()->has('logistics_hub_name');

        /*
        |--------------------------------------------------------------------------
        | Revalidate Hub Operator
        |--------------------------------------------------------------------------
        */

        if ($hasHubIdentity) {

            if (
                ! $isHubOperator
                || $hubAccountId <= 0
                || $facilityId <= 0
            ) {
                return $this->revokeInvalidSession($request);
            }

            $hubAccount = LogisticsHubAccount::query()
                ->with('facility')
                ->whereKey($hubAccountId)
                ->where(
                    'logistics_account_id',
                    $provider->id
                )
                ->where(
                    'logistics_facility_id',
                    $facilityId
                )
                ->where('role', 'hub_operator')
                ->where('account_status', 'active')
                ->first();

            $facility = $hubAccount?->facility;

            $sessionHubEmail = strtolower(
                trim(
                    (string) $request->session()->get(
                        'logistics_hub_email',
                        ''
                    )
                )
            );

            /*
            |--------------------------------------------------------------------------
            | Validate Account, Facility and Parent Provider Relationship
            |--------------------------------------------------------------------------
            */

            if (
                ! $hubAccount
                || ! $facility
                || ! $facility->is_active
                || (int) $facility->id !== $facilityId
                || (int) $facility->logistics_account_id
                    !== (int) $provider->id
                || $sessionHubEmail === ''
                || $sessionHubEmail !== strtolower(
                    trim((string) $hubAccount->email)
                )
            ) {
                return $this->revokeInvalidSession($request);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Allow Authenticated Logistics Request
        |--------------------------------------------------------------------------
        */

        return $next($request);
    }

    /*
    |--------------------------------------------------------------------------
    | Missing Authentication
    |--------------------------------------------------------------------------
    */

    private function denyUnauthenticated(Request $request): Response
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Please sign in to your Logistics account.',
            ], 401);
        }

        return redirect()->route('login');
    }

    /*
    |--------------------------------------------------------------------------
    | Invalid or Revoked Logistics Session
    |--------------------------------------------------------------------------
    |
    | Clear the entire invalid session, including any stale Hub markers,
    | instead of removing only the Hub flag.
    |
    */

    private function revokeInvalidSession(Request $request): Response
    {
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' =>
                    'Your Logistics access has changed. Please sign in again.',
            ], 403);
        }

        return redirect()
            ->route('login')
            ->withErrors([
                'email' =>
                    'Your Logistics session is no longer valid. Please sign in again.',
            ]);
    }
}
