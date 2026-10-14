<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_orders', function (Blueprint $table) {
            $table->decimal('original_delivery_fee', 12, 2)
                ->default(0)
                ->after('subtotal');
            $table->decimal('shipping_discount_amount', 12, 2)
                ->default(0)
                ->after('original_delivery_fee');
            $table->foreignId('seller_free_shipping_promotion_id')
                ->nullable()
                ->after('shipping_discount_amount')
                ->constrained('seller_free_shipping_promotions')
                ->nullOnDelete();
            $table->string('shipping_discount_funder', 20)
                ->nullable()
                ->after('seller_free_shipping_promotion_id');
        });

        // Preserve the known courier fee for existing non-free-shipping orders.
        // Historical orders that already stored a zero delivery fee cannot be
        // safely inferred and are intentionally left unchanged.
        DB::table('marketplace_orders')
            ->where('delivery_fee', '>', 0)
            ->update([
                'original_delivery_fee' => DB::raw('delivery_fee'),
            ]);
    }

    public function down(): void
    {
        Schema::table('marketplace_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('seller_free_shipping_promotion_id');
            $table->dropColumn([
                'original_delivery_fee',
                'shipping_discount_amount',
                'shipping_discount_funder',
            ]);
        });
    }
};
