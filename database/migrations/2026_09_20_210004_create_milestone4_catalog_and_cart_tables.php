<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();

                $table->foreignId('seller_id')
                    ->constrained()
                    ->restrictOnDelete();

                $table->foreignId('category_id')
                    ->constrained()
                    ->restrictOnDelete();

                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->softDeletes();
                $table->timestamps();

                $table->index(['seller_id', 'is_active']);
                $table->index(['category_id', 'is_active']);
            });
        }

        if (!Schema::hasTable('product_variants')) {
            Schema::create('product_variants', function (Blueprint $table) {
                $table->id();

                $table->foreignId('product_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->string('sku')->unique();
                $table->string('name');
                $table->json('options')->nullable();

                // Integer centavos, never decimal money.
                $table->unsignedInteger('price_minor');

                $table->unsignedInteger('stock')->default(0);
                $table->unsignedInteger('weight_grams')->default(0);
                $table->boolean('is_active')->default(true);
                $table->softDeletes();
                $table->timestamps();

                $table->index(['product_id', 'is_active']);
            });
        }

        if (!Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();

                $table->foreignId('product_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->string('path');
                $table->unsignedInteger('position')->default(0);
                $table->timestamps();

                $table->index(['product_id', 'position']);
            });
        }

        if (!Schema::hasTable('carts')) {
            Schema::create('carts', function (Blueprint $table) {
                $table->id();

                $table->foreignId('user_id')
                    ->unique()
                    ->constrained()
                    ->cascadeOnDelete();

                $table->timestamps();
            });
        }

        if (!Schema::hasTable('cart_items')) {
            Schema::create('cart_items', function (Blueprint $table) {
                $table->id();

                $table->foreignId('cart_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('product_variant_id')
                    ->constrained()
                    ->restrictOnDelete();

                $table->unsignedInteger('quantity')->default(1);
                $table->boolean('selected')->default(true);
                $table->timestamps();

                $table->unique(['cart_id', 'product_variant_id']);
                $table->index(['cart_id', 'selected']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
    }
};
