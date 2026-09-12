<?php

namespace App\Services;

use App\Models\MarketplaceOrder;
use App\Models\OrderCommission;
use App\Models\SellerSettlement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SellerSettlementService
{
    public const WITHHOLDING_STATUS = 'not_evaluated';

    public function recordForCompletedOrder(
        MarketplaceOrder $order,
        OrderCommission $commission,
        string $source = 'order_completion'
    ): SellerSettlement {
        return DB::transaction(function () use ($order, $commission, $source): SellerSettlement {
            $lockedOrder = MarketplaceOrder::query()
                ->whereKey($order->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $lockedCommission = OrderCommission::query()
                ->whereKey($commission->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedOrder->status !== 'delivered' || !$lockedOrder->delivered_at) {
                throw ValidationException::withMessages([
                    'order' => 'Seller payable can only be recognized after delivery.',
                ]);
            }

            if (strtolower((string) $lockedOrder->payment_status) !== 'paid') {
                throw ValidationException::withMessages([
                    'payment' => 'Seller payable cannot be recognized until the order is paid.',
                ]);
            }

            if ((int) $lockedCommission->marketplace_order_id !== (int) $lockedOrder->id) {
                throw ValidationException::withMessages([
                    'commission' => 'Commission record does not belong to this order.',
                ]);
            }

            $merchandise = round(max(0, (float) $lockedOrder->subtotal), 2);
            $platformCommission = round(max(0, (float) $lockedCommission->net_commission), 2);

            /*
             * BIR RR 5-2025 prescribes 0.5% CWT on covered gross remittances by
             * e-marketplace operators/DFSPs. SARI does NOT auto-apply it here
             * because the current project has no verified seller tax profile,
             * threshold/declaration status, exemption data, or real remittance
             * provider. This keeps the ledger tax-aware without fabricating a
             * tax deduction that the system cannot yet substantiate.
             */
            $withholdingRate = 0.0;
            $withholdingTax = 0.0;
            $sellerNet = round(max(0, $merchandise - $platformCommission - $withholdingTax), 2);

            return SellerSettlement::query()->firstOrCreate(
                ['marketplace_order_id' => $lockedOrder->id],
                [
                    'seller_account_id' => $lockedOrder->seller_account_id,
                    'order_commission_id' => $lockedCommission->id,
                    'merchandise_amount' => $merchandise,
                    'platform_commission_amount' => $platformCommission,
                    'withholding_rate' => $withholdingRate,
                    'withholding_tax_amount' => $withholdingTax,
                    'seller_net_amount' => $sellerNet,
                    'currency' => 'PHP',
                    'status' => 'payable',
                    'withholding_status' => self::WITHHOLDING_STATUS,
                    'eligible_at' => $lockedOrder->delivered_at,
                    'policy_snapshot' => [
                        'source' => $source,
                        'financial_recognition' => 'delivered_and_paid',
                        'merchandise_field' => 'marketplace_orders.subtotal',
                        'delivery_fee_excluded_from_seller_merchandise_settlement' => true,
                        'commission_record_id' => $lockedCommission->id,
                        'commission_rate_percent' => (float) $lockedCommission->rate_percent,
                        'withholding' => [
                            'status' => self::WITHHOLDING_STATUS,
                            'amount' => 0,
                            'reason' => 'Seller tax applicability has not yet been verified by the application.',
                            'reference' => 'BIR RR 5-2025 / RMC 8-2024',
                        ],
                        'important' => 'payable means an internal obligation is recognized; it does not prove an external bank/e-wallet transfer occurred.',
                    ],
                ]
            );
        });
    }

    public function syncFromCommission(OrderCommission $commission): ?SellerSettlement
    {
        return DB::transaction(function () use ($commission): ?SellerSettlement {
            $lockedCommission = OrderCommission::query()
                ->whereKey($commission->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $settlement = SellerSettlement::query()
                ->where('order_commission_id', $lockedCommission->id)
                ->lockForUpdate()
                ->first();

            if (!$settlement) {
                return null;
            }

            $platformCommission = round(max(0, (float) $lockedCommission->net_commission), 2);
            $sellerNet = round(max(
                0,
                (float) $settlement->merchandise_amount
                    - $platformCommission
                    - (float) $settlement->withholding_tax_amount
            ), 2);

            $settlement->forceFill([
                'platform_commission_amount' => $platformCommission,
                'seller_net_amount' => $sellerNet,
                'status' => $lockedCommission->status === 'reversed'
                    ? 'payable'
                    : $settlement->status,
            ])->save();

            return $settlement->fresh();
        });
    }
}
