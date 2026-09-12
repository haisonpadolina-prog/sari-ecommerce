<?php

namespace App\Services;

use App\Models\CourierAccount;
use App\Models\MarketplaceOrder;
use App\Models\RiderEarning;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RiderEarningService
{
    public function recordForCompletedOrder(
        MarketplaceOrder $order,
        string $source = 'order_completion'
    ): RiderEarning {
        return DB::transaction(function () use ($order, $source): RiderEarning {
            $locked = MarketplaceOrder::query()
                ->whereKey($order->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status !== 'delivered' || !$locked->delivered_at) {
                throw ValidationException::withMessages([
                    'order' => 'Rider earnings can only be recognized after delivery.',
                ]);
            }

            $email = strtolower(trim((string) $locked->courier_email));

            if ($email === '') {
                throw ValidationException::withMessages([
                    'courier' => 'Delivered order has no assigned courier email.',
                ]);
            }

            $courier = CourierAccount::query()
                ->whereRaw('LOWER(email) = ?', [$email])
                ->first();

            if (!$courier) {
                throw ValidationException::withMessages([
                    'courier' => 'Assigned courier account could not be resolved for rider earnings.',
                ]);
            }

            return RiderEarning::query()->firstOrCreate(
                ['marketplace_order_id' => $locked->id],
                [
                    'courier_account_id' => $courier->id,
                    'delivery_fee_amount' => round(max(0, (float) $locked->delivery_fee), 2),
                    'currency' => 'PHP',
                    'status' => 'available',
                    'earned_at' => $locked->delivered_at,
                    'metadata' => [
                        'source' => $source,
                        'order_number' => $locked->order_number,
                        'delivery_fee_field' => 'marketplace_orders.delivery_fee',
                        'important' => 'available means earned in the internal rider ledger; it is not yet an external payout.',
                    ],
                ]
            );
        });
    }
}
