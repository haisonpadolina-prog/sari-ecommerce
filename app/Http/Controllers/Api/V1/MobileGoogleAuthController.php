<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Services\Accounts\AccountEmailBanService;
use App\Services\Accounts\GoogleMobileIdTokenVerifier;
use App\Services\Mobile\MobileBuyerActionLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Stateless mobile Google sign-in; only active, already registered accounts. */
class MobileGoogleAuthController extends Controller
{
    public function login(
        Request $request,
        GoogleMobileIdTokenVerifier $google,
        AccountEmailBanService $emailBans,
    ): JsonResponse {
        $input = $request->validate([
            'id_token' => ['required', 'string', 'max:8192'],
            'device_name' => ['required', 'string', 'min:2', 'max:80'],
        ]);

        if (!config('services.google.mobile_server_client_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Google sign-in is not configured on the server.',
            ], 503);
        }

        $identity = $google->verify($input['id_token']);
        if ($identity === null) {
            return $this->denied();
        }

        $email = $identity['email'];
        if ($emailBans->isBanned($email)) {
            return $this->denied();
        }

        // Resolve against ALL role tables; reject ambiguous legacy duplicates.
        // Seller is intentionally not a mobile-app role, but must still be counted.
        $matches = [];
        foreach ([
            'buyer' => BuyerAccount::class,
            'rider' => CourierAccount::class,
            'logistics' => LogisticsAccount::class,
            'seller' => SellerAccount::class,
        ] as $role => $modelClass) {
            $account = $modelClass::query()->where('email', $email)->first();
            if ($account) {
                $matches[$role] = $account;
            }
        }

        if (count($matches) !== 1) {
            return $this->denied();
        }

        $role = array_key_first($matches);
        if ($role === 'seller') {
            // Seller Google login remains available on Laravel WEB only.
            return $this->denied();
        }

        $account = $matches[$role];
        if (strtolower((string) ($account->account_status ?? '')) !== 'active') {
            return $this->denied();
        }

        if ($role === 'rider' && $account->logistics_account_id
            && strtolower((string) ($account->logistics?->account_status ?? '')) !== 'active') {
            return $this->denied();
        }

        // No account creation and no web session mutation. Sanctum bearer token
        // has the same shape and abilities as /api/v1/auth/login.
        $issued = $account->createToken(
            'sari-flutter:'.trim($input['device_name']),
            ['mobile:'.$role],
            now()->addDays(30),
        );

        $name = trim((string) ($account->first_name ?? '').' '.(string) ($account->last_name ?? ''));
        if ($role === 'logistics') {
            $name = $account->displayName();
        }

        if ($account instanceof BuyerAccount) {
            app(MobileBuyerActionLogger::class)->record($account, 'login');
        }

        return response()->json([
            'success' => true,
            'data' => [
                'token_type' => 'Bearer',
                'access_token' => $issued->plainTextToken,
                'expires_at' => $issued->accessToken->expires_at?->toIso8601String(),
                'user' => [
                    'id' => (int) $account->getKey(),
                    'role' => $role,
                    'name' => $name !== '' ? $name : 'SARI Account',
                    'email' => (string) $account->email,
                    'account_status' => (string) $account->account_status,
                    'store_name' => null,
                    'contact_number' => (string) ($account->contact_no ?? ''),
                ],
            ],
        ])->header('Cache-Control', 'no-store');
    }

    private function denied(): JsonResponse
    {
        // Do not disclose whether an email is registered, banned or suspended.
        return response()->json([
            'success' => false,
            'message' => 'Google sign-in is unavailable for this account. Check your registration status or use email sign-in.',
        ], 401)->header('Cache-Control', 'no-store');
    }
}
