<?php

namespace App\Services\Messaging;

use App\Events\BuyerSellerMessageUpdated;
use App\Models\Messaging\BuyerSellerMessage;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Platform\SellerNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BuyerSellerOrderConfirmationService
{
    public function createForOrder(MarketplaceOrder $order): BuyerSellerMessage
    {
        // Called from within the checkout transaction: an order and its first
        // confirmation message either commit together or both roll back.
        $message = BuyerSellerMessage::query()->firstOrCreate(
            ['order_confirmation_order_id' => $order->id],
            [
                'marketplace_order_id' => $order->id,
                'seller_account_id' => $order->seller_account_id,
                'buyer_account_id' => $order->buyer_account_id,
                'buyer_social_account_id' => $order->buyer_social_account_id,
                'sender_role' => 'seller',
                'message_type' => 'order_confirmation',
                'body' => 'Thank you for your order! Please confirm your delivery address and contact number below. If anything is incorrect, send us a message before your order is shipped.',
            ]
        );

        // Existing Seller Center bell reads seller_notifications.
        if ($message->wasRecentlyCreated) {
            $buyerKey = $order->buyer_account_id
                ? 'account-' . $order->buyer_account_id
                : 'social-' . $order->buyer_social_account_id;
            SellerNotification::create([
                'seller_account_id' => $order->seller_account_id,
                'type' => 'order_confirmation',
                'title' => 'New order — address confirmation requested',
                'message' => 'Order ' . $order->order_number . ' has been placed. A confirmation message has been sent automatically.',
                'action_url' => route('seller.messages', ['buyer' => $buyerKey]),
            ]);
        }
        $this->announce($message);

        return $message;
    }

    public function announce(BuyerSellerMessage $message): void
    {
        $notify = static function () use ($message): void {
            try {
                BuyerSellerMessageUpdated::dispatch($message);
            } catch (\Throwable $exception) {
                // A WebSocket outage must not invalidate a successfully saved order/message.
                Log::warning('Buyer seller chat broadcast unavailable', [
                    'message_id' => $message->id,
                    'exception' => get_class($exception),
                ]);
            }
        };

        if (DB::transactionLevel() > 0) {
            DB::afterCommit($notify);
        } else {
            $notify();
        }
    }

    public function payload(BuyerSellerMessage $message, string $viewer = 'buyer'): array
    {
        $message->loadMissing('order');
        $order = $message->message_type === 'order_confirmation'
            ? $message->order : null;

        return [
            'id' => (int) $message->id,
            'sender_role' => (string) $message->sender_role,
            'body' => (string) $message->body,
            'message_type' => (string) ($message->message_type ?: 'text'),
            'created_at' => $message->created_at?->toIso8601String(),
            'order_confirmation' => $order ? [
                'id' => (int) $order->id,
                'number' => (string) $order->order_number,
                'status' => (string) $order->status,
                'items' => collect($order->items ?? [])->map(fn ($item) => [
                    'name' => (string) ($item['name'] ?? 'Product'),
                    'image_url' => !empty($item['product_id'])
                        ? route($viewer === 'seller' ? 'seller.products.image' : 'buyer.product.image', ['product' => (int) $item['product_id']])
                        : null,
                    'qty' => (int) ($item['qty'] ?? 1),
                ])->values()->all(),
                'total' => number_format((float) $order->total, 2, '.', ''),
                'phone' => (string) ($order->buyer_phone ?? ''),
                'address' => (string) ($order->buyer_address ?? ''),
                'confirmed' => $order->delivery_address_confirmed_at !== null,
                'confirmed_at' => $order->delivery_address_confirmed_at?->toIso8601String(),
            ] : null,
        ];
    }
}
