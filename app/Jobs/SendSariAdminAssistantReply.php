<?php

namespace App\Jobs;

use App\Events\PlatformMessageSent;
use App\Models\PlatformMessage;
use App\Services\SariAdminAssistantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SendSariAdminAssistantReply implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 45;

    public function __construct(public int $triggerMessageId)
    {
    }

    public function handle(SariAdminAssistantService $assistant): void
    {
        if (!(bool) config('sari_assistant.enabled', true)) {
            return;
        }

        $trigger = PlatformMessage::query()
            ->with('conversation.participants')
            ->find($this->triggerMessageId);

        if (!$trigger || !$trigger->conversation || $trigger->sender_role === 'admin') {
            return;
        }

        $conversation = $trigger->conversation;

        if ($conversation->status !== 'active') {
            return;
        }

        $adminParticipant = $conversation->participants
            ->first(fn ($participant) =>
                $participant->participant_role === 'admin'
                && !$participant->left_at
            );

        if (!$adminParticipant) {
            return;
        }

        // Only the latest unanswered user message is eligible for AI cover.
        $latestNonAdminId = PlatformMessage::query()
            ->where('platform_conversation_id', $conversation->id)
            ->where('sender_role', '!=', 'admin')
            ->whereNull('deleted_at')
            ->max('id');

        if ((int) $latestNonAdminId !== (int) $trigger->id) {
            return;
        }

        $adminMessagesAfterTrigger = PlatformMessage::query()
            ->where('platform_conversation_id', $conversation->id)
            ->where('sender_role', 'admin')
            ->where('id', '>', $trigger->id)
            ->whereNull('deleted_at')
            ->get(['id', 'metadata']);

        // A human Admin reply cancels the bot. An existing AI reply also avoids duplicates.
        if ($adminMessagesAfterTrigger->isNotEmpty()) {
            return;
        }

        $body = $assistant->replyFor($conversation, $trigger);

        if (!$body) {
            return;
        }

        $message = DB::transaction(function () use (
            $conversation,
            $adminParticipant,
            $trigger,
            $body
        ): PlatformMessage {
            // Re-check inside the transaction to avoid a race with a human Admin reply.
            $newerAdminMessageExists = PlatformMessage::query()
                ->where('platform_conversation_id', $conversation->id)
                ->where('sender_role', 'admin')
                ->where('id', '>', $trigger->id)
                ->whereNull('deleted_at')
                ->exists();

            if ($newerAdminMessageExists) {
                return new PlatformMessage();
            }

            $message = PlatformMessage::query()->create([
                'platform_conversation_id' => $conversation->id,
                'sender_role' => 'admin',
                'sender_id' => (int) $adminParticipant->participant_id,
                'message_type' => 'text',
                'body' => $body,
                'metadata' => [
                    'ai_assistant' => true,
                    'automated' => true,
                    'assistant_name' => 'SARI Assistant',
                    'trigger_message_id' => $trigger->id,
                    'provider' => 'gemini',
                ],
            ]);

            $conversation->forceFill([
                'last_message_at' => $message->created_at,
            ])->save();

            return $message;
        });

        if (!$message->exists) {
            return;
        }

        event(new PlatformMessageSent(
            $message->fresh()->load('conversation.participants')
        ));
    }
}
