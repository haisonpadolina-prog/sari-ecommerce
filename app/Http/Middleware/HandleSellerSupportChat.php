<?php

namespace App\Http\Middleware;

use App\Models\ChatMessage;
use App\Models\SellerChatRestriction;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class HandleSellerSupportChat
{
    private const ADMIN_ONLINE_CACHE_KEY = 'sari_admin_support_online';
    private const ADMIN_LAST_SEEN_CACHE_KEY = 'sari_admin_support_last_seen';

    public function handle(Request $request, Closure $next): Response
    {
        $sellerId = (int) $request->session()->get('seller_account_id');

        if ($sellerId > 0 && $this->isBlocked($sellerId)) {
            $restriction = SellerChatRestriction::query()
                ->where('seller_account_id', $sellerId)
                ->first();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'This support conversation is currently restricted by SARI Admin.',
                    'blocked' => true,
                    'reason' => $restriction?->block_reason,
                ], 423);
            }

            return back()->withErrors([
                'message' => 'This support conversation is currently restricted by SARI Admin.',
            ]);
        }

        $response = $next($request);

        // Do not interfere with failed validation / failed old chat backend responses.
        if ($sellerId <= 0 || $response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            return $response;
        }

        if (Cache::has(self::ADMIN_ONLINE_CACHE_KEY)) {
            return $response;
        }

        $botMessage = $this->createOfflineBotReplyOnce($sellerId);

        if ($botMessage && $response instanceof JsonResponse) {
            $payload = $response->getData(true);
            $payload['bot_message'] = $this->messagePayload($botMessage);
            $response->setData($payload);
        }

        return $response;
    }

    private function isBlocked(int $sellerId): bool
    {
        if (!Schema::hasTable('seller_chat_restrictions')) {
            return false;
        }

        return SellerChatRestriction::query()
            ->where('seller_account_id', $sellerId)
            ->where('is_blocked', true)
            ->exists();
    }

    private function createOfflineBotReplyOnce(int $sellerId): ?ChatMessage
    {
        if (!Schema::hasTable('chat_messages')) {
            return null;
        }

        // Each time an admin comes online, this timestamp changes. A seller gets
        // at most one bot reply for that offline cycle, not one reply per message.
        $offlineCycle = (string) Cache::get(self::ADMIN_LAST_SEEN_CACHE_KEY, 'never-online');
        $sellerMarkerKey = 'sari_support_bot_replied_cycle:' . $sellerId;

        if ((string) Cache::get($sellerMarkerKey, '') === $offlineCycle) {
            return null;
        }

        $message = new ChatMessage();
        $message->seller_account_id = $sellerId;
        $message->sender_role = 'admin';
        $message->body = 'Hi! I’m SARI Support Bot, an automated assistant. Our administrators are currently offline, but your message has been received. A member of our team will respond as soon as they are available.';

        if (Schema::hasColumn('chat_messages', 'is_bot')) {
            $message->is_bot = true;
        }

        // Seller receives this immediately in the same response, so do not create
        // a false unread badge for the bot reply.
        if (Schema::hasColumn('chat_messages', 'read_by_seller_at')) {
            $message->read_by_seller_at = now();
        }

        $message->save();
        Cache::forever($sellerMarkerKey, $offlineCycle);

        try {
            if (class_exists(\App\Events\ChatMessageSent::class)) {
                event(new \App\Events\ChatMessageSent($message));
            }
        } catch (Throwable $e) {
            report($e);
        }

        return $message;
    }

    private function messagePayload(ChatMessage $message): array
    {
        return [
            'id' => $message->id,
            'seller_id' => (int) $message->seller_account_id,
            'sender_role' => 'admin',
            'is_bot' => true,
            'body' => $message->body,
            'attachment_url' => null,
            'attachment_name' => null,
            'attachment_mime' => null,
            'created_at' => $message->created_at?->toIso8601String(),
            'created_at_iso' => $message->created_at?->toIso8601String(),
        ];
    }
}
