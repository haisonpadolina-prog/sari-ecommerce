<?php

namespace App\Services\Messaging;

use App\Events\PlatformMessageSent;
use App\Models\Messaging\PlatformConversation;
use App\Models\Messaging\PlatformMessage;
use Illuminate\Support\Facades\DB;
use Throwable;

class SariAdminAssistantReplyService
{
    public function replyToTrigger(int $triggerMessageId): ?PlatformMessage
    {
        if (!(bool) config('sari.assistant.enabled', true)) {
            return null;
        }

        $trigger = PlatformMessage::query()
            ->with('conversation.participants')
            ->find($triggerMessageId);

        if (
            !$trigger
            || !$trigger->conversation
            || $trigger->sender_role === 'admin'
        ) {
            return null;
        }

        $conversation = $trigger->conversation;

        if (
            $conversation->conversation_type !== 'admin_support'
            || $conversation->status !== 'active'
        ) {
            return null;
        }

        $adminParticipant = $conversation->participants
            ->first(fn ($participant) =>
                $participant->participant_role === 'admin'
                && !$participant->left_at
            );

        if (!$adminParticipant) {
            return null;
        }

        $body = $this->formalAcknowledgement($trigger);

        $message = DB::transaction(function () use (
            $conversation,
            $trigger,
            $adminParticipant,
            $body
        ): ?PlatformMessage {
            PlatformConversation::query()
                ->whereKey($conversation->id)
                ->lockForUpdate()
                ->first();

            /*
            | If a human Admin already responded after this Seller message,
            | never insert an automated acknowledgement underneath it.
            */
            $repliesAfterTrigger = PlatformMessage::query()
                ->where('platform_conversation_id', $conversation->id)
                ->where('sender_role', 'admin')
                ->where('id', '>', $trigger->id)
                ->whereNull('deleted_at')
                ->oldest('id')
                ->get();

            foreach ($repliesAfterTrigger as $reply) {
                $isAutomated = (bool) data_get(
                    $reply->metadata,
                    'ai_assistant',
                    false
                );

                if (!$isAutomated) {
                    return null;
                }

                /*
                | Exact acknowledgement for this trigger already exists.
                */
                if (
                    (bool) data_get(
                        $reply->metadata,
                        'support_acknowledgement',
                        false
                    )
                    && (int) data_get(
                        $reply->metadata,
                        'trigger_message_id',
                        0
                    ) === (int) $trigger->id
                ) {
                    return $reply;
                }

                /*
                | Repair a legacy automated contextual reply for this exact
                | trigger instead of creating another message.
                */
                if (
                    (int) data_get(
                        $reply->metadata,
                        'trigger_message_id',
                        0
                    ) === (int) $trigger->id
                ) {
                    $metadata = (array) ($reply->metadata ?? []);

                    $reply->forceFill([
                        'message_type' => 'text',
                        'body' => $body,
                        'metadata' => array_merge($metadata, [
                            'ai_assistant' => true,
                            'automated' => true,
                            'assistant_name' => 'SARI Assistant',
                            'trigger_message_id' => $trigger->id,
                            'support_acknowledgement' => true,
                            'provider' => 'system_acknowledgement',
                        ]),
                    ])->save();

                    return $reply->fresh([
                        'conversation.participants',
                        'reactions',
                    ]);
                }
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
                    'support_acknowledgement' => true,
                    'provider' => 'system_acknowledgement',
                ],
            ]);

            $conversation->forceFill([
                'last_message_at' => $message->created_at,
            ])->save();

            return $message;
        });

        if (!$message) {
            return null;
        }

        try {
            event(new PlatformMessageSent(
                $message->fresh()->load('conversation.participants')
            ));
        } catch (Throwable $e) {
            /*
            | The acknowledgement is already stored. Realtime failure must
            | never turn a successful Seller message into a server error.
            */
            report($e);
        }

        return $message->fresh([
            'conversation.participants',
            'reactions',
        ]);
    }

    private function formalAcknowledgement(
        PlatformMessage $trigger
    ): string {
        if (
            $trigger->message_type === 'attachment'
            && trim((string) $trigger->body) === ''
        ) {
            return 'Thank you for contacting SARI Admin Support. Your attachment has been received successfully and will be reviewed by our support team. Please wait for a SARI administrator to respond in this conversation. If you have additional details or supporting information, you may send them here while you wait.';
        }

        return 'Thank you for contacting SARI Admin Support. We understand your concern and have received your message successfully. Our support team will review the information you provided. Please wait for a SARI administrator to respond in this conversation. If you have additional details or supporting information, you may send them here while you wait.';
    }
}
