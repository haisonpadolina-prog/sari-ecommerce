<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_order_id')
                ->constrained('marketplace_orders')
                ->restrictOnDelete();

            $table->string('idempotency_key', 160)->unique();
            $table->string('provider', 50)->default('internal');
            $table->string('provider_reference', 191)->nullable()->index();
            $table->string('channel', 50)->default('unknown');
            $table->string('type', 40)->default('collection');
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('PHP');
            $table->string('status', 30)->default('pending')->index();
            $table->dateTime('paid_at', 6)->nullable()->index();
            $table->dateTime('refunded_at', 6)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(
                ['marketplace_order_id', 'status'],
                'payment_transactions_order_status'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
