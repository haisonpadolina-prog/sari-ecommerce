<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_orders', function (Blueprint $table): void {
            $table->decimal('seller_receivable_subtotal', 12, 2)->nullable()->after('discount_amount');
            $table->decimal('platform_sale_discount_amount', 12, 2)->default(0)->after('seller_receivable_subtotal');
            $table->decimal('platform_voucher_discount_amount', 12, 2)->default(0)->after('platform_sale_discount_amount');
            $table->decimal('platform_discount_amount', 12, 2)->default(0)->after('platform_voucher_discount_amount');
            $table->string('platform_voucher_code', 64)->nullable()->after('platform_discount_amount');
            $table->json('platform_campaign_snapshot')->nullable()->after('platform_voucher_code');
        });
    }

    public function down(): void
    {
        Schema::table('marketplace_orders', function (Blueprint $table): void {
            $table->dropColumn([
                'seller_receivable_subtotal',
                'platform_sale_discount_amount',
                'platform_voucher_discount_amount',
                'platform_discount_amount',
                'platform_voucher_code',
                'platform_campaign_snapshot',
            ]);
        });
    }
};
