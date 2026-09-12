<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rider_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_order_id')
                ->unique()
                ->constrained('marketplace_orders')
                ->restrictOnDelete();
            $table->foreignId('courier_account_id')
                ->constrained('courier_accounts')
                ->restrictOnDelete();

            $table->decimal('delivery_fee_amount', 12, 2);
            $table->char('currency', 3)->default('PHP');
            $table->string('status', 30)->default('available')->index();
            $table->dateTime('earned_at', 6)->index();
            $table->dateTime('paid_at', 6)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(
                ['courier_account_id', 'status', 'earned_at'],
                'rider_earnings_courier_status_earned'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rider_earnings');
    }
};
