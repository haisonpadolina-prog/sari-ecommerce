<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('buyer_seller_messages')) {
            return;
        }

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

            // Short explicit index names for MySQL's 64-character limit.
            $table->index(
                ['seller_account_id', 'created_at'],
                'bsm_seller_created_idx'
            );

            $table->index(
                ['buyer_account_id', 'created_at'],
                'bsm_buyer_created_idx'
            );

            $table->index(
                ['buyer_social_account_id', 'created_at'],
                'bsm_social_created_idx'
            );

            $table->index(
                ['marketplace_order_id', 'created_at'],
                'bsm_order_created_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_seller_messages');
    }
};