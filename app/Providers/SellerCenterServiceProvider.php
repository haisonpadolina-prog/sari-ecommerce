<?php
namespace App\Providers;

use App\Models\ComplianceMessage;
use App\Models\MarketplaceOrder;
use App\Models\ProductReview;
use App\Models\SellerNotification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SellerCenterServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(base_path('routes/seller-center.php'));

        MarketplaceOrder::created(function (MarketplaceOrder $order): void {
            if (!Schema::hasTable('seller_notifications')) {
                return;
            }
            SellerNotification::create([
                'seller_account_id' => $order->seller_account_id,
                'type' => 'new_order',
                'title' => 'New order received',
                'message' => 'Buyer checkout created ' . $order->order_number . '.',
                'action_url' => '/seller/orders',
                'data' => ['order_id' => $order->id, 'status' => $order->status],
            ]);
        });

        MarketplaceOrder::updated(function (MarketplaceOrder $order): void {
            if (!$order->wasChanged('status') || !Schema::hasTable('seller_notifications')) {
                return;
            }
            SellerNotification::create([
                'seller_account_id' => $order->seller_account_id,
                'type' => 'order_status',
                'title' => 'Order status updated',
                'message' => $order->order_number . ' is now ' . $order->statusLabel() . '.',
                'action_url' => '/seller/orders',
                'data' => ['order_id' => $order->id, 'status' => $order->status],
            ]);
        });

        ProductReview::created(function (ProductReview $review): void {
            if (!Schema::hasTable('seller_notifications')) {
                return;
            }
            SellerNotification::create([
                'seller_account_id' => $review->seller_account_id,
                'type' => 'review',
                'title' => 'New product review',
                'message' => 'A Buyer left a ' . $review->rating . '-star review.',
                'action_url' => '/seller/reviews-ratings',
                'data' => ['review_id' => $review->id],
            ]);
        });

        ComplianceMessage::created(function (ComplianceMessage $message): void {
            if ($message->sender_role !== 'admin' || !Schema::hasTable('seller_notifications')) {
                return;
            }
            SellerNotification::create([
                'seller_account_id' => $message->seller_account_id,
                'type' => 'compliance',
                'title' => 'Compliance update',
                'message' => str($message->message)->limit(180)->toString(),
                'action_url' => '/seller/compliance-center',
                'data' => ['compliance_message_id' => $message->id],
            ]);
        });
    }
}
