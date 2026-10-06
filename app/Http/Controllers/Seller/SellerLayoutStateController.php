<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Accounts\SellerAccount;
use App\Models\Messaging\BuyerSellerMessage;
use App\Models\Messaging\ChatMessage;
use App\Models\Platform\SellerNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SellerLayoutStateController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if (!$request->session()->get('is_seller')) {
            return response()
                ->json([
                    'authenticated' => false,
                ], 401)
                ->header(
                    'Cache-Control',
                    'no-store, no-cache, must-revalidate'
                );
        }

        $seller = $request->attributes->get('sellerAccount');

        if (!$seller instanceof SellerAccount) {
            $seller = SellerAccount::query()->find(
                (int) $request->session()->get(
                    'seller_account_id'
                )
            );
        }

        if (!$seller) {
            return response()
                ->json([
                    'authenticated' => false,
                ], 401)
                ->header(
                    'Cache-Control',
                    'no-store, no-cache, must-revalidate'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Messaging unread state
        |--------------------------------------------------------------------------
        |
        | This drives ONLY the Chat / Messaging sidebar badge.
        | It must never be used as the header notification bell count.
        |
        */
        $adminUnreadCount = ChatMessage::query()
            ->where('seller_account_id', $seller->id)
            ->where('sender_role', 'admin')
            ->whereNull('read_by_seller_at')
            ->count();

        $buyerUnreadCount = BuyerSellerMessage::query()
            ->where('seller_account_id', $seller->id)
            ->where('sender_role', 'buyer')
            ->whereNull('read_at')
            ->count();

        $messageUnreadCount =
            $adminUnreadCount + $buyerUnreadCount;

        /*
        |--------------------------------------------------------------------------
        | Header notification bell state
        |--------------------------------------------------------------------------
        |
        | SellerNotification is the ONLY source of truth.
        | Return unread rows only because the compact header dropdown is an
        | unread inbox. Read history remains available in /seller/notifications.
        |
        */
        $notificationUnreadCount = 0;
        $recentNotifications = collect();

        if (Schema::hasTable('seller_notifications')) {
            $notificationUnreadCount =
                SellerNotification::query()
                    ->where(
                        'seller_account_id',
                        $seller->id
                    )
                    ->whereNull('read_at')
                    ->count();

            $recentNotifications =
                SellerNotification::query()
                    ->where(
                        'seller_account_id',
                        $seller->id
                    )
                    ->whereNull('read_at')
                    ->latest('created_at')
                    ->limit(8)
                    ->get()
                    ->map(function (
                        SellerNotification $notification
                    ) {
                        return [
                            'id' => (int) $notification->id,
                            'type' => (string) $notification->type,
                            'title' => (string) $notification->title,
                            'message' => $notification->message,
                            'action_url' =>
                                $notification->action_url
                                ?: route(
                                    'seller.notifications.index',
                                    [],
                                    false
                                ),
                            'read_url' => route(
                                'seller.notifications.read',
                                $notification,
                                false
                            ),
                            'data' =>
                                $notification->data ?: [],
                            'created_at' =>
                                $notification->created_at
                                    ?->toIso8601String(),
                            'time' =>
                                $notification->created_at
                                    ?->format('h:i A'),
                            'unread' => true,
                        ];
                    })
                    ->values();
        }

        /*
        |--------------------------------------------------------------------------
        | Backward-compatible message list
        |--------------------------------------------------------------------------
        |
        | Existing messaging code may still use this list. It does NOT drive
        | the header bell.
        |
        */
        $recentMessages = ChatMessage::query()
            ->where('seller_account_id', $seller->id)
            ->where('sender_role', 'admin')
            ->latest('created_at')
            ->limit(5)
            ->get([
                'id',
                'body',
                'attachment_name',
                'created_at',
                'read_by_seller_at',
            ])
            ->map(function (ChatMessage $message) {
                return [
                    'id' => (int) $message->id,
                    'body' => $message->body,
                    'attachment_name' =>
                        $message->attachment_name,
                    'time' =>
                        $message->created_at
                            ?->format('h:i A'),
                    'unread' =>
                        is_null(
                            $message->read_by_seller_at
                        ),
                ];
            })
            ->values();

        return response()
            ->json([
                'authenticated' => true,

                'message_unread_count' =>
                    $messageUnreadCount,

                'notification_unread_count' =>
                    $notificationUnreadCount,

                'recent_notifications' =>
                    $recentNotifications,

                /*
                 * Keep legacy key for older clients, but it now aliases
                 * the notification count—not chat count.
                 */
                'unread_count' =>
                    $notificationUnreadCount,

                'recent_messages' =>
                    $recentMessages,
            ])
            ->header(
                'Cache-Control',
                'no-store, no-cache, must-revalidate'
            );
    }
}
