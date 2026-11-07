<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['seller_products', 'seller_product_versions'] as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'flash_sale_starts_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->timestamp('flash_sale_starts_at')->nullable()->after('discount');
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['seller_product_versions', 'seller_products'] as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'flash_sale_starts_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('flash_sale_starts_at');
                });
            }
        }
    }
};
