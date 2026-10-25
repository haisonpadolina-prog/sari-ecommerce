<?php

namespace App\Services\Finance;

use App\Models\Orders\MarketplaceOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FinancialFlowService
{
    public function __construct(
        private readonly CommissionService $commissions,
        private readonly PaymentLedgerService $payments,
        private readonly SellerSettlementService $sellerSettlements,
        private readonly ShippingEarningService $shippingEarnings,
        private readonly FinanceReleaseService $release,
    ) {
    }

    /**
     * Record all internal obligations for a completed marketplace order.
     *
     * COD is deliberately two-stage:
     * 1. Delivery confirms Buyer payment and creates internal obligations.
     * 2. Verified Rider remittance releases those obligations to wallets.
     *
     * Non-COD paid orders can release immediately because no Rider cash
     * custody remains to reconcile.
     */
    public function recordCompletedOrder(
        MarketplaceOrder $order,
        string $source = 'order_workflow_delivery',
        ?int $riderEarningCourierId = null
    ): array {
        return DB::transaction(function () use (
            $order,
            $source,
            $riderEarningCourierId
        ): array {
            $locked = MarketplaceOrder::query()
                ->whereKey($order->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status !== 'delivered' || ! $locked->delivered_at) {
                throw ValidationException::withMessages([
                    'order' => 'Financial completion requires a delivered order.',
                ]);
            }

            if (strtolower((string) $locked->payment_status) !== 'paid') {
                throw ValidationException::withMessages([
                    'payment' => 'Financial completion requires a paid order.',
                ]);
            }

            $commission = $this->commissions->recordForDeliveredOrder($locked, $source);
            $payment = $this->payments->recordCompletedOrderCollection($locked, $source);
            $sellerSettlement = $this->sellerSettlements->recordForCompletedOrder(
                $locked,
                $commission,
                $source
            );
            $shipping = $this->shippingEarnings->recordForCompletedOrder(
                $locked,
                $source,
                $riderEarningCourierId
            );

            $isCod = strtoupper((string) $locked->payment_method) === 'COD';

            if (! $isCod) {
                $this->release->releaseOrder($locked, 'non_cod_completed_payment');
            }

            return [
                'commission' => $commission,
                'payment' => $payment,
                'seller_settlement' => $sellerSettlement,
                'rider_earning' => $shipping['primary_rider_earning'],
                'rider_earnings' => $shipping['rider_earnings'],
                'logistics_earning' => $shipping['logistics_earning'],
                'wallet_release_status' => $isCod ? 'awaiting_cod_remittance' : 'released',
            ];
        });
    }
}
