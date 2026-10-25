<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_wallets', function (Blueprint $table): void {
            $table->id();
            $table->string('owner_type', 30);
            $table->unsignedBigInteger('owner_id');
            $table->char('currency', 3)->default('PHP');
            $table->decimal('available_balance', 14, 2)->default(0);
            $table->decimal('reserved_balance', 14, 2)->default(0);
            $table->decimal('lifetime_credits', 14, 2)->default(0);
            $table->decimal('lifetime_payouts', 14, 2)->default(0);
            $table->timestamps();

            $table->unique(
                ['owner_type', 'owner_id', 'currency'],
                'finance_wallets_owner_currency_unique'
            );
        });

        Schema::create('finance_wallet_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('finance_wallet_id')
                ->constrained('finance_wallets')
                ->cascadeOnDelete();
            $table->foreignId('marketplace_order_id')
                ->nullable()
                ->constrained('marketplace_orders')
                ->nullOnDelete();
            $table->string('idempotency_key', 191)->unique();
            $table->string('entry_type', 30);
            $table->string('category', 60)->index();
            $table->decimal('amount', 14, 2);
            $table->decimal('available_delta', 14, 2)->default(0);
            $table->decimal('reserved_delta', 14, 2)->default(0);
            $table->decimal('available_balance_after', 14, 2);
            $table->decimal('reserved_balance_after', 14, 2);
            $table->string('source_type', 80)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->dateTime('occurred_at', 6)->index();
            $table->timestamps();

            $table->index(
                ['finance_wallet_id', 'occurred_at'],
                'finance_wallet_entries_wallet_occurred'
            );
        });

        Schema::create('finance_payout_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('finance_wallet_id')
                ->constrained('finance_wallets')
                ->restrictOnDelete();
            $table->string('request_code', 40)->unique();
            $table->string('owner_type', 30)->index();
            $table->unsignedBigInteger('owner_id')->index();
            $table->decimal('amount', 14, 2);
            $table->char('currency', 3)->default('PHP');
            $table->string('payout_method', 30);
            $table->string('destination_label', 191);
            $table->text('destination_details');
            $table->string('status', 30)->default('pending')->index();
            $table->text('admin_note')->nullable();
            $table->foreignId('reviewed_by_admin_id')
                ->nullable()
                ->constrained('admin_accounts')
                ->nullOnDelete();
            $table->string('external_reference', 191)->nullable()->index();
            $table->dateTime('requested_at', 6)->index();
            $table->dateTime('reviewed_at', 6)->nullable();
            $table->dateTime('paid_at', 6)->nullable();
            $table->timestamps();

            $table->index(
                ['owner_type', 'owner_id', 'status'],
                'finance_payout_requests_owner_status'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_payout_requests');
        Schema::dropIfExists('finance_wallet_entries');
        Schema::dropIfExists('finance_wallets');
    }
};
