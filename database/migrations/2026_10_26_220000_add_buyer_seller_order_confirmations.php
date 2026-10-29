<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_orders', function (Blueprint $table): void {
            $table->timestamp('delivery_address_confirmed_at')->nullable();
        });

        Schema::table('buyer_seller_messages', function (Blueprint $table): void {
            $table->string('message_type', 40)->default('text');
            $table->foreignId('order_confirmation_order_id')->nullable()
                ->unique('buyer_seller_confirmation_order_unique')
                ->constrained('marketplace_orders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('buyer_seller_messages', function (Blueprint $table): void {
            $table->dropForeign(['order_confirmation_order_id']);
            $table->dropUnique('buyer_seller_confirmation_order_unique');
            $table->dropColumn(['message_type', 'order_confirmation_order_id']);
        });
        Schema::table('marketplace_orders', function (Blueprint $table): void {
            $table->dropColumn('delivery_address_confirmed_at');
        });
    }
};
