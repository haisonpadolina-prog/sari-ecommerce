<?php

namespace App\Providers;

use App\Events\SellerNotificationCreated;
use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\ProductReview;
use App\Models\Catalog\SellerInventoryMovement;
use App\Models\Catalog\SellerProduct;
use App\Models\Compliance\ComplianceMessage;
use App\Models\Finance\SellerSettlement;
use App\Models\Messaging\BuyerSellerMessage;
use App\Models\Messaging\ChatMessage;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Platform\SellerNotification;
use App\Models\Promotions\SellerVoucher;
use App\Models\Promotions\SellerVoucherRedemption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SellerCenterServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(base_path('routes/seller-center.php'));

        /*
        |--------------------------------------------------------------------------
        | Unified realtime notification broadcast
        |--------------------------------------------------------------------------
        |
        | SellerNotification is the database source-of-truth. Every record that
        | is successfully committed is also pushed to the Seller Echo channel,
        | so the persistent header bell updates without a page refresh.
        |
        */
        SellerNotification::created(function (SellerNotification $notification): void {
            DB::afterCommit(function () use ($notification): void {
                $fresh = SellerNotification::query()->find($notification->id);

                if (!$fresh) {
                    return;
                }

                $seller = SellerAccount::query()->find($fresh->seller_account_id);

                if (!$seller) {
                    return;
                }

                $token = $seller->ensureRealtimeToken();

                SellerNotificationCreated::dispatch($fresh, $token);
            });
        });

        /*
        |--------------------------------------------------------------------------
        | Orders + Shipping / Fulfillment
        |--------------------------------------------------------------------------
        */
        MarketplaceOrder::created(function (MarketplaceOrder $order): void {
            $this->notify([
                'seller_account_id' => $order->seller_account_id,
                'type' => 'new_order',
                'title' => 'New order received',
                'message' => 'Buyer checkout created ' . $order->order_number . '.',
                'action_url' => '/seller/orders',
                'data' => [
                    'order_id' => $order->id,
                    'status' => $order->status,
                ],
            ]);
        });

        MarketplaceOrder::updated(function (MarketplaceOrder $order): void {
            if (!$order->wasChanged('status')) {
                return;
            }

            $shippingStatuses = [
                'ready_for_pickup',
                'courier_accepted',
                'heading_pickup',
                'arrived_pickup',
                'in_transit',
                'arrived_buyer',
                'delivered',
            ];

            $isShipping = in_array($order->status, $shippingStatuses, true);

            $this->notify([
                'seller_account_id' => $order->seller_account_id,
                'type' => $isShipping ? 'shipment' : 'order_status',
                'title' => $isShipping
                    ? 'Shipment update'
                    : 'Order status updated',
                'message' => $order->order_number . ' is now ' . $order->statusLabel() . '.',
                'action_url' => $isShipping
                    ? '/seller/shipping/' . $order->id
                    : '/seller/orders',
                'data' => [
                    'order_id' => $order->id,
                    'status' => $order->status,
                ],
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Buyer + Admin messages
        |--------------------------------------------------------------------------
        */
        BuyerSellerMessage::created(function (BuyerSellerMessage $message): void {
            if ($message->sender_role !== 'buyer') {
                return;
            }

            $message->loadMissing(['buyer', 'socialBuyer']);

            if ($message->buyer_account_id) {
                $buyerKey = 'account-' . $message->buyer_account_id;
                $buyerName = trim(
                    (string) ($message->buyer?->first_name ?? '') . ' ' .
                    (string) ($message->buyer?->last_name ?? '')
                ) ?: 'Buyer';
            } else {
                $buyerKey = 'social-' . $message->buyer_social_account_id;
                $buyerName = $message->socialBuyer?->name ?: 'SARI Buyer';
            }

            $this->notify([
                'seller_account_id' => $message->seller_account_id,
                'type' => 'buyer_message',
                'title' => 'New Buyer message',
                'message' => $buyerName . ': ' .
                    str($message->body)->limit(160)->toString(),
                'action_url' => '/seller/buyer-messages?buyer=' .
                    rawurlencode($buyerKey),
                'data' => [
                    'buyer_message_id' => $message->id,
                    'buyer_key' => $buyerKey,
                    'marketplace_order_id' => $message->marketplace_order_id,
                ],
            ]);
        });

        ChatMessage::created(function (ChatMessage $message): void {
            if ($message->sender_role !== 'admin') {
                return;
            }

            $preview = filled($message->body)
                ? str($message->body)->limit(160)->toString()
                : ($message->attachment_name ?: 'Admin sent an attachment.');

            $this->notify([
                'seller_account_id' => $message->seller_account_id,
                'type' => 'admin_message',
                'title' => 'New Admin message',
                'message' => $preview,
                'action_url' => '/seller/messages',
                'data' => [
                    'chat_message_id' => $message->id,
                ],
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Reviews + Compliance
        |--------------------------------------------------------------------------
        */
        ProductReview::created(function (ProductReview $review): void {
            $this->notify([
                'seller_account_id' => $review->seller_account_id,
                'type' => 'review',
                'title' => 'New product review',
                'message' => 'A Buyer left a ' . $review->rating . '-star review.',
                'action_url' => '/seller/reviews-ratings',
                'data' => [
                    'review_id' => $review->id,
                ],
            ]);
        });

        ComplianceMessage::created(function (ComplianceMessage $message): void {
            if ($message->sender_role !== 'admin') {
                return;
            }

            $this->notify([
                'seller_account_id' => $message->seller_account_id,
                'type' => 'compliance',
                'title' => 'Compliance update',
                'message' => str($message->message)->limit(180)->toString(),
                'action_url' => '/seller/compliance-center',
                'data' => [
                    'compliance_message_id' => $message->id,
                ],
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Inventory alerts caused by Buyer checkout
        |--------------------------------------------------------------------------
        |
        | Do not notify on every manual stock edit. Notify when an order-driven
        | deduction crosses the Seller's low-stock threshold or reaches zero.
        |
        */
        SellerInventoryMovement::created(function (SellerInventoryMovement $movement): void {
            if ($movement->reason !== 'order_deduction') {
                return;
            }

            $product = SellerProduct::query()->find($movement->seller_product_id);

            if (!$product) {
                return;
            }

            $before = (int) $movement->quantity_before;
            $after = (int) $movement->quantity_after;
            $threshold = max(0, (int) ($product->low_stock_threshold ?? 5));

            if ($after <= 0 && $before > 0) {
                $this->notify([
                    'seller_account_id' => $movement->seller_account_id,
                    'type' => 'inventory',
                    'title' => 'Product is out of stock',
                    'message' => $product->name . ' reached 0 available stock after a Buyer order.',
                    'action_url' => '/seller/inventory',
                    'data' => [
                        'seller_product_id' => $product->id,
                        'stock' => $after,
                    ],
                ]);

                return;
            }

            if (
                $after > 0
                && $after <= $threshold
                && $before > $threshold
            ) {
                $this->notify([
                    'seller_account_id' => $movement->seller_account_id,
                    'type' => 'inventory',
                    'title' => 'Low stock alert',
                    'message' => $product->name . ' is down to ' . $after . ' unit' .
                        ($after === 1 ? '' : 's') . '.',
                    'action_url' => '/seller/inventory',
                    'data' => [
                        'seller_product_id' => $product->id,
                        'stock' => $after,
                        'low_stock_threshold' => $threshold,
                    ],
                ]);
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Promotions activity
        |--------------------------------------------------------------------------
        */
        SellerVoucherRedemption::created(function (SellerVoucherRedemption $redemption): void {
            $voucher = SellerVoucher::query()->find($redemption->seller_voucher_id);

            if (!$voucher) {
                return;
            }

            $this->notify([
                'seller_account_id' => $voucher->seller_account_id,
                'type' => 'voucher',
                'title' => 'Voucher redeemed',
                'message' => $voucher->code . ' was used on an order for a ₱' .
                    number_format((float) $redemption->discount_amount, 2) .
                    ' discount.',
                'action_url' => '/seller/vouchers',
                'data' => [
                    'seller_voucher_id' => $voucher->id,
                    'marketplace_order_id' => $redemption->marketplace_order_id,
                    'redemption_id' => $redemption->id,
                ],
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Finance / settlement state
        |--------------------------------------------------------------------------
        */
        SellerSettlement::updated(function (SellerSettlement $settlement): void {
            if (!$settlement->wasChanged('status')) {
                return;
            }

            $this->notify([
                'seller_account_id' => $settlement->seller_account_id,
                'type' => 'finance',
                'title' => 'Finance status updated',
                'message' => 'Settlement status changed to ' .
                    str((string) $settlement->status)
                        ->replace('_', ' ')
                        ->title()
                        ->toString() . '.',
                'action_url' => '/seller/finance',
                'data' => [
                    'seller_settlement_id' => $settlement->id,
                    'marketplace_order_id' => $settlement->marketplace_order_id,
                    'status' => $settlement->status,
                ],
            ]);
        });
    }

    private function notify(array $attributes): void
    {
        if (!Schema::hasTable('seller_notifications')) {
            return;
        }

        SellerNotification::create($attributes);
    }
}
