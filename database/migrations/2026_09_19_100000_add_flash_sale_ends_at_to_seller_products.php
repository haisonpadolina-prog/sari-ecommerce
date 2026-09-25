<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('seller_products') && !Schema::hasColumn('seller_products', 'flash_sale_ends_at')) {
            Schema::table('seller_products', function (Blueprint $table) {
                $table->timestamp('flash_sale_ends_at')->nullable()->after('discount');
            });
        }

        if (Schema::hasTable('seller_product_versions') && !Schema::hasColumn('seller_product_versions', 'flash_sale_ends_at')) {
            Schema::table('seller_product_versions', function (Blueprint $table) {
                $table->timestamp('flash_sale_ends_at')->nullable()->after('discount');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('seller_product_versions') && Schema::hasColumn('seller_product_versions', 'flash_sale_ends_at')) {
            Schema::table('seller_product_versions', function (Blueprint $table) {
                $table->dropColumn('flash_sale_ends_at');
            });
        }

        if (Schema::hasTable('seller_products') && Schema::hasColumn('seller_products', 'flash_sale_ends_at')) {
            Schema::table('seller_products', function (Blueprint $table) {
                $table->dropColumn('flash_sale_ends_at');
            });
        }
    }
};
