<?php

namespace App\Console\Commands;

use App\Models\Orders\MarketplaceOrder;
use App\Models\Promotions\PlatformShippingRedemption;
use App\Services\Promotions\PlatformShippingBudgetService;
use Illuminate\Console\Command;

class ReconcilePlatformShipping extends Command
{
    protected $signature = 'sari:shipping-budget-reconcile';
    protected $description = 'Finalize reserved shipping subsidies for delivered/cancelled orders after bulk imports or interrupted work.';

    public function handle(PlatformShippingBudgetService $budget): int
    {
        $count = 0;
        PlatformShippingRedemption::query()->where('state', 'reserved')
            ->orderBy('id')->chunkById(200, function ($records) use ($budget, &$count): void {
                foreach ($records as $record) {
                    $order = MarketplaceOrder::query()->find($record->marketplace_order_id);
                    if ($order && in_array($order->status, ['delivered', 'cancelled'], true)) {
                        $budget->syncOrder($order); $count++;
                    }
                }
            });
        $this->info("Reconciled {$count} shipping reservations.");
        return self::SUCCESS;
    }
}
