<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class RegistrationEmailVerificationService
{
    private const SESSION_KEY = 'registration_email_otp';
    private const EXPIRES_MINUTES = 10;
    private const RESEND_SECONDS = 45;
    private const MAX_ATTEMPTS = 5;
    private const VERIFIED_MINUTES = 20;

    public function sendCode(Request $request, string $email): array
    {
        $email = strtolower(trim($email));

        $mailer = (string) config('mail.default', '');

        if ($mailer === '' || in_array($mailer, ['log', 'array'], true)) {
            return [
                'ok' => false,
                'status' => 503,
                'message' => 'Email OTP is not configured yet. Configure a real Laravel mailer before enabling registration verification.',
            ];
        }

        $current = $request->session()->get(self::SESSION_KEY, []);
        $lastSentAt = isset($current['last_sent_at'])
            ? (int) $current['last_sent_at']
            : 0;

        if (
            ($current['email'] ?? null) === $email
            && $lastSentAt > 0
        ) {
            $secondsSinceLastSend = now()->timestamp - $lastSentAt;
            $remaining = self::RESEND_SECONDS - $secondsSinceLastSend;

            if ($remaining > 0) {
                return [
                    'ok' => false,
                    'status' => 429,
                    'message' => 'Please wait before requesting another verification code.',
                    'retry_after' => $remaining,
                ];
            }
        }

        $code = (string) random_int(100000, 999999);
        $hash = $this->hashOtp($code);
        $expiresAt = now()->addMinutes(self::EXPIRES_MINUTES);

        try {
            Mail::raw(
                "Your SARI email verification code is {$code}.\n\n"
                . 'This code expires in ' . self::EXPIRES_MINUTES . " minutes.\n"
                . "If you did not request this code, you can ignore this email.",
                function ($message) use ($email): void {
                    $message
                        ->to($email)
                        ->subject('SARI registration verification code');
                }
            );
        } catch (Throwable $exception) {
            report($exception);

            return [
                'ok' => false,
                'status' => 503,
                'message' => 'We could not send the verification code right now. Please check the mail configuration and try again.',
            ];
        }

        $request->session()->put(self::SESSION_KEY, [
            'email' => $email,
            'hash' => $hash,
            'expires_at' => $expiresAt->timestamp,
            'last_sent_at' => now()->timestamp,
            'attempts' => 0,
            'verified_at' => null,
        ]);

        return [
            'ok' => true,
            'status' => 200,
            'message' => 'Verification code sent.',
            'masked_email' => $this->maskEmail($email),
            'expires_in' => self::EXPIRES_MINUTES * 60,
            'retry_after' => self::RESEND_SECONDS,
        ];
    }

    public function verifyCode(Request $request, string $email, string $otp): array
    {
        $email = strtolower(trim($email));
        $otp = trim($otp);

        $state = $request->session()->get(self::SESSION_KEY);

        if (!is_array($state) || ($state['email'] ?? null) !== $email) {
            return [
                'ok' => false,
                'status' => 422,
                'message' => 'Request a new verification code for this email address.',
                'errors' => [
                    'otp' => ['Request a new verification code for this email address.'],
                ],
            ];
        }

        if (!empty($state['verified_at'])) {
            return [
                'ok' => true,
                'status' => 200,
                'message' => 'Email already verified.',
                'verified' => true,
            ];
        }

        $expiresAt = (int) ($state['expires_at'] ?? 0);

        if ($expiresAt <= now()->timestamp) {
            $request->session()->forget(self::SESSION_KEY);

            return [
                'ok' => false,
                'status' => 422,
                'message' => 'The verification code has expired. Request a new code.',
                'errors' => [
                    'otp' => ['The verification code has expired. Request a new code.'],
                ],
            ];
        }

        $attempts = (int) ($state['attempts'] ?? 0);

        if ($attempts >= self::MAX_ATTEMPTS) {
            $request->session()->forget(self::SESSION_KEY);

            return [
                'ok' => false,
                'status' => 429,
                'message' => 'Too many incorrect attempts. Request a new verification code.',
                'errors' => [
                    'otp' => ['Too many incorrect attempts. Request a new verification code.'],
                ],
            ];
        }

        $expectedHash = (string) ($state['hash'] ?? '');
        $actualHash = $this->hashOtp($otp);

        if ($expectedHash === '' || !hash_equals($expectedHash, $actualHash)) {
            $state['attempts'] = $attempts + 1;
            $request->session()->put(self::SESSION_KEY, $state);

            $remainingAttempts = max(
                0,
                self::MAX_ATTEMPTS - (int) $state['attempts']
            );

            return [
                'ok' => false,
                'status' => 422,
                'message' => 'The verification code is incorrect.',
                'errors' => [
                    'otp' => [
                        $remainingAttempts > 0
                            ? "Incorrect code. {$remainingAttempts} attempt(s) remaining."
                            : 'Incorrect code. Request a new verification code.',
                    ],
                ],
            ];
        }

        $state['verified_at'] = now()->timestamp;
        $state['attempts'] = 0;
        $request->session()->put(self::SESSION_KEY, $state);

        return [
            'ok' => true,
            'status' => 200,
            'message' => 'Email verified successfully.',
            'verified' => true,
        ];
    }

    public function isVerified(Request $request, string $email): bool
    {
        $email = strtolower(trim($email));
        $state = $request->session()->get(self::SESSION_KEY);

        if (!is_array($state)) {
            return false;
        }

        if (($state['email'] ?? null) !== $email) {
            return false;
        }

        $verifiedAt = (int) ($state['verified_at'] ?? 0);

        if ($verifiedAt <= 0) {
            return false;
        }

        return $verifiedAt >= now()
            ->subMinutes(self::VERIFIED_MINUTES)
            ->timestamp;
    }

    public function clear(Request $request): void
    {
        $request->session()->forget(self::SESSION_KEY);
    }

    private function hashOtp(string $otp): string
    {
        return hash_hmac(
            'sha256',
            $otp,
            (string) config('app.key')
        );
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');

        if ($domain === '') {
            return $email;
        }

        $visible = mb_substr($local, 0, min(2, mb_strlen($local)));
        $maskedCount = max(
            3,
            mb_strlen($local) - mb_strlen($visible)
        );

        return $visible
            . str_repeat('•', $maskedCount)
            . '@'
            . $domain;
    }
}
