<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('buyer_cart_items')) {
            return;
        }

        Schema::create('buyer_cart_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('buyer_account_id')
                ->nullable()
                ->constrained('buyer_accounts')
                ->cascadeOnDelete();

            $table->foreignId('buyer_social_account_id')
                ->nullable()
                ->constrained('social_accounts')
                ->cascadeOnDelete();

            $table->foreignId('seller_product_id')
                ->constrained('seller_products')
                ->cascadeOnDelete();

            $table->foreignId('seller_product_variant_id')
                ->nullable()
                ->constrained('seller_product_variants')
                ->cascadeOnDelete();

            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->index(['buyer_account_id', 'updated_at']);
            $table->index(['buyer_social_account_id', 'updated_at']);
            $table->index(['seller_product_id', 'seller_product_variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_cart_items');
    }
};
