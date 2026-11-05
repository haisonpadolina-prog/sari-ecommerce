<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Throwable;

class MobilePasswordResetController extends Controller
{
    private const OTP_TTL_SECONDS = 600;
    private const MAX_ATTEMPTS = 5;
    private const RESET_TOKEN_TTL_SECONDS = 600;

    public function sendCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = Str::lower(trim((string) $validated['email']));
        $account = $this->findMobileAccount($email);
        $otp = (string) random_int(100000, 999999);

        Cache::put(
            $this->otpKey($email),
            [
                'otp_hash' => Hash::make($otp),
                'attempts' => 0,
            ],
            now()->addSeconds(self::OTP_TTL_SECONDS)
        );

        // Same response whether the account exists or not.
        if ($account) {
            try {
                Mail::send(
                    'emails.password-reset',
                    [
                        'otp' => $otp,
                        'expiresInMinutes' => (int) (self::OTP_TTL_SECONDS / 60),
                    ],
                    function ($message) use ($email): void {
                        $message
                            ->to($email)
                            ->subject('Your SARI password reset code');
                    }
                );
            } catch (Throwable $exception) {
                Cache::forget($this->otpKey($email));

                Log::error('SARI mobile password reset OTP email failed.', [
                    'email' => $email,
                    'exception' => $exception->getMessage(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'We could not send the verification code right now.',
                ], 503);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'If this email is linked to a SARI account, a verification code has been sent.',
            'expires_in' => self::OTP_TTL_SECONDS,
        ]);
    }

    public function verifyCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'code' => ['required', 'digits:6'],
        ]);

        $email = Str::lower(trim((string) $validated['email']));
        $key = $this->otpKey($email);
        $state = Cache::get($key);

        if (!is_array($state)) {
            return response()->json([
                'success' => false,
                'message' => 'The verification code expired. Request a new code.',
            ], 422);
        }

        $attempts = (int) ($state['attempts'] ?? 0);

        if ($attempts >= self::MAX_ATTEMPTS) {
            Cache::forget($key);

            return response()->json([
                'success' => false,
                'message' => 'Too many incorrect attempts. Request a new code.',
            ], 429);
        }

        if (!Hash::check((string) $validated['code'], (string) ($state['otp_hash'] ?? ''))) {
            $state['attempts'] = $attempts + 1;
            Cache::put($key, $state, now()->addSeconds(self::OTP_TTL_SECONDS));

            return response()->json([
                'success' => false,
                'message' => 'Incorrect verification code.',
                'attempts_remaining' => max(0, self::MAX_ATTEMPTS - (int) $state['attempts']),
            ], 422);
        }

        Cache::forget($key);

        $resetToken = Str::random(64);
        Cache::put(
            $this->tokenKey($resetToken),
            $email,
            now()->addSeconds(self::RESET_TOKEN_TTL_SECONDS)
        );

        return response()->json([
            'success' => true,
            'message' => 'Email verified.',
            'reset_token' => $resetToken,
            'expires_in' => self::RESET_TOKEN_TTL_SECONDS,
        ]);
    }

    public function reset(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'reset_token' => ['required', 'string', 'min:40', 'max:255'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ]);

        $email = Str::lower(trim((string) $validated['email']));
        $resetToken = (string) $validated['reset_token'];
        $tokenKey = $this->tokenKey($resetToken);
        $tokenEmail = Cache::get($tokenKey);

        if (!is_string($tokenEmail) || !hash_equals($tokenEmail, $email)) {
            return response()->json([
                'success' => false,
                'message' => 'The password reset session is invalid or expired.',
            ], 422);
        }

        $account = $this->findMobileAccount($email);

        if (!$account) {
            Cache::forget($tokenKey);

            return response()->json([
                'success' => false,
                'message' => 'The password reset session is invalid or expired.',
            ], 422);
        }

        $account->forceFill([
            'password' => Hash::make((string) $validated['password']),
        ])->save();

        // Revoke existing mobile Sanctum sessions after a password reset.
        $account->tokens()->delete();
        Cache::forget($tokenKey);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully. You can now sign in.',
        ]);
    }

    private function findMobileAccount(string $email): ?Model
    {
        return BuyerAccount::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereNotNull('password')
            ->first()
            ?? CourierAccount::query()
                ->whereRaw('LOWER(email) = ?', [$email])
                ->whereNotNull('password')
                ->first()
            ?? LogisticsAccount::query()->whereRaw('LOWER(email) = ?', [$email])
                ->whereNotNull('password')->first();
    }

    private function otpKey(string $email): string
    {
        return 'sari:mobile-password-reset:otp:'.hash('sha256', $email);
    }

    private function tokenKey(string $token): string
    {
        return 'sari:mobile-password-reset:token:'.hash('sha256', $token);
    }
}


