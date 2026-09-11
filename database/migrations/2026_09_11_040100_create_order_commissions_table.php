<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_order_id')
                ->unique()
                ->constrained('marketplace_orders')
                ->restrictOnDelete();
            $table->foreignId('seller_account_id')
                ->constrained('seller_accounts')
                ->restrictOnDelete();
            $table->foreignId('commission_rate_id')
                ->constrained('commission_rates')
                ->restrictOnDelete();

            $table->decimal('eligible_amount', 12, 2);
            $table->decimal('rate_percent', 7, 4);
            $table->decimal('gross_commission', 12, 2);
            $table->decimal('adjustment_total', 12, 2)->default(0);
            $table->decimal('net_commission', 12, 2);
            $table->string('status', 30)->default('earned')->index();
            $table->dateTime('earned_at', 6)->index();
            $table->string('source', 50)->default('delivery');
            $table->json('policy_snapshot')->nullable();
            $table->timestamps();

            $table->index(['seller_account_id', 'earned_at'], 'order_commissions_seller_earned');
            $table->index(['status', 'earned_at'], 'order_commissions_status_earned');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_commissions');
    }
};
