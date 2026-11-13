<?php

namespace App\Services\Mobile;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

final class MobileFcmSender
{
    /** Returns true ONLY on successful FCM HTTP v1 acceptance. */
    public function send(string $encryptedToken, string $title, string $body, string $type, string $eventId): bool
    {
        $project = trim((string) config('services.sari_fcm.project_id'));
        $keyFile = trim((string) config('services.sari_fcm.credentials_file'));
        if ($project === '' || $keyFile === '' || !is_file($keyFile)) {
            return false;
        }
        try {
            $google = new GoogleClient();
            $google->setAuthConfig($keyFile);
            $google->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $access = $google->fetchAccessTokenWithAssertion()['access_token'] ?? '';
            if (!$access) {
                return false;
            }
            $response = Http::withToken($access)->timeout(12)->post(
                'https://fcm.googleapis.com/v1/projects/'.rawurlencode($project).'/messages:send',
                ['message' => [
                    'token' => Crypt::decryptString($encryptedToken),
                    'notification' => ['title' => $title, 'body' => $body],
                    'data' => ['type' => $type, 'event_id' => $eventId],
                    'android' => ['priority' => 'high', 'notification' => ['channel_id' => 'sari_updates', 'sound' => 'default']],
                    'apns' => ['payload' => ['aps' => ['sound' => 'default']]],
                ]]
            );
            if (!$response->successful()) {
                Log::warning('FCM delivery rejected', ['status' => $response->status()]);
            }
            return $response->successful();
        } catch (Throwable $e) {
            // Never log the FCM registration token or service account credentials.
            Log::warning('FCM delivery failed', ['exception' => get_class($e)]);
            return false;
        }
    }
}
