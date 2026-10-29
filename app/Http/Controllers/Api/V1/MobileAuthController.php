<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Services\Accounts\AccountEmailBanService;
use App\Services\Seller\SellerAccountStatusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MobileAuthController extends Controller
{
    public function login(Request $request, AccountEmailBanService $emailBans, SellerAccountStatusService $sellerStatus): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['buyer', 'seller', 'rider', 'logistics'])],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'min:2', 'max:80'],
        ]);

        $role = $validated['role'];
        $email = strtolower(trim($validated['email']));
        $accountClass = match ($role) {
            'buyer' => BuyerAccount::class,
            'seller' => SellerAccount::class,
            'rider' => CourierAccount::class,
            'logistics' => LogisticsAccount::class,
        };

        $account = $accountClass::query()->where('email', $email)->first();
        if (!$account || !$account->password || !Hash::check($validated['password'], (string) $account->password)
            || $emailBans->isBanned($email)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials or inaccessible account.'], 401);
        }

        if ($account instanceof SellerAccount) {
            $account = $sellerStatus->refresh($account);
            $canLogin = ($account->registration_status ?? 'approved') === 'approved'
                && !in_array(strtolower((string) ($account->account_status ?: 'active')),
                    ['banned', 'deactivated', 'blocked'], true);
        } else {
            $canLogin = strtolower((string) ($account->account_status ?? '')) === 'active';
        }

        if (!$canLogin) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials or inaccessible account.'], 401);
        }

        // A new mobile login doesn't create or mutate any browser session.
        $issued = $account->createToken(
            'sari-flutter:'.trim($validated['device_name']),
            ['mobile:'.$role],
            now()->addDays(30)
        );

        return response()->json([
            'success' => true,
            'data' => [
                'token_type' => 'Bearer',
                'access_token' => $issued->plainTextToken,
                'expires_at' => $issued->accessToken->expires_at?->toIso8601String(),
                'user' => $this->profile($account, $role),
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $account = $request->user();

        return response()->json([
            'success' => true,
            'data' => ['user' => $this->profile($account, $this->roleFor($account))],
        ])->header('Cache-Control', 'no-store');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out from this device.',
        ]);
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->where('name', 'like', 'sari-flutter:%')->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mobile sessions revoked.',
        ]);
    }

    private function roleFor(BuyerAccount|SellerAccount|CourierAccount|LogisticsAccount $account): string
    {
        return match (true) {
            $account instanceof BuyerAccount => 'buyer',
            $account instanceof SellerAccount => 'seller',
            $account instanceof CourierAccount => 'rider',
            default => 'logistics',
        };
    }

    private function profile(BuyerAccount|SellerAccount|CourierAccount|LogisticsAccount $account, string $role): array
    {
        $displayName = trim((string) ($account->first_name ?? '').' '.(string) ($account->last_name ?? ''));
        if ($role === 'seller') {
            $displayName = trim((string) ($account->store_name ?: $displayName));
        } elseif ($role === 'logistics' && method_exists($account, 'displayName')) {
            $displayName = $account->displayName();
        }

        return [
            'id' => (int) $account->getKey(),
            'role' => $role,
            'name' => $displayName !== '' ? $displayName : 'SARI Account',
            'email' => (string) $account->email,
            'account_status' => (string) ($account->account_status ?? 'active'),
            'store_name' => $role === 'seller' ? (string) ($account->store_name ?? '') : null,
            'contact_number' => (string) ($account->contact_no ?? ''),
        ];
    }
}
