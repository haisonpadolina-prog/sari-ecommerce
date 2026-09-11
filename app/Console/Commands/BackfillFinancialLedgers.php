<?php

namespace App\Console\Commands;

use App\Models\MarketplaceOrder;
use App\Services\FinancialFlowService;
use Illuminate\Console\Command;

class BackfillFinancialLedgers extends Command
{
    protected $signature = 'finance:backfill
        {--dry-run : Count eligible rows without writing finance ledger records}
        {--force : Run without a production confirmation prompt}';

    protected $description = 'Backfill payment, commission, seller-payable, and rider-earning ledgers for delivered + paid marketplace orders.';

    public function handle(FinancialFlowService $finance): int
    {
        $eligible = MarketplaceOrder::query()
            ->where('status', 'delivered')
            ->where('payment_status', 'paid')
            ->whereNotNull('delivered_at');

        $count = (clone $eligible)->count();
        $unpaid = MarketplaceOrder::query()
            ->where('status', 'delivered')
            ->where(function ($query) {
                $query->whereNull('payment_status')
                    ->orWhere('payment_status', '!=', 'paid');
            })
            ->count();

        $this->info("Delivered + paid orders eligible for finance reconciliation: {$count}");

        if ($unpaid > 0) {
            $this->warn("Delivered orders not marked paid and intentionally skipped: {$unpaid}");
        }

        if ($this->option('dry-run')) {
            $this->comment('Dry run only. No database rows were changed.');
            return self::SUCCESS;
        }

        if ($count === 0) {
            $this->info('Nothing to backfill.');
            return self::SUCCESS;
        }

        if (app()->environment('production') && !$this->option('force')) {
            if (!$this->confirm('Create/reconcile finance ledgers for these delivered + paid orders?')) {
                $this->comment('Backfill cancelled.');
                return self::SUCCESS;
            }
        }

        $ok = 0;
        $failed = 0;

        $eligible->orderBy('id')->chunkById(100, function ($orders) use ($finance, &$ok, &$failed): void {
            foreach ($orders as $order) {
                try {
                    $finance->recordCompletedOrder($order, 'legacy_finance_backfill');
                    $ok++;
                } catch (\Throwable $error) {
                    $failed++;
                    $this->error(
                        'Failed order ' . ($order->order_number ?: $order->id) . ': ' . $error->getMessage()
                    );
                }
            }
        });

        $this->newLine();
        $this->info("Reconciled finance rows: {$ok}");

        if ($failed > 0) {
            $this->error("Failed orders: {$failed}");
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
