<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('seller_products') && !Schema::hasColumn('seller_products', 'free_shipping')) {
            Schema::table('seller_products', function (Blueprint $table) {
                $table->boolean('free_shipping')
                    ->default(false)
                    ->after('discount');
            });
        }

        /*
        | Preserve Seller product history when the version table exists.
        | The controller snapshots this value on future edits.
        */
        if (
            Schema::hasTable('seller_product_versions')
            && !Schema::hasColumn('seller_product_versions', 'free_shipping')
        ) {
            Schema::table('seller_product_versions', function (Blueprint $table) {
                $table->boolean('free_shipping')
                    ->default(false)
                    ->after('discount');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('seller_product_versions') && Schema::hasColumn('seller_product_versions', 'free_shipping')) {
            Schema::table('seller_product_versions', function (Blueprint $table) {
                $table->dropColumn('free_shipping');
            });
        }

        if (Schema::hasTable('seller_products') && Schema::hasColumn('seller_products', 'free_shipping')) {
            Schema::table('seller_products', function (Blueprint $table) {
                $table->dropColumn('free_shipping');
            });
        }
    }
};
