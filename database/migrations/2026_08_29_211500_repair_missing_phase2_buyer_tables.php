<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('buyer_cart_items')) {
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

                $table->index(
                    ['buyer_account_id', 'updated_at'],
                    'repair_cart_buyer_updated_idx'
                );

                $table->index(
                    ['buyer_social_account_id', 'updated_at'],
                    'repair_cart_social_updated_idx'
                );

                $table->index(
                    ['seller_product_id', 'seller_product_variant_id'],
                    'repair_cart_product_variant_idx'
                );
            });
        }

        if (!Schema::hasTable('buyer_seller_messages')) {
            Schema::create('buyer_seller_messages', function (Blueprint $table) {
                $table->id();

                $table->foreignId('seller_account_id')
                    ->constrained('seller_accounts')
                    ->cascadeOnDelete();

                $table->foreignId('buyer_account_id')
                    ->nullable()
                    ->constrained('buyer_accounts')
                    ->cascadeOnDelete();

                $table->foreignId('buyer_social_account_id')
                    ->nullable()
                    ->constrained('social_accounts')
                    ->cascadeOnDelete();

                $table->foreignId('marketplace_order_id')
                    ->nullable()
                    ->constrained('marketplace_orders')
                    ->nullOnDelete();

                $table->string('sender_role', 20);
                $table->text('body');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index(
                    ['seller_account_id', 'created_at'],
                    'repair_msg_seller_created_idx'
                );

                $table->index(
                    ['buyer_account_id', 'created_at'],
                    'repair_msg_buyer_created_idx'
                );

                $table->index(
                    ['buyer_social_account_id', 'created_at'],
                    'repair_msg_social_created_idx'
                );
            });
        }

        if (!Schema::hasTable('product_reviews')) {
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

                $table->index(
                    ['seller_account_id', 'created_at'],
                    'repair_reviews_seller_created_idx'
                );
            });
        }
    }

    public function down(): void
    {
        // Intentionally empty. This is a repair migration and should not
        // delete Buyer/Seller data if rolled back accidentally.
    }
};
