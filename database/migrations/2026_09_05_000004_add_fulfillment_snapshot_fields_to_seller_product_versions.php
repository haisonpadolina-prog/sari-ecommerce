<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_product_versions', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_product_versions', 'condition')) {
                $table->string('condition', 30)->nullable()->after('brand');
            }
            if (!Schema::hasColumn('seller_product_versions', 'low_stock_threshold')) {
                $table->unsignedInteger('low_stock_threshold')->default(5)->after('stock');
            }
            if (!Schema::hasColumn('seller_product_versions', 'free_shipping')) {
                $table->boolean('free_shipping')->default(false)->after('discount');
            }
            if (!Schema::hasColumn('seller_product_versions', 'package_weight')) {
                $table->decimal('package_weight', 10, 3)->nullable()->after('free_shipping');
            }
            if (!Schema::hasColumn('seller_product_versions', 'package_length')) {
                $table->decimal('package_length', 10, 2)->nullable()->after('package_weight');
            }
            if (!Schema::hasColumn('seller_product_versions', 'package_width')) {
                $table->decimal('package_width', 10, 2)->nullable()->after('package_length');
            }
            if (!Schema::hasColumn('seller_product_versions', 'package_height')) {
                $table->decimal('package_height', 10, 2)->nullable()->after('package_width');
            }
            if (!Schema::hasColumn('seller_product_versions', 'preparation_days')) {
                $table->unsignedSmallInteger('preparation_days')->nullable()->after('package_height');
            }
            if (!Schema::hasColumn('seller_product_versions', 'gallery_snapshot')) {
                $table->json('gallery_snapshot')->nullable()->after('variants_snapshot');
            }
        });
    }

    public function down(): void
    {
        $columns = array_values(array_filter([
            Schema::hasColumn('seller_product_versions', 'condition') ? 'condition' : null,
            Schema::hasColumn('seller_product_versions', 'low_stock_threshold') ? 'low_stock_threshold' : null,
            Schema::hasColumn('seller_product_versions', 'free_shipping') ? 'free_shipping' : null,
            Schema::hasColumn('seller_product_versions', 'package_weight') ? 'package_weight' : null,
            Schema::hasColumn('seller_product_versions', 'package_length') ? 'package_length' : null,
            Schema::hasColumn('seller_product_versions', 'package_width') ? 'package_width' : null,
            Schema::hasColumn('seller_product_versions', 'package_height') ? 'package_height' : null,
            Schema::hasColumn('seller_product_versions', 'preparation_days') ? 'preparation_days' : null,
            Schema::hasColumn('seller_product_versions', 'gallery_snapshot') ? 'gallery_snapshot' : null,
        ]));

        if ($columns) {
            Schema::table('seller_product_versions', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
