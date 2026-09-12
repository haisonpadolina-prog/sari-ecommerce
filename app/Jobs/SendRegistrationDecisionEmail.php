<?php

namespace App\Jobs;

use App\Models\PlatformSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendRegistrationDecisionEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 20;

    public function __construct(
        public string $email,
        public string $subject,
        public string $message
    ) {}

    public function handle(): void
    {
        $enabled = PlatformSetting::valueOf(
            'registration_decision_email_enabled',
            true
        );

        $enabled = is_bool($enabled)
            ? $enabled
            : (
                filter_var(
                    $enabled,
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                ) ?? false
            );

        if (!$enabled) {
            Log::info(
                'SARI registration decision email skipped by platform policy.',
                ['email' => $this->email]
            );

            return;
        }

        try {
            Mail::raw($this->message, function ($mail) {
                $mail->to($this->email)
                    ->subject($this->subject);
            });
        } catch (\Throwable $e) {
            Log::warning(
                'SARI queued registration decision email could not be sent.',
                [
                    'email' => $this->email,
                    'error' => $e->getMessage(),
                ]
            );
        }
    }
}
