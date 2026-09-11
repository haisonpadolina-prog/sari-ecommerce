<?php

namespace App\Services;

use App\Models\MarketplaceOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FinancialFlowService
{
    public function __construct(
        private readonly CommissionService $commissions,
        private readonly PaymentLedgerService $payments,
        private readonly SellerSettlementService $sellerSettlements,
        private readonly RiderEarningService $riderEarnings,
    ) {
    }

    /**
     * Record the internal financial ledgers for a completed marketplace order.
     *
     * This method recognizes obligations and earnings. It does not claim that
     * seller or rider funds were externally transferred by a bank/e-wallet.
     */
    public function recordCompletedOrder(
        MarketplaceOrder $order,
        string $source = 'order_workflow_delivery'
    ): array {
        return DB::transaction(function () use ($order, $source): array {
            $locked = MarketplaceOrder::query()
                ->whereKey($order->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status !== 'delivered' || !$locked->delivered_at) {
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
            $riderEarning = $this->riderEarnings->recordForCompletedOrder($locked, $source);

            return [
                'commission' => $commission,
                'payment' => $payment,
                'seller_settlement' => $sellerSettlement,
                'rider_earning' => $riderEarning,
            ];
        });
    }
}
