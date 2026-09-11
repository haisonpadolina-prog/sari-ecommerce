<?php

namespace App\Services;

use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MarketplaceOrderWorkflowService
{
    public function __construct(
        private readonly FinancialFlowService $finance
    ) {
    }

    public function transition(
        MarketplaceOrder $order,
        string $expectedStatus,
        string $newStatus,
        string $title,
        string $message,
        string $audience = 'seller',
        array $extra = []
    ): MarketplaceOrder {
        return DB::transaction(function () use (
            $order,
            $expectedStatus,
            $newStatus,
            $title,
            $message,
            $audience,
            $extra
        ) {
            $locked = MarketplaceOrder::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status !== $expectedStatus) {
                throw ValidationException::withMessages([
                    'order' => 'This order already changed. Current status: ' . $locked->statusLabel() . '.',
                ]);
            }

            $locked->forceFill(array_merge([
                'status' => $newStatus,
            ], $extra))->save();

            MarketplaceOrderEvent::create([
                'marketplace_order_id' => $locked->id,
                'seller_account_id' => $locked->seller_account_id,
                'audience' => $audience,
                'type' => $newStatus,
                'title' => $title,
                'message' => $message,
                'status' => $newStatus,
            ]);

            if ($newStatus === 'delivered') {
                $this->finance->recordCompletedOrder(
                    $locked,
                    'order_workflow_delivery'
                );
            }

            return $locked->fresh([
                'seller',
                'commission',
                'paymentTransactions',
                'sellerSettlement',
                'riderEarning',
            ]);
        });
    }

    public function record(
        MarketplaceOrder $order,
        string $audience,
        string $type,
        string $title,
        string $message
    ): void {
        MarketplaceOrderEvent::create([
            'marketplace_order_id' => $order->id,
            'seller_account_id' => $order->seller_account_id,
            'audience' => $audience,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'status' => $order->status,
        ]);
    }
}
