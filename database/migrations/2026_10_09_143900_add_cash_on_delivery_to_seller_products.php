<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('seller_products', 'cash_on_delivery')) {
            Schema::table('seller_products', function (Blueprint $table) {
                $table->boolean('cash_on_delivery')
                    ->default(false)
                    ->after('free_shipping');
            });
        }

        if (Schema::hasTable('seller_product_versions')
            && !Schema::hasColumn('seller_product_versions', 'cash_on_delivery')) {
            Schema::table('seller_product_versions', function (Blueprint $table) {
                $table->boolean('cash_on_delivery')
                    ->default(false)
                    ->after('free_shipping');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('seller_product_versions')
            && Schema::hasColumn('seller_product_versions', 'cash_on_delivery')) {
            Schema::table('seller_product_versions', function (Blueprint $table) {
                $table->dropColumn('cash_on_delivery');
            });
        }

        if (Schema::hasColumn('seller_products', 'cash_on_delivery')) {
            Schema::table('seller_products', function (Blueprint $table) {
                $table->dropColumn('cash_on_delivery');
            });
        }
    }
};
