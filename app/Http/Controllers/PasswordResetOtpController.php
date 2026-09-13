<?php

namespace App\Http\Controllers;

use App\Models\AdminAccount;
use App\Models\BuyerAccount;
use App\Models\CourierAccount;
use App\Models\LogisticsAccount;
use App\Models\SellerAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class PasswordResetOtpController extends Controller
{
    private const SESSION_KEY = 'sari_password_reset_otp';
    private const OTP_TTL_SECONDS = 600;
    private const RESEND_SECONDS = 45;
    private const MAX_ATTEMPTS = 5;
    private const VERIFIED_TTL_SECONDS = 600;

    public function show(Request $request): View
    {
        $state = $this->state($request);

        if ($state && ($state['expires_at'] ?? 0) <= now()->timestamp) {
            $this->clearState($request);
            $state = null;
        }

        $step = 'email';
        $email = '';
        $maskedEmail = '';
        $resendAfter = 0;

        if ($state) {
            $email = (string) ($state['email'] ?? '');
            $maskedEmail = $this->maskEmail($email);
            $resendAfter = max(0, (int) ($state['resend_at'] ?? 0) - now()->timestamp);
            $step = !empty($state['verified']) ? 'password' : 'otp';
        }

        return view('pages.forgot-password', compact(
            'step',
            'email',
            'maskedEmail',
            'resendAfter'
        ));
    }

    public function sendCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = Str::lower(trim($validated['email']));
        $existingState = $this->state($request);

        if (
            $existingState
            && hash_equals((string) ($existingState['email'] ?? ''), $email)
            && (int) ($existingState['resend_at'] ?? 0) > now()->timestamp
        ) {
            $retryAfter = (int) $existingState['resend_at'] - now()->timestamp;

            return response()->json([
                'ok' => false,
                'message' => "You can request another code in {$retryAfter} seconds.",
                'retry_after' => $retryAfter,
            ], 429);
        }

        $account = $this->findPasswordAccount($email);
        $otp = (string) random_int(100000, 999999);

        $state = [
            'email' => $email,
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addSeconds(self::OTP_TTL_SECONDS)->timestamp,
            'resend_at' => now()->addSeconds(self::RESEND_SECONDS)->timestamp,
            'attempts' => 0,
            'verified' => false,
            'verified_at' => null,
        ];

        $request->session()->put(self::SESSION_KEY, $state);

        // Do not reveal whether an email exists in SARI. Unknown addresses receive
        // the same browser response, but no message is sent.
        if ($account) {
            try {
                Mail::send(
                    'emails.password-reset',
                    [
                        'otp' => $otp,
                        'expiresInMinutes' => (int) (self::OTP_TTL_SECONDS / 60),
                    ],
                    function ($message) use ($email) {
                        $message
                            ->to($email)
                            ->subject('Your SARI password reset code');
                    }
                );
            } catch (Throwable $exception) {
                $this->clearState($request);

                Log::error('SARI password reset OTP email failed.', [
                    'email' => $email,
                    'exception' => $exception->getMessage(),
                ]);

                return response()->json([
                    'ok' => false,
                    'message' => 'We could not send the verification code right now. Please try again in a moment.',
                ], 503);
            }
        }

        return response()->json([
            'ok' => true,
            'message' => 'If this email is linked to a password-based SARI account, a verification code has been sent.',
            'masked_email' => $this->maskEmail($email),
            'resend_after' => self::RESEND_SECONDS,
            'expires_in' => self::OTP_TTL_SECONDS,
        ]);
    }

    public function verifyCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $state = $this->state($request);

        if (!$state) {
            return response()->json([
                'ok' => false,
                'message' => 'Your verification session has ended. Please request a new code.',
                'restart' => true,
            ], 422);
        }

        if ((int) ($state['expires_at'] ?? 0) <= now()->timestamp) {
            $this->clearState($request);

            return response()->json([
                'ok' => false,
                'message' => 'That verification code has expired. Please request a new code.',
                'restart' => true,
            ], 422);
        }

        $attempts = (int) ($state['attempts'] ?? 0);

        if ($attempts >= self::MAX_ATTEMPTS) {
            $this->clearState($request);

            return response()->json([
                'ok' => false,
                'message' => 'Too many incorrect attempts. Please request a new code.',
                'restart' => true,
            ], 429);
        }

        $attempts++;
        $state['attempts'] = $attempts;

        if (!Hash::check((string) $validated['code'], (string) ($state['otp_hash'] ?? ''))) {
            $request->session()->put(self::SESSION_KEY, $state);
            $remaining = max(0, self::MAX_ATTEMPTS - $attempts);

            return response()->json([
                'ok' => false,
                'message' => $remaining > 0
                    ? "Incorrect code. {$remaining} attempt" . ($remaining === 1 ? '' : 's') . ' remaining.'
                    : 'Incorrect code. Please request a new code.',
                'attempts_remaining' => $remaining,
                'restart' => $remaining === 0,
            ], 422);
        }

        $state['verified'] = true;
        $state['verified_at'] = now()->timestamp;
        // Remove the reusable OTP material after successful verification.
        $state['otp_hash'] = null;
        $request->session()->put(self::SESSION_KEY, $state);

        return response()->json([
            'ok' => true,
            'message' => 'Email verified successfully.',
        ]);
    }

    public function reset(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $state = $this->state($request);

        if (
            !$state
            || empty($state['verified'])
            || empty($state['verified_at'])
            || (int) $state['verified_at'] < now()->subSeconds(self::VERIFIED_TTL_SECONDS)->timestamp
        ) {
            $this->clearState($request);

            return response()->json([
                'ok' => false,
                'message' => 'Your verified reset session has expired. Please start again.',
                'restart' => true,
            ], 422);
        }

        $email = (string) ($state['email'] ?? '');
        $account = $this->findPasswordAccount($email);

        if (!$account) {
            $this->clearState($request);

            return response()->json([
                'ok' => false,
                'message' => 'We could not complete this password reset. Please start again.',
                'restart' => true,
            ], 422);
        }

        $account->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        $this->clearState($request);
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return response()->json([
            'ok' => true,
            'message' => 'Your password has been updated successfully.',
            'login_url' => route('login'),
        ]);
    }

    public function restart(Request $request): JsonResponse
    {
        $this->clearState($request);

        return response()->json([
            'ok' => true,
        ]);
    }

    private function state(Request $request): ?array
    {
        $state = $request->session()->get(self::SESSION_KEY);

        return is_array($state) ? $state : null;
    }

    private function clearState(Request $request): void
    {
        $request->session()->forget(self::SESSION_KEY);
    }

    private function findPasswordAccount(string $email): ?Model
    {
        return AdminAccount::query()->where('email', $email)->whereNotNull('password')->first()
            ?? BuyerAccount::query()->where('email', $email)->whereNotNull('password')->first()
            ?? SellerAccount::query()->where('email', $email)->whereNotNull('password')->first()
            ?? CourierAccount::query()->where('email', $email)->whereNotNull('password')->first()
            ?? LogisticsAccount::query()->where('email', $email)->whereNotNull('password')->first();
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');

        if ($local === '' || $domain === '') {
            return $email;
        }

        $visible = mb_substr($local, 0, min(2, mb_strlen($local)));
        $stars = str_repeat('•', max(3, mb_strlen($local) - mb_strlen($visible)));

        return $visible . $stars . '@' . $domain;
    }
}
