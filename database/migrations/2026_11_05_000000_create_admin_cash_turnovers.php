<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_cash_turnovers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('logistics_account_id')
                ->constrained('logistics_accounts')->restrictOnDelete();
            $table->foreignId('main_facility_id')
                ->constrained('logistics_facilities')->restrictOnDelete();
            $table->string('turnover_code', 40)->unique();
            $table->decimal('expected_amount', 14, 2);
            $table->decimal('submitted_amount', 14, 2);
            $table->decimal('confirmed_amount', 14, 2)->nullable();
            $table->decimal('difference_amount', 14, 2)->default(0);
            $table->char('currency', 3)->default('PHP');
            $table->string('status', 30)->default('submitted')->index();
            $table->text('logistics_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->unsignedBigInteger('prepared_by_logistics_account_id');
            $table->foreignId('received_by_admin_account_id')
                ->nullable()->constrained('admin_accounts')->restrictOnDelete();
            $table->dateTime('submitted_at', 6)->index();
            $table->dateTime('received_at', 6)->nullable();
            $table->timestamps();

            $table->index(['logistics_account_id', 'status', 'submitted_at'], 'admin_turnover_logistics_status_idx');
        });

        Schema::create('admin_cash_turnover_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('admin_cash_turnover_id')
                ->constrained('admin_cash_turnovers')->restrictOnDelete();
            // A Rider COD batch can only enter one Logistics -> SARI turnover.
            $table->foreignId('cod_remittance_batch_id')->unique()
                ->constrained('cod_remittance_batches')->restrictOnDelete();
            $table->decimal('expected_amount', 14, 2);
            $table->timestamps();
        });

        // Every actual Admin count is append-only, including discrepancies and retries.
        Schema::create('admin_cash_turnover_counts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('admin_cash_turnover_id')
                ->constrained('admin_cash_turnovers')->restrictOnDelete();
            $table->foreignId('admin_account_id')
                ->constrained('admin_accounts')->restrictOnDelete();
            $table->decimal('confirmed_amount', 14, 2);
            $table->decimal('difference_amount', 14, 2);
            $table->string('result', 30);
            $table->text('note')->nullable();
            $table->dateTime('counted_at', 6);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_cash_turnover_counts');
        Schema::dropIfExists('admin_cash_turnover_items');
        Schema::dropIfExists('admin_cash_turnovers');
    }
};
