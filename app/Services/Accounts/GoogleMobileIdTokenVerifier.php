<?php

namespace App\Services\Accounts;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/** Only accepts Google ID tokens intended for SARI's server OAuth client. */
class GoogleMobileIdTokenVerifier
{
    public function verify(string $idToken): ?array
    {
        $audience = trim((string) config('services.google.mobile_server_client_id'));
        if ($audience === '') {
            throw new RuntimeException('Google mobile login is not configured.');
        }

        try {
            $google = new GoogleClient(['client_id' => $audience]);
            $claims = $google->verifyIdToken($idToken);
        } catch (Throwable $exception) {
            // Do not log the token or return raw provider error details to the device.
            Log::warning('Google mobile ID token verification failed', [
                'exception' => get_class($exception),
            ]);
            return null;
        }

        if (!is_array($claims)) {
            return null;
        }

        $issuer = (string) ($claims['iss'] ?? '');
        $emailVerified = filter_var($claims['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $email = strtolower(trim((string) ($claims['email'] ?? '')));
        $subject = trim((string) ($claims['sub'] ?? ''));
        $aud = (string) ($claims['aud'] ?? '');
        $exp = (int) ($claims['exp'] ?? 0);

        if (!in_array($issuer, ['accounts.google.com', 'https://accounts.google.com'], true)
            || !$emailVerified
            || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || $subject === ''
            || $aud !== $audience
            || $exp <= time()) {
            return null;
        }

        return ['email' => $email, 'sub' => $subject];
    }
}
