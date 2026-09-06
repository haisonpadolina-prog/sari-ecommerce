<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\SellerAccount;
use Illuminate\Http\Request;

class SellerLayoutStateController extends Controller
{
    public function __invoke(Request $request)
    {
        if (!$request->session()->get('is_seller')) {
            return response()->json([
                'authenticated' => false,
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | Reuse middleware seller context
        |--------------------------------------------------------------------------
        */

        $seller = $request->attributes->get('sellerAccount');

        if (!$seller instanceof SellerAccount) {
            $seller = SellerAccount::find(
                $request->session()->get('seller_account_id')
            );
        }

        if (!$seller) {
            return response()->json([
                'authenticated' => false,
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | Notification state loads after first paint
        |--------------------------------------------------------------------------
        |
        | These queries no longer block the Seller sidebar/logo from rendering.
        |
        */

        $unreadCount = ChatMessage::query()
            ->where('seller_account_id', $seller->id)
            ->where('sender_role', 'admin')
            ->whereNull('read_by_seller_at')
            ->count();

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
                    'attachment_name' => $message->attachment_name,
                    'time' => $message->created_at?->format('h:i A'),
                    'unread' => is_null($message->read_by_seller_at),
                ];
            })
            ->values();

        return response()->json([
            'authenticated' => true,
            'unread_count' => $unreadCount,
            'recent_messages' => $recentMessages,
        ]);
    }
}
