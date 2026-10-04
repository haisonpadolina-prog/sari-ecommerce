<?php

namespace App\Jobs;

use App\Services\Messaging\SariAdminAssistantReplyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSariAdminAssistantReply implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 45;

    public function __construct(public int $triggerMessageId)
    {
    }

    public function handle(
        SariAdminAssistantReplyService $replies
    ): void {
        if (!(bool) config('sari.assistant.enabled', true)) {
            return;
        }

        $replies->replyToTrigger($this->triggerMessageId);
    }
}
