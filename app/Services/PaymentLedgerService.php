<?php

namespace App\Services;

use App\Models\MarketplaceOrder;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentLedgerService
{
    public function recordCompletedOrderCollection(
        MarketplaceOrder $order,
        string $source = 'order_completion'
    ): PaymentTransaction {
        return DB::transaction(function () use ($order, $source): PaymentTransaction {
            $locked = MarketplaceOrder::query()
                ->whereKey($order->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status !== 'delivered' || !$locked->delivered_at) {
                throw ValidationException::withMessages([
                    'order' => 'Payment collection can only be finalized for a delivered order.',
                ]);
            }

            if (strtolower((string) $locked->payment_status) !== 'paid') {
                throw ValidationException::withMessages([
                    'payment' => 'A delivered order must be marked paid before finance records are created.',
                ]);
            }

            $key = 'order:' . $locked->id . ':completed-payment';

            return PaymentTransaction::query()->firstOrCreate(
                ['idempotency_key' => $key],
                [
                    'marketplace_order_id' => $locked->id,
                    'provider' => strtoupper((string) $locked->payment_method) === 'COD'
                        ? 'cod'
                        : 'external_or_manual',
                    'provider_reference' => null,
                    'channel' => strtoupper((string) $locked->payment_method) === 'COD'
                        ? 'cash_on_delivery'
                        : strtolower((string) $locked->payment_method),
                    'type' => 'collection',
                    'amount' => round((float) $locked->total, 2),
                    'currency' => 'PHP',
                    'status' => 'paid',
                    'paid_at' => $locked->delivered_at,
                    'metadata' => [
                        'source' => $source,
                        'order_number' => $locked->order_number,
                        'payment_status_snapshot' => $locked->payment_status,
                        'note' => strtoupper((string) $locked->payment_method) === 'COD'
                            ? 'Internal COD collection confirmation from the delivery-completion workflow.'
                            : 'Internal payment-status snapshot only. No external payment-provider reference was supplied.',
                    ],
                ]
            );
        });
    }
}
