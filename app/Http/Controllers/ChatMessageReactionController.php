<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatMessageReaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChatMessageReactionController extends Controller
{
    private const ALLOWED_EMOJIS = [
        '👍',
        '❤️',
        '😂',
        '😮',
        '😢',
        '🙏',
    ];

    /**
     * Return the authoritative database timestamp for a message that belongs
     * to the currently logged-in seller's Admin support conversation.
     */
    public function sellerMeta(Request $request, ChatMessage $message): JsonResponse
    {
        $sellerId = $this->sellerId($request);

        $conversationMessage = $this->resolveSellerConversationMessage(
            $sellerId,
            (int) $message->getKey()
        );

        return response()->json([
            'id' => $conversationMessage->id,
            'sender_role' => $conversationMessage->sender_role,
            'created_at' => $conversationMessage->created_at?->toIso8601String(),
        ]);
    }

    /**
     * Toggle the logged-in seller's reaction on ANY message that belongs to
     * this seller's support conversation.
     *
     * We intentionally DO NOT authorize by sender_role. Older Admin messages
     * can have legacy role labels, but they are still valid if their
     * seller_account_id belongs to the logged-in seller.
     */
    public function sellerToggle(Request $request, ChatMessage $message): JsonResponse
    {
        $sellerId = $this->sellerId($request);

        $conversationMessage = $this->resolveSellerConversationMessage(
            $sellerId,
            (int) $message->getKey()
        );

        $validated = $request->validate([
            'emoji' => [
                'required',
                'string',
                Rule::in(self::ALLOWED_EMOJIS),
            ],
        ]);

        $existing = ChatMessageReaction::query()
            ->where('chat_message_id', $conversationMessage->id)
            ->where('reactor_role', 'seller')
            ->where('reactor_id', $sellerId)
            ->first();

        if ($existing && $existing->emoji === $validated['emoji']) {
            $existing->delete();
        } else {
            ChatMessageReaction::query()->updateOrCreate(
                [
                    'chat_message_id' => $conversationMessage->id,
                    'reactor_role' => 'seller',
                    'reactor_id' => $sellerId,
                ],
                [
                    'emoji' => $validated['emoji'],
                ]
            );
        }

        $payload = $this->reactionPayload(
            (int) $conversationMessage->id,
            $sellerId
        );

        $payload['message_sender_role'] = $conversationMessage->sender_role;

        return response()->json($payload);
    }

    private function sellerId(Request $request): int
    {
        abort_unless(
            (bool) $request->session()->get('is_seller'),
            403,
            'Seller session required.'
        );

        $sellerId = (int) $request->session()->get('seller_account_id');

        abort_unless(
            $sellerId > 0,
            403,
            'Seller account required.'
        );

        return $sellerId;
    }

    /**
     * Security rule:
     * the message must belong to the logged-in seller's conversation.
     *
     * No sender_role filter is used here. This allows Seller reactions on
     * Admin messages even if an older Admin row uses a legacy role label.
     */
    private function resolveSellerConversationMessage(
        int $sellerId,
        int $messageId
    ): ChatMessage {
        return ChatMessage::query()
            ->whereKey($messageId)
            ->where('seller_account_id', $sellerId)
            ->firstOr(function () {
                abort(
                    403,
                    'This message is not available in your seller conversation.'
                );
            });
    }

    private function reactionPayload(int $messageId, int $sellerId): array
    {
        $rows = ChatMessageReaction::query()
            ->where('chat_message_id', $messageId)
            ->orderBy('id')
            ->get();

        $reactions = $rows
            ->groupBy('emoji')
            ->map(function ($items, $emoji) {
                return [
                    'emoji' => $emoji,
                    'count' => $items->count(),
                ];
            })
            ->values();

        $mine = $rows
            ->first(function (ChatMessageReaction $reaction) use ($sellerId) {
                return $reaction->reactor_role === 'seller'
                    && (int) $reaction->reactor_id === $sellerId;
            })?->emoji;

        return [
            'success' => true,
            'reactions' => $reactions,
            'mine' => $mine,
        ];
    }
}
