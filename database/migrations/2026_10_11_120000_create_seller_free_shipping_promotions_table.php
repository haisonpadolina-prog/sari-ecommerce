<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_free_shipping_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_account_id')
                ->constrained('seller_accounts')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('minimum_order_amount', 12, 2)->default(0);
            $table->decimal('maximum_shipping_discount', 12, 2);
            $table->dateTime('starts_at', 6)->index();
            $table->dateTime('ends_at', 6)->index();
            $table->string('status', 20)->default('active')->index();
            $table->dateTime('paused_at', 6)->nullable();
            $table->dateTime('ended_at', 6)->nullable();
            $table->timestamps();

            $table->index(
                ['seller_account_id', 'status', 'starts_at', 'ends_at'],
                'seller_free_shipping_schedule_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_free_shipping_promotions');
    }
};
