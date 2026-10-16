<?php

namespace App\Services\Finance;

use App\Models\Accounts\CourierAccount;
use App\Models\Finance\RiderEarning;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RiderEarningService
{
    public function recordForCompletedOrder(
        MarketplaceOrder $order,
        string $source = 'order_completion',
        ?int $courierAccountId = null
    ): RiderEarning {
        return DB::transaction(
            function () use (
                $order,
                $source,
                $courierAccountId
            ): RiderEarning {
                $locked = MarketplaceOrder::query()
                    ->whereKey(
                        $order->getKey()
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    $locked->status !== 'delivered'
                    || ! $locked->delivered_at
                ) {
                    throw ValidationException::withMessages([
                        'order' =>
                            'Rider earnings can only be recognized after delivery.',
                    ]);
                }

                $courier = null;

                $resolution =
                    'order_courier_email';

                /*
                |--------------------------------------------------------------------------
                | Explicit final-mile Rider
                |--------------------------------------------------------------------------
                |
                | Logistics-managed orders keep courier_email as the original
                | Seller-pickup Rider snapshot.
                |
                | The final-mile Rider is therefore passed explicitly when the
                | last-mile workflow completes the order.
                |
                */

                if ($courierAccountId !== null) {
                    $courier =
                        CourierAccount::query()
                            ->whereKey(
                                $courierAccountId
                            )
                            ->first();

                    if (! $courier) {
                        throw ValidationException::withMessages([
                            'courier' =>
                                'The Rider selected for this delivery earning could not be resolved.',
                        ]);
                    }

                    if (
                        $locked->logistics_account_id
                        &&
                        $courier->logistics_account_id
                        &&
                        (int) $locked
                            ->logistics_account_id
                            !==
                        (int) $courier
                            ->logistics_account_id
                    ) {
                        throw ValidationException::withMessages([
                            'courier' =>
                                'The final-mile Rider does not belong to the Logistics provider handling this order.',
                        ]);
                    }

                    $resolution =
                        'explicit_last_mile_rider';
                } else {
                    /*
                    |--------------------------------------------------------------------------
                    | Legacy / direct-delivery Rider resolution
                    |--------------------------------------------------------------------------
                    */

                    $email =
                        strtolower(
                            trim(
                                (string) $locked
                                    ->courier_email
                            )
                        );

                    if ($email === '') {
                        throw ValidationException::withMessages([
                            'courier' =>
                                'Delivered order has no assigned courier email.',
                        ]);
                    }

                    $courier =
                        CourierAccount::query()
                            ->whereRaw(
                                'LOWER(email) = ?',
                                [
                                    $email,
                                ]
                            )
                            ->first();

                    if (! $courier) {
                        throw ValidationException::withMessages([
                            'courier' =>
                                'Assigned courier account could not be resolved for rider earnings.',
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | One Rider earning per MarketplaceOrder
                |--------------------------------------------------------------------------
                */

                return RiderEarning::query()
                    ->firstOrCreate(
                        [
                            'marketplace_order_id' =>
                                $locked->id,
                        ],
                        [
                            'courier_account_id' =>
                                $courier->id,

                            'delivery_fee_amount' =>
                                round(
                                    max(
                                        0,
                                        (float) $locked
                                            ->delivery_fee
                                    ),
                                    2
                                ),

                            'currency' =>
                                'PHP',

                            'status' =>
                                'available',

                            'earned_at' =>
                                $locked->delivered_at,

                            'metadata' => [
                                'source' =>
                                    $source,

                                'order_number' =>
                                    $locked->order_number,

                                'delivery_fee_field' =>
                                    'marketplace_orders.delivery_fee',

                                'rider_resolution' =>
                                    $resolution,

                                'important' =>
                                    'available means earned in the internal rider ledger; it is not yet an external payout.',
                            ],
                        ]
                    );
            }
        );
    }
}