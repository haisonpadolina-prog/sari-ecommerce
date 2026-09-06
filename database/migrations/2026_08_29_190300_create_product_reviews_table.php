<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_reviews')) {
            return;
        }

        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('seller_product_id')
                ->constrained('seller_products')
                ->cascadeOnDelete();

            $table->foreignId('seller_account_id')
                ->constrained('seller_accounts')
                ->cascadeOnDelete();

            $table->foreignId('marketplace_order_id')
                ->constrained('marketplace_orders')
                ->cascadeOnDelete();

            $table->foreignId('buyer_account_id')
                ->nullable()
                ->constrained('buyer_accounts')
                ->nullOnDelete();

            $table->foreignId('buyer_social_account_id')
                ->nullable()
                ->constrained('social_accounts')
                ->nullOnDelete();

            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(
                ['marketplace_order_id', 'seller_product_id'],
                'product_reviews_order_product_unique'
            );

            $table->index(['seller_account_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
