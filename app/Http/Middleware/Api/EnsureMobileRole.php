<?php

namespace App\Http\Middleware\Api;

use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Services\Accounts\AccountEmailBanService;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class EnsureMobileRole
{
    public function __construct(private readonly AccountEmailBanService $emailBans)
    {
    }

    /** Ensure the account identity, account status, and token scope all agree. */
    public function handle(Request $request, Closure $next, ?string $requiredRole = null): Response
    {
        $account = $request->user();
        $role = match (true) {
            $account instanceof BuyerAccount => 'buyer',
            $account instanceof SellerAccount => 'seller',
            $account instanceof CourierAccount => 'rider',
            $account instanceof LogisticsAccount => 'logistics',
            default => null,
        };

        // Never let web-session authentication substitute for a mobile bearer token.
        if ($role === null
            || !($account->currentAccessToken() instanceof PersonalAccessToken)
            || !$account->tokenCan('mobile:'.$role)) {
            return response()->json([
                'success' => false,
                'message' => 'A valid mobile token is required.',
            ], 401);
        }

        if ($requiredRole !== null && $requiredRole !== $role) {
            return response()->json([
                'success' => false,
                'message' => 'This account cannot access this endpoint.',
            ], 403);
        }

        if (!$this->isUsable($account, $role)
            || $this->emailBans->isBanned((string) $account->email)) {
            return response()->json([
                'success' => false,
                'message' => 'This SARI account is not currently accessible.',
            ], 403);
        }

        return $next($request);
    }

    private function isUsable(BuyerAccount|SellerAccount|CourierAccount|LogisticsAccount $account, string $role): bool
    {
        if ($role === 'seller') {
            return ($account->registration_status ?? 'approved') === 'approved'
                && !in_array(strtolower((string) ($account->account_status ?: 'active')),
                    ['banned', 'deactivated', 'blocked'], true);
        }

        return strtolower((string) ($account->account_status ?? '')) === 'active';
    }
}
