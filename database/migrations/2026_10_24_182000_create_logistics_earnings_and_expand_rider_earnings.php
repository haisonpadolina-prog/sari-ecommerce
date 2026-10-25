<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_earnings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('marketplace_order_id')
                ->unique()
                ->constrained('marketplace_orders')
                ->restrictOnDelete();
            $table->foreignId('logistics_account_id')
                ->constrained('logistics_accounts')
                ->restrictOnDelete();
            $table->decimal('gross_shipping_revenue', 14, 2);
            $table->decimal('pickup_rider_cost', 14, 2)->default(0);
            $table->decimal('last_mile_rider_cost', 14, 2)->default(0);
            $table->decimal('net_logistics_amount', 14, 2);
            $table->char('currency', 3)->default('PHP');
            $table->string('status', 30)->default('pending_remittance')->index();
            $table->dateTime('eligible_at', 6)->index();
            $table->dateTime('released_at', 6)->nullable();
            $table->json('policy_snapshot')->nullable();
            $table->timestamps();

            $table->index(
                ['logistics_account_id', 'status', 'eligible_at'],
                'logistics_earnings_provider_status'
            );
        });

        // MySQL requires an index to remain available for the existing
        // marketplace_order_id foreign key. Add a non-unique supporting
        // index before removing the legacy one-order/one-rider UNIQUE index.
        Schema::table('rider_earnings', function (Blueprint $table): void {
            $table->index(
                'marketplace_order_id',
                'rider_earnings_order_fk_idx'
            );
        });

        Schema::table('rider_earnings', function (Blueprint $table): void {
            $table->dropUnique('rider_earnings_marketplace_order_id_unique');
            $table->string('earning_type', 40)
                ->default('direct_delivery')
                ->after('courier_account_id');
            $table->dateTime('released_at', 6)
                ->nullable()
                ->after('earned_at');
            $table->unique(
                ['marketplace_order_id', 'courier_account_id', 'earning_type'],
                'rider_earnings_order_rider_type_unique'
            );
        });

        if (Schema::hasTable('marketplace_orders')) {
            // Legacy finance created one full-fee Rider earning at final delivery.
            // For Logistics-managed orders, normalize unpaid/unreserved legacy
            // rows into the last-mile leg before the new pickup + Logistics split
            // is backfilled. Historical paid/reserved earnings are preserved.
            $lastMilePercent = 45.0;

            if (Schema::hasTable('platform_settings')) {
                $configured = DB::table('platform_settings')
                    ->where('key', 'shipping_last_mile_rider_percent')
                    ->value('value');

                if (is_numeric($configured)) {
                    $lastMilePercent = max(0, min(100, (float) $configured));
                }
            }

            DB::table('marketplace_orders')
                ->whereNotNull('logistics_account_id')
                ->select(['id', 'original_delivery_fee', 'delivery_fee'])
                ->orderBy('id')
                ->chunkById(200, function ($orders) use ($lastMilePercent): void {
                    foreach ($orders as $order) {
                        $grossShipping = (float) $order->original_delivery_fee > 0
                            ? (float) $order->original_delivery_fee
                            : (float) $order->delivery_fee;

                        $lastMileAmount = round(
                            max(0, $grossShipping) * ($lastMilePercent / 100),
                            2
                        );

                        DB::table('rider_earnings')
                            ->where('marketplace_order_id', $order->id)
                            ->where('earning_type', 'direct_delivery')
                            ->whereNotIn('status', ['paid', 'reserved'])
                            ->update([
                                'earning_type' => 'last_mile_leg',
                                'delivery_fee_amount' => $lastMileAmount,
                            ]);

                        // Preserve the historical amount/status of legacy payouts,
                        // but label the work correctly so later services cannot
                        // create a second final-mile earning for the same Rider.
                        DB::table('rider_earnings')
                            ->where('marketplace_order_id', $order->id)
                            ->where('earning_type', 'direct_delivery')
                            ->whereIn('status', ['paid', 'reserved'])
                            ->update([
                                'earning_type' => 'last_mile_leg',
                            ]);
                    }
                });

            $codOrderIds = DB::table('marketplace_orders')
                ->whereRaw('UPPER(payment_method) = ?', ['COD'])
                ->where('status', 'delivered')
                ->pluck('id');

            if ($codOrderIds->isNotEmpty()) {
                DB::table('rider_earnings')
                    ->whereIn('marketplace_order_id', $codOrderIds)
                    ->where('status', 'available')
                    ->update(['status' => 'pending_remittance']);
            }
        }
    }

    public function down(): void
    {
        Schema::table('rider_earnings', function (Blueprint $table): void {
            $table->dropUnique('rider_earnings_order_rider_type_unique');
            $table->dropColumn(['earning_type', 'released_at']);
            $table->unique('marketplace_order_id');
        });

        // The recreated unique index now satisfies the FK, so the temporary
        // non-unique support index is no longer needed on rollback.
        Schema::table('rider_earnings', function (Blueprint $table): void {
            $table->dropIndex('rider_earnings_order_fk_idx');
        });

        Schema::dropIfExists('logistics_earnings');
    }
};
