<?php

namespace App\Console\Commands;

use App\Models\MarketplaceOrder;
use App\Services\CommissionService;
use Illuminate\Console\Command;

class BackfillOrderCommissions extends Command
{
    protected $signature = 'commissions:backfill
        {--dry-run : Count eligible legacy delivered+paid orders without writing commission rows}
        {--force : Run without a production confirmation prompt}';

    protected $description = 'Create immutable commission snapshots for legacy orders that are both delivered and paid.';

    public function handle(CommissionService $commissions): int
    {
        $eligibleQuery = MarketplaceOrder::query()
            ->where('status', 'delivered')
            ->where('payment_status', 'paid')
            ->whereNotNull('delivered_at')
            ->whereDoesntHave('commission');

        $missingCount = (clone $eligibleQuery)->count();
        $missingTimestampCount = MarketplaceOrder::query()
            ->where('status', 'delivered')
            ->where('payment_status', 'paid')
            ->whereNull('delivered_at')
            ->whereDoesntHave('commission')
            ->count();
        $unpaidDeliveredCount = MarketplaceOrder::query()
            ->where('status', 'delivered')
            ->where(function ($query) {
                $query->whereNull('payment_status')
                    ->orWhere('payment_status', '!=', 'paid');
            })
            ->whereDoesntHave('commission')
            ->count();

        $this->info("Delivered + paid orders missing commission snapshots: {$missingCount}");

        if ($missingTimestampCount > 0) {
            $this->warn("Skipped paid delivered orders with no delivered_at timestamp: {$missingTimestampCount}");
        }

        if ($unpaidDeliveredCount > 0) {
            $this->warn("Skipped delivered orders not marked paid: {$unpaidDeliveredCount}");
            $this->warn('They require payment-status review; this command will not infer payment that the database cannot prove.');
        }

        if ($missingCount === 0) {
            $this->info('Nothing to backfill.');
            return self::SUCCESS;
        }

        $this->warn('Legacy note: the old system did not store per-order historical commission rates.');
        $this->warn('Backfill uses the migrated commission-rate timeline, whose initial rate came from platform_settings.');

        if ($this->option('dry-run')) {
            $this->comment('Dry run only. No database rows were changed.');
            return self::SUCCESS;
        }

        if (app()->environment('production') && !$this->option('force')) {
            if (!$this->confirm('Create immutable commission snapshots for these delivered + paid orders?')) {
                $this->comment('Backfill cancelled.');
                return self::SUCCESS;
            }
        }

        $created = 0;
        $failed = 0;

        $eligibleQuery
            ->orderBy('id')
            ->chunkById(100, function ($orders) use ($commissions, &$created, &$failed): void {
                foreach ($orders as $order) {
                    try {
                        $commissions->recordForDeliveredOrder($order, 'legacy_backfill');
                        $created++;
                    } catch (\Throwable $error) {
                        $failed++;
                        $this->error(
                            'Failed order ' . ($order->order_number ?: $order->id) . ': ' . $error->getMessage()
                        );
                    }
                }
            });

        $this->newLine();
        $this->info("Created commission snapshots: {$created}");

        if ($failed > 0) {
            $this->error("Failed orders: {$failed}");
            return self::FAILURE;
        }

        $this->info('Commission backfill completed successfully.');
        return self::SUCCESS;
    }
}
