<?php

namespace App\Services\Finance;

use App\Models\Accounts\CourierAccount;
use App\Models\Finance\LogisticsEarning;
use App\Models\Finance\RiderEarning;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Platform\PlatformSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShippingEarningService
{
    public const PICKUP_PERCENT_DEFAULT = 25.0;
    public const LAST_MILE_PERCENT_DEFAULT = 45.0;

    /**
     * @return array{rider_earnings: Collection<int, RiderEarning>, logistics_earning: ?LogisticsEarning, primary_rider_earning: ?RiderEarning}
     */
    public function recordForCompletedOrder(
        MarketplaceOrder $order,
        string $source = 'order_completion',
        ?int $finalMileCourierId = null
    ): array {
        return DB::transaction(function () use (
            $order,
            $source,
            $finalMileCourierId
        ): array {
            $locked = MarketplaceOrder::query()
                ->whereKey($order->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status !== 'delivered' || ! $locked->delivered_at) {
                throw ValidationException::withMessages([
                    'order' => 'Shipping earnings can only be recognized after delivery.',
                ]);
            }

            $grossShipping = round(max(0, $locked->courierDeliveryFee()), 2);
            $isCod = strtoupper((string) $locked->payment_method) === 'COD';
            $initialStatus = $isCod ? 'pending_remittance' : 'available';
            $hasLogisticsParcel = $locked->logisticsParcel()->exists();

            if (! $hasLogisticsParcel || ! $locked->logistics_account_id) {
                $rider = $this->resolveDirectRider($locked, $finalMileCourierId);

                $earning = $this->riderEarning(
                    $locked,
                    $rider,
                    'direct_delivery',
                    $grossShipping,
                    $initialStatus,
                    $source,
                    [
                        'shipping_model' => 'direct_delivery_full_fee',
                        'gross_shipping_revenue' => $grossShipping,
                    ]
                );

                return [
                    'rider_earnings' => collect([$earning]),
                    'logistics_earning' => null,
                    'primary_rider_earning' => $earning,
                ];
            }

            $pickupPercent = $this->boundedPercent(
                PlatformSetting::valueOf('shipping_pickup_rider_percent', self::PICKUP_PERCENT_DEFAULT)
            );
            $lastMilePercent = $this->boundedPercent(
                PlatformSetting::valueOf('shipping_last_mile_rider_percent', self::LAST_MILE_PERCENT_DEFAULT)
            );

            if ($pickupPercent + $lastMilePercent > 100) {
                throw ValidationException::withMessages([
                    'shipping_split' => 'Pickup and last-mile Rider percentages cannot exceed 100% combined.',
                ]);
            }

            $pickupRider = $this->resolvePickupRider($locked);
            $finalRider = $this->resolveFinalMileRider($locked, $finalMileCourierId);

            $pickupAmount = $pickupRider
                ? round($grossShipping * ($pickupPercent / 100), 2)
                : 0.0;
            $lastMileAmount = $finalRider
                ? round($grossShipping * ($lastMilePercent / 100), 2)
                : 0.0;
            $logisticsAmount = round(max(0, $grossShipping - $pickupAmount - $lastMileAmount), 2);

            $riderEarnings = collect();

            if ($pickupRider && $pickupAmount > 0) {
                $riderEarnings->push($this->riderEarning(
                    $locked,
                    $pickupRider,
                    'pickup_leg',
                    $pickupAmount,
                    $initialStatus,
                    $source,
                    [
                        'shipping_model' => 'logistics_split',
                        'share_percent' => $pickupPercent,
                        'route_leg' => 'seller_to_origin_hub',
                    ]
                ));
            }

            $primary = null;

            if ($finalRider && $lastMileAmount > 0) {
                $primary = $this->riderEarning(
                    $locked,
                    $finalRider,
                    'last_mile_leg',
                    $lastMileAmount,
                    $initialStatus,
                    $source,
                    [
                        'shipping_model' => 'logistics_split',
                        'share_percent' => $lastMilePercent,
                        'route_leg' => 'destination_hub_to_buyer',
                    ]
                );
                $riderEarnings->push($primary);
            }

            $logistics = LogisticsEarning::query()->firstOrCreate(
                ['marketplace_order_id' => $locked->id],
                [
                    'logistics_account_id' => $locked->logistics_account_id,
                    'gross_shipping_revenue' => $grossShipping,
                    'pickup_rider_cost' => $pickupAmount,
                    'last_mile_rider_cost' => $lastMileAmount,
                    'net_logistics_amount' => $logisticsAmount,
                    'currency' => 'PHP',
                    'status' => $initialStatus,
                    'eligible_at' => $locked->delivered_at,
                    'policy_snapshot' => [
                        'source' => $source,
                        'basis' => 'marketplace_orders.original_delivery_fee_fallback_delivery_fee',
                        'pickup_rider_percent' => $pickupPercent,
                        'last_mile_rider_percent' => $lastMilePercent,
                        'logistics_remainder_percent' => round(100 - $pickupPercent - $lastMilePercent, 4),
                        'pickup_rider_id' => $pickupRider?->id,
                        'final_mile_rider_id' => $finalRider?->id,
                        'seller_funded_shipping_discount' => (float) ($locked->shipping_discount_amount ?? 0),
                        'important' => 'This is an internal earning allocation. COD cash is not released to wallets until remittance verification.',
                    ],
                ]
            );

            return [
                'rider_earnings' => $riderEarnings,
                'logistics_earning' => $logistics,
                'primary_rider_earning' => $primary ?: $riderEarnings->first(),
            ];
        });
    }

    private function riderEarning(
        MarketplaceOrder $order,
        CourierAccount $rider,
        string $type,
        float $amount,
        string $status,
        string $source,
        array $metadata
    ): RiderEarning {
        return RiderEarning::query()->firstOrCreate(
            [
                'marketplace_order_id' => $order->id,
                'courier_account_id' => $rider->id,
                'earning_type' => $type,
            ],
            [
                'delivery_fee_amount' => round($amount, 2),
                'currency' => 'PHP',
                'status' => $status,
                'earned_at' => $order->delivered_at,
                'released_at' => $status === 'available' ? now() : null,
                'metadata' => [
                    'source' => $source,
                    'order_number' => $order->order_number,
                    ...$metadata,
                ],
            ]
        );
    }

    private function resolveDirectRider(MarketplaceOrder $order, ?int $explicitId): CourierAccount
    {
        if ($explicitId) {
            return CourierAccount::query()->findOrFail($explicitId);
        }

        $email = strtolower(trim((string) $order->courier_email));
        $rider = $email !== ''
            ? CourierAccount::query()->whereRaw('LOWER(email) = ?', [$email])->first()
            : null;

        if (! $rider) {
            throw ValidationException::withMessages([
                'courier' => 'The completed direct-delivery Rider could not be resolved.',
            ]);
        }

        return $rider;
    }

    private function resolvePickupRider(MarketplaceOrder $order): ?CourierAccount
    {
        $email = strtolower(trim((string) $order->courier_email));

        if ($email === '') {
            return null;
        }

        return CourierAccount::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->where('logistics_account_id', $order->logistics_account_id)
            ->first();
    }

    private function resolveFinalMileRider(MarketplaceOrder $order, ?int $explicitId): ?CourierAccount
    {
        $riderId = $explicitId;

        if (! $riderId) {
            $riderId = DB::table('last_mile_assignment_orders as link')
                ->join(
                    'last_mile_assignments as assignment',
                    'assignment.id',
                    '=',
                    'link.last_mile_assignment_id'
                )
                ->where('link.marketplace_order_id', $order->id)
                ->value('assignment.courier_account_id');
        }

        if (! $riderId) {
            throw ValidationException::withMessages([
                'courier' => 'The final-mile Rider could not be resolved for the completed Logistics order.',
            ]);
        }

        $rider = CourierAccount::query()
            ->whereKey($riderId)
            ->where('logistics_account_id', $order->logistics_account_id)
            ->first();

        if (! $rider) {
            throw ValidationException::withMessages([
                'courier' => 'The final-mile Rider does not belong to the Logistics provider handling this order.',
            ]);
        }

        return $rider;
    }

    private function boundedPercent(mixed $value): float
    {
        return round(max(0, min(100, (float) $value)), 4);
    }
}
