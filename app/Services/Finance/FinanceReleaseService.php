<?php

namespace App\Services\Finance;

use App\Models\Finance\LogisticsEarning;
use App\Models\Finance\OrderCommission;
use App\Models\Finance\RiderEarning;
use App\Models\Finance\SellerSettlement;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FinanceReleaseService
{
    public function __construct(
        private readonly WalletService $wallets
    ) {
    }

    public function releaseOrder(MarketplaceOrder $order, string $source = 'verified_remittance'): void
    {
        DB::transaction(function () use ($order, $source): void {
            $locked = MarketplaceOrder::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->status !== 'delivered' || strtolower((string) $locked->payment_status) !== 'paid') {
                throw ValidationException::withMessages([
                    'order' => 'Funds can only be released for a delivered and paid order.',
                ]);
            }

            // Delivery alone is not proof that SARI holds the collected COD cash.
            // The remittance batch must have passed both physical cash counting
            // and Main Branch central reconciliation before Admin cash handover.
            if (strtoupper((string) $locked->payment_method) === 'COD') {
                $remittance = $locked->codRemittanceItem()->with('batch')->first()?->batch;

                if (! $remittance
                    || $remittance->remittance_method !== 'cash_hub'
                    || $remittance->status !== 'verified'
                    || ! $remittance->central_reconciled_at
                    || ! $remittance->verified_at
                    || abs((float) $remittance->expected_amount - (float) $remittance->confirmed_amount) >= 0.01
                    || ($locked->logistics_account_id
                        && (int) $remittance->logistics_account_id !== (int) $locked->logistics_account_id)) {
                    throw ValidationException::withMessages([
                        'remittance' => 'COD earnings cannot be released until the full cash collection is verified at the Logistics Main Branch and received by SARI Admin.',
                    ]);
                }

                $handover = $remittance->adminCashTurnoverItem()
                    ->with('turnover')
                    ->first();
                $turnover = $handover?->turnover;

                if (! $turnover
                    || $turnover->status !== 'received'
                    || ! $turnover->received_at
                    || ! $turnover->received_by_admin_account_id
                    || (int) $turnover->logistics_account_id !== (int) $remittance->logistics_account_id
                    || abs((float) $turnover->expected_amount - (float) $turnover->confirmed_amount) >= 0.01
                    || abs((float) $handover->expected_amount - (float) $remittance->confirmed_amount) >= 0.01) {
                    throw ValidationException::withMessages([
                        'turnover' => 'COD earnings stay pending until an authorized SARI Admin counts and receives the full physical cash turnover.',
                    ]);
                }
            }

            $commission = OrderCommission::query()
                ->where('marketplace_order_id', $locked->id)
                ->lockForUpdate()
                ->firstOrFail();

            $settlement = SellerSettlement::query()
                ->where('marketplace_order_id', $locked->id)
                ->lockForUpdate()
                ->firstOrFail();

            $platformWallet = $this->wallets->platformWallet();
            $sellerWallet = $this->wallets->wallet('seller', (int) $locked->seller_account_id);

            if ((float) $commission->net_commission > 0) {
                $this->wallets->credit(
                    $platformWallet,
                    (float) $commission->net_commission,
                    'platform_commission',
                    'order:'.$locked->id.':platform-commission',
                    $locked,
                    OrderCommission::class,
                    $commission->id,
                    'Platform commission released after payment/remittance verification.',
                    ['source' => $source]
                );
            }

            // Do not re-credit seller funds that the legacy system already
            // recorded as externally paid before wallet migration.
            if ($settlement->status !== 'paid' && (float) $settlement->seller_net_amount > 0) {
                $this->wallets->credit(
                    $sellerWallet,
                    (float) $settlement->seller_net_amount,
                    'seller_settlement',
                    'order:'.$locked->id.':seller-settlement',
                    $locked,
                    SellerSettlement::class,
                    $settlement->id,
                    'Seller net settlement released to the internal wallet.',
                    ['source' => $source]
                );
            }

            if ($settlement->status === 'payable') {
                $settlement->forceFill(['status' => 'available'])->save();
            }

            $logistics = LogisticsEarning::query()
                ->where('marketplace_order_id', $locked->id)
                ->lockForUpdate()
                ->first();

            if ($logistics && (float) $logistics->net_logistics_amount > 0) {
                $logisticsWallet = $this->wallets->wallet(
                    'logistics',
                    (int) $logistics->logistics_account_id
                );

                $this->wallets->credit(
                    $logisticsWallet,
                    (float) $logistics->net_logistics_amount,
                    'logistics_shipping_margin',
                    'order:'.$locked->id.':logistics-margin',
                    $locked,
                    LogisticsEarning::class,
                    $logistics->id,
                    'Logistics shipping margin released after payment/remittance verification.',
                    ['source' => $source]
                );

                $logistics->forceFill([
                    'status' => 'available',
                    'released_at' => now(),
                ])->save();
            }

            $riderEarnings = RiderEarning::query()
                ->where('marketplace_order_id', $locked->id)
                ->lockForUpdate()
                ->get();

            foreach ($riderEarnings as $earning) {
                // Legacy payout rows marked reserved/paid stay under the legacy
                // payout workflow and are never re-credited into the new wallet.
                if (in_array($earning->status, ['reserved', 'paid'], true)) {
                    continue;
                }

                if ((float) $earning->delivery_fee_amount <= 0) {
                    continue;
                }

                $riderWallet = $this->wallets->wallet('courier', (int) $earning->courier_account_id);

                $this->wallets->credit(
                    $riderWallet,
                    (float) $earning->delivery_fee_amount,
                    'rider_'.$earning->earning_type,
                    'rider-earning:'.$earning->id.':wallet-credit',
                    $locked,
                    RiderEarning::class,
                    $earning->id,
                    'Rider earning released to the internal wallet.',
                    [
                        'source' => $source,
                        'earning_type' => $earning->earning_type,
                    ]
                );

                if ($earning->status === 'pending_remittance') {
                    $earning->forceFill([
                        'status' => 'available',
                        'released_at' => now(),
                    ])->save();
                }
            }
        });
    }
}
