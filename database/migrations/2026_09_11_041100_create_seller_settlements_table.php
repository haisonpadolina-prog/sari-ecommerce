<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_order_id')
                ->unique()
                ->constrained('marketplace_orders')
                ->restrictOnDelete();
            $table->foreignId('seller_account_id')
                ->constrained('seller_accounts')
                ->restrictOnDelete();
            $table->foreignId('order_commission_id')
                ->unique()
                ->constrained('order_commissions')
                ->restrictOnDelete();

            $table->decimal('merchandise_amount', 12, 2);
            $table->decimal('platform_commission_amount', 12, 2);
            $table->decimal('withholding_rate', 7, 4)->default(0);
            $table->decimal('withholding_tax_amount', 12, 2)->default(0);
            $table->decimal('seller_net_amount', 12, 2);
            $table->char('currency', 3)->default('PHP');

            // payable = internal obligation recognized; it does NOT mean money was transferred.
            $table->string('status', 30)->default('payable')->index();
            $table->string('withholding_status', 40)->default('not_evaluated')->index();
            $table->dateTime('eligible_at', 6)->index();
            $table->dateTime('paid_at', 6)->nullable();
            $table->string('external_settlement_reference', 191)->nullable()->index();
            $table->json('policy_snapshot')->nullable();
            $table->timestamps();

            $table->index(
                ['seller_account_id', 'eligible_at'],
                'seller_settlements_seller_eligible'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_settlements');
    }
};
