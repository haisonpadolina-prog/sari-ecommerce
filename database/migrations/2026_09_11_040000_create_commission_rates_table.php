<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('rate_percent', 7, 4);
            $table->string('calculation_basis', 80)->default('delivered_merchandise_subtotal');
            $table->dateTime('effective_from', 6)->index();
            $table->dateTime('effective_until', 6)->nullable()->index();
            $table->foreignId('changed_by_admin_id')
                ->nullable()
                ->constrained('admin_accounts')
                ->nullOnDelete();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['effective_from', 'effective_until'], 'commission_rates_effective_window');
        });

        // The legacy application stored only one mutable commission_rate value.
        // Seed one historical baseline so existing delivered orders can be
        // snapshotted without inventing a different rate per order.
        $legacyRate = 10.0;

        if (Schema::hasTable('platform_settings')) {
            $storedRate = DB::table('platform_settings')
                ->where('key', 'commission_rate')
                ->value('value');

            if (is_numeric($storedRate)) {
                $legacyRate = max(0.0, min(100.0, (float) $storedRate));
            }
        }

        $effectiveFrom = now();

        if (Schema::hasTable('marketplace_orders')) {
            $firstDeliveredAt = DB::table('marketplace_orders')
                ->where('status', 'delivered')
                ->whereNotNull('delivered_at')
                ->min('delivered_at');

            if ($firstDeliveredAt) {
                $effectiveFrom = $firstDeliveredAt;
            }
        }

        DB::table('commission_rates')->insert([
            'rate_percent' => number_format($legacyRate, 4, '.', ''),
            'calculation_basis' => 'delivered_merchandise_subtotal',
            'effective_from' => $effectiveFrom,
            'effective_until' => null,
            'changed_by_admin_id' => null,
            'reason' => 'Initial rate migrated from the legacy platform_settings commission_rate value.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_rates');
    }
};
