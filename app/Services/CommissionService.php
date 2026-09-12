<?php

namespace App\Services;

use App\Models\CommissionAdjustment;
use App\Models\CommissionAuditLog;
use App\Models\CommissionRate;
use App\Models\MarketplaceOrder;
use App\Models\OrderCommission;
use App\Models\PlatformSetting;
use App\Models\SellerSettlement;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class CommissionService
{
    public const BASIS = 'delivered_merchandise_subtotal';

    public function currentRate(?CarbonInterface $at = null): CommissionRate
    {
        return $this->rateFor($at ?? now());
    }

    public function rateFor(CarbonInterface $at): CommissionRate
    {
        $rate = CommissionRate::query()
            ->activeAt($at)
            ->orderByDesc('effective_from')
            ->orderByDesc('id')
            ->first();

        if ($rate) {
            return $rate;
        }

        $this->ensureInitialRate();

        $rate = CommissionRate::query()
            ->activeAt($at)
            ->orderByDesc('effective_from')
            ->orderByDesc('id')
            ->first();

        if (!$rate) {
            throw new RuntimeException('No commission rate is effective for the requested date.');
        }

        return $rate;
    }

    public function recordForDeliveredOrder(
        MarketplaceOrder $order,
        string $source = 'delivery'
    ): OrderCommission {
        return DB::transaction(function () use ($order, $source): OrderCommission {
            $lockedOrder = MarketplaceOrder::query()
                ->whereKey($order->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $existing = OrderCommission::query()
                ->where('marketplace_order_id', $lockedOrder->id)
                ->first();

            if ($existing) {
                return $existing;
            }

            if ($lockedOrder->status !== 'delivered' || !$lockedOrder->delivered_at) {
                throw ValidationException::withMessages([
                    'order' => 'Commission can only be recorded after an order is delivered with a delivered_at timestamp.',
                ]);
            }

            if (strtolower((string) $lockedOrder->payment_status) !== 'paid') {
                throw ValidationException::withMessages([
                    'payment' => 'Commission can only be earned after the delivered order is also paid.',
                ]);
            }

            $rate = $this->rateFor($lockedOrder->delivered_at);
            $eligibleAmount = round(max(0, (float) $lockedOrder->subtotal), 2);
            $ratePercent = (float) $rate->rate_percent;
            $grossCommission = round($eligibleAmount * ($ratePercent / 100), 2);

            $commission = OrderCommission::query()->create([
                'marketplace_order_id' => $lockedOrder->id,
                'seller_account_id' => $lockedOrder->seller_account_id,
                'commission_rate_id' => $rate->id,
                'eligible_amount' => $eligibleAmount,
                'rate_percent' => $ratePercent,
                'gross_commission' => $grossCommission,
                'adjustment_total' => 0,
                'net_commission' => $grossCommission,
                'status' => 'earned',
                'earned_at' => $lockedOrder->delivered_at,
                'source' => $source,
                'policy_snapshot' => [
                    'basis' => self::BASIS,
                    'eligible_field' => 'marketplace_orders.subtotal',
                    'excluded' => [
                        'delivery_fee',
                        'cancelled_orders',
                        'non_delivered_orders',
                    ],
                    'rate_percent' => $ratePercent,
                    'commission_rate_id' => $rate->id,
                ],
            ]);

            CommissionAuditLog::query()->create([
                'order_commission_id' => $commission->id,
                'commission_rate_id' => $rate->id,
                'admin_account_id' => null,
                'actor_type' => 'system',
                'action' => 'commission_recorded',
                'description' => 'Platform commission snapshotted when the order became delivered.',
                'new_values' => [
                    'eligible_amount' => $eligibleAmount,
                    'rate_percent' => $ratePercent,
                    'gross_commission' => $grossCommission,
                    'net_commission' => $grossCommission,
                    'status' => 'earned',
                ],
                'metadata' => [
                    'marketplace_order_id' => $lockedOrder->id,
                    'order_number' => $lockedOrder->order_number,
                    'source' => $source,
                ],
            ]);

            return $commission->fresh(['order', 'seller', 'rate']);
        });
    }

    public function setCurrentRate(
        float $ratePercent,
        ?int $adminAccountId = null,
        ?string $reason = null
    ): CommissionRate {
        if ($ratePercent < 0 || $ratePercent > 100) {
            throw ValidationException::withMessages([
                'commission_rate' => 'Commission rate must be between 0 and 100.',
            ]);
        }

        $ratePercent = round($ratePercent, 4);

        return DB::transaction(function () use (
            $ratePercent,
            $adminAccountId,
            $reason
        ): CommissionRate {
            $effectiveFrom = now();
            $current = CommissionRate::query()
                ->activeAt($effectiveFrom)
                ->orderByDesc('effective_from')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if (!$current) {
                $this->ensureInitialRate();
                $current = CommissionRate::query()
                    ->activeAt($effectiveFrom)
                    ->orderByDesc('effective_from')
                    ->orderByDesc('id')
                    ->lockForUpdate()
                    ->first();
            }

            if ($current && abs((float) $current->rate_percent - $ratePercent) < 0.00005) {
                PlatformSetting::putValue('commission_rate', $ratePercent);
                return $current;
            }

            $oldValues = $current ? [
                'commission_rate_id' => $current->id,
                'rate_percent' => (float) $current->rate_percent,
                'effective_from' => $current->effective_from?->toIso8601String(),
                'effective_until' => $current->effective_until?->toIso8601String(),
            ] : null;

            if ($current) {
                $current->forceFill([
                    'effective_until' => $effectiveFrom,
                ])->save();
            }

            $newRate = CommissionRate::query()->create([
                'rate_percent' => $ratePercent,
                'calculation_basis' => self::BASIS,
                'effective_from' => $effectiveFrom,
                'effective_until' => null,
                'changed_by_admin_id' => $adminAccountId,
                'reason' => $reason ?: 'Commission rate updated by an administrator.',
            ]);

            PlatformSetting::putValue('commission_rate', $ratePercent);

            CommissionAuditLog::query()->create([
                'commission_rate_id' => $newRate->id,
                'admin_account_id' => $adminAccountId,
                'actor_type' => $adminAccountId ? 'admin' : 'system',
                'action' => 'commission_rate_changed',
                'description' => $reason ?: 'Commission rate updated.',
                'old_values' => $oldValues,
                'new_values' => [
                    'commission_rate_id' => $newRate->id,
                    'rate_percent' => $ratePercent,
                    'effective_from' => $newRate->effective_from?->toIso8601String(),
                    'calculation_basis' => $newRate->calculation_basis,
                ],
            ]);

            return $newRate;
        });
    }

    public function addAdjustment(
        OrderCommission $commission,
        float $amount,
        string $type,
        string $reason,
        ?int $adminAccountId = null,
        ?string $reference = null,
        array $metadata = []
    ): CommissionAdjustment {
        if (round($amount, 2) === 0.0) {
            throw ValidationException::withMessages([
                'amount' => 'Commission adjustment amount cannot be zero.',
            ]);
        }

        $type = trim($type);
        $reason = trim($reason);

        if ($type === '' || $reason === '') {
            throw ValidationException::withMessages([
                'adjustment' => 'Adjustment type and reason are required.',
            ]);
        }

        return DB::transaction(function () use (
            $commission,
            $amount,
            $type,
            $reason,
            $adminAccountId,
            $reference,
            $metadata
        ): CommissionAdjustment {
            $locked = OrderCommission::query()
                ->whereKey($commission->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $adjustment = CommissionAdjustment::query()->create([
                'order_commission_id' => $locked->id,
                'type' => $type,
                'amount' => round($amount, 2),
                'reason' => $reason,
                'reference' => $reference,
                'created_by_admin_id' => $adminAccountId,
                'adjusted_at' => now(),
                'metadata' => $metadata ?: null,
            ]);

            $adjustmentTotal = round((float) $locked->adjustments()->sum('amount'), 2);
            $netCommission = round((float) $locked->gross_commission + $adjustmentTotal, 2);
            $status = $netCommission <= 0 ? 'reversed' : 'adjusted';

            $oldValues = [
                'adjustment_total' => (float) $locked->adjustment_total,
                'net_commission' => (float) $locked->net_commission,
                'status' => $locked->status,
            ];

            $locked->forceFill([
                'adjustment_total' => $adjustmentTotal,
                'net_commission' => $netCommission,
                'status' => $status,
            ])->save();

            $settlement = SellerSettlement::query()
                ->where('order_commission_id', $locked->id)
                ->lockForUpdate()
                ->first();

            if ($settlement) {
                $sellerNet = round(max(
                    0,
                    (float) $settlement->merchandise_amount
                        - max(0, $netCommission)
                        - (float) $settlement->withholding_tax_amount
                ), 2);

                $settlement->forceFill([
                    'platform_commission_amount' => max(0, $netCommission),
                    'seller_net_amount' => $sellerNet,
                ])->save();
            }

            CommissionAuditLog::query()->create([
                'order_commission_id' => $locked->id,
                'commission_rate_id' => $locked->commission_rate_id,
                'admin_account_id' => $adminAccountId,
                'actor_type' => $adminAccountId ? 'admin' : 'system',
                'action' => 'commission_adjusted',
                'description' => $reason,
                'old_values' => $oldValues,
                'new_values' => [
                    'adjustment_id' => $adjustment->id,
                    'adjustment_type' => $type,
                    'adjustment_amount' => round($amount, 2),
                    'adjustment_total' => $adjustmentTotal,
                    'net_commission' => $netCommission,
                    'status' => $status,
                ],
                'metadata' => [
                    'reference' => $reference,
                    ...$metadata,
                ],
            ]);

            return $adjustment;
        });
    }

    private function ensureInitialRate(): CommissionRate
    {
        $existing = CommissionRate::query()
            ->orderBy('effective_from')
            ->orderBy('id')
            ->first();

        if ($existing) {
            return $existing;
        }

        return DB::transaction(function (): CommissionRate {
            $existing = CommissionRate::query()
                ->orderBy('effective_from')
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return $existing;
            }

            $legacyRate = (float) PlatformSetting::valueOf('commission_rate', 10);
            $legacyRate = max(0, min(100, $legacyRate));
            $effectiveFrom = MarketplaceOrder::query()
                ->where('status', 'delivered')
                ->whereNotNull('delivered_at')
                ->min('delivered_at') ?: now();

            $rate = CommissionRate::query()->create([
                'rate_percent' => $legacyRate,
                'calculation_basis' => self::BASIS,
                'effective_from' => $effectiveFrom,
                'effective_until' => null,
                'changed_by_admin_id' => null,
                'reason' => 'Initial rate restored from platform_settings.',
            ]);

            CommissionAuditLog::query()->create([
                'commission_rate_id' => $rate->id,
                'admin_account_id' => null,
                'actor_type' => 'system',
                'action' => 'commission_rate_initialized',
                'description' => 'Initial commission rate restored from legacy platform settings.',
                'new_values' => [
                    'rate_percent' => $legacyRate,
                    'effective_from' => $rate->effective_from?->toIso8601String(),
                    'calculation_basis' => self::BASIS,
                ],
            ]);

            return $rate;
        });
    }
}
