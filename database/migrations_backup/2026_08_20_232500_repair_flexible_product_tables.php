<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | PRODUCT OPTIONS
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('seller_product_options')) {
            Schema::create('seller_product_options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('seller_product_id')
                    ->constrained('seller_products')
                    ->cascadeOnDelete();

                $table->string('name', 60);
                $table->unsignedSmallInteger('position')->default(0);
                $table->timestamps();

                $table->index(
                    ['seller_product_id', 'position'],
                    'seller_product_options_product_position_idx'
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | OPTION VALUES
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('seller_product_option_values')) {
            Schema::create('seller_product_option_values', function (Blueprint $table) {
                $table->id();
                $table->foreignId('seller_product_option_id')
                    ->constrained('seller_product_options')
                    ->cascadeOnDelete();

                $table->string('value', 100);
                $table->unsignedSmallInteger('position')->default(0);
                $table->timestamps();

                $table->index(
                    ['seller_product_option_id', 'position'],
                    'seller_product_option_values_option_position_idx'
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | PRODUCT VARIANTS
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('seller_product_variants')) {
            Schema::create('seller_product_variants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('seller_product_id')
                    ->constrained('seller_products')
                    ->cascadeOnDelete();

                $table->string('combination_key', 512);
                $table->json('option_values');
                $table->string('sku', 120)->nullable();
                $table->decimal('price', 12, 2)->default(0);
                $table->unsignedInteger('stock')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(
                    ['seller_product_id', 'combination_key'],
                    'seller_variant_combination_unique'
                );

                $table->index(
                    ['seller_product_id', 'is_active'],
                    'seller_product_variants_product_active_idx'
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | PRODUCT SPECIFICATIONS
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('seller_product_specifications')) {
            Schema::create('seller_product_specifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('seller_product_id')
                    ->constrained('seller_products')
                    ->cascadeOnDelete();

                $table->string('name', 80);
                $table->string('value', 255);
                $table->unsignedSmallInteger('position')->default(0);
                $table->timestamps();

                $table->index(
                    ['seller_product_id', 'position'],
                    'seller_product_specifications_product_position_idx'
                );
            });
        }
    }

    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        |
        | This is a repair migration. We intentionally do not drop the flexible
        | product tables in down() so a rollback cannot accidentally remove
        | Seller option / variant / specification data.
        |
        */
    }
};
