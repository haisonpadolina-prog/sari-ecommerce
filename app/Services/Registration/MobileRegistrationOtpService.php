<?php

namespace App\Services\Registration;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MobileRegistrationOtpService
{
    private const EXPIRES_MINUTES = 5;
    private const RESEND_SECONDS = 45;
    private const MAX_ATTEMPTS = 5;

    public function sendCode(string $email, string $purpose = 'buyer'): array
    {
        $email = strtolower(trim($email));
        $purpose = strtolower(trim($purpose));
        $key = $this->challengeKey($email, $purpose);
        $existing = Cache::get($key, []);

        $lastSentAt = is_array($existing) ? (int) ($existing['last_sent_at'] ?? 0) : 0;
        $remaining = self::RESEND_SECONDS - (now()->timestamp - $lastSentAt);

        if ($lastSentAt > 0 && $remaining > 0) {
            return [
                'ok' => false,
                'status' => 429,
                'message' => 'Please wait before requesting another verification code.',
                'retry_after' => $remaining,
            ];
        }

        $code = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(self::EXPIRES_MINUTES);

        try {
            Mail::raw(
                "Your SARI email verification code is {$code}.\n\nThis code expires in ".self::EXPIRES_MINUTES.' minutes.',
                fn ($message) => $message->to($email)->subject('SARI registration verification code')
            );
        } catch (Throwable $exception) {
            report($exception);
            return ['ok' => false, 'status' => 503, 'message' => 'The verification email could not be sent right now.'];
        }

        Cache::put($key, [
            'email' => $email,
            'purpose' => $purpose,
            'hash' => $this->hashCode($code),
            'expires_at' => $expiresAt->timestamp,
            'last_sent_at' => now()->timestamp,
            'attempts' => 0,
        ], $expiresAt);

        return [
            'ok' => true,
            'status' => 200,
            'message' => 'Verification code sent.',
            'expires_in' => self::EXPIRES_MINUTES * 60,
            'retry_after' => self::RESEND_SECONDS,
        ];
    }

    public function verifyCode(string $email, string $code, string $purpose = 'buyer'): array
    {
        $email = strtolower(trim($email));
        $purpose = strtolower(trim($purpose));
        $key = $this->challengeKey($email, $purpose);
        $state = Cache::get($key);

        if (!is_array($state) || ($state['email'] ?? '') !== $email || ($state['purpose'] ?? '') !== $purpose) {
            return ['ok' => false, 'status' => 422, 'message' => 'Request a new verification code for this email address.'];
        }

        if ((int) ($state['expires_at'] ?? 0) <= now()->timestamp) {
            Cache::forget($key);
            return ['ok' => false, 'status' => 422, 'message' => 'The verification code has expired. Request a new code.'];
        }

        $attempts = (int) ($state['attempts'] ?? 0);
        if ($attempts >= self::MAX_ATTEMPTS) {
            Cache::forget($key);
            return ['ok' => false, 'status' => 429, 'message' => 'Too many incorrect attempts. Request a new verification code.'];
        }

        if (!hash_equals((string) ($state['hash'] ?? ''), $this->hashCode(trim($code)))) {
            $state['attempts'] = $attempts + 1;
            Cache::put($key, $state, now()->addSeconds(max(1, (int) $state['expires_at'] - now()->timestamp)));
            return ['ok' => false, 'status' => 422, 'message' => 'The verification code is incorrect.'];
        }

        Cache::forget($key);
        return ['ok' => true, 'status' => 200, 'message' => 'Email verified successfully.'];
    }

    public function clear(string $email, string $purpose = 'buyer'): void
    {
        Cache::forget($this->challengeKey(strtolower(trim($email)), strtolower(trim($purpose))));
    }

    private function challengeKey(string $email, string $purpose): string
    {
        return 'sari:mobile-otp:'.$purpose.':'.hash('sha256', $email);
    }

    private function hashCode(string $code): string
    {
        return hash_hmac('sha256', $code, (string) config('app.key'));
    }
}
