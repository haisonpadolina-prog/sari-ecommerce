<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cod_remittance_batches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('logistics_account_id')
                ->nullable()
                ->constrained('logistics_accounts')
                ->restrictOnDelete();
            $table->foreignId('courier_account_id')
                ->constrained('courier_accounts')
                ->restrictOnDelete();
            $table->string('batch_code', 40)->unique();
            $table->decimal('expected_amount', 14, 2);
            $table->decimal('submitted_amount', 14, 2);
            $table->decimal('confirmed_amount', 14, 2)->nullable();
            $table->decimal('difference_amount', 14, 2)->default(0);
            $table->char('currency', 3)->default('PHP');
            $table->string('remittance_method', 30)->default('cash_hub');
            $table->string('status', 30)->default('submitted')->index();
            $table->string('rider_reference', 191)->nullable();
            $table->string('logistics_reference', 191)->nullable();
            $table->text('rider_note')->nullable();
            $table->text('logistics_note')->nullable();
            $table->string('verified_by_type', 30)->nullable();
            $table->unsignedBigInteger('verified_by_id')->nullable();
            $table->dateTime('submitted_at', 6)->index();
            $table->dateTime('verified_at', 6)->nullable();
            $table->timestamps();

            $table->index(
                ['logistics_account_id', 'status', 'submitted_at'],
                'cod_remittance_batches_logistics_status'
            );
            $table->index(
                ['courier_account_id', 'status', 'submitted_at'],
                'cod_remittance_batches_rider_status'
            );
        });

        Schema::create('cod_remittance_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cod_remittance_batch_id')
                ->constrained('cod_remittance_batches')
                ->cascadeOnDelete();
            $table->foreignId('marketplace_order_id')
                ->unique()
                ->constrained('marketplace_orders')
                ->restrictOnDelete();
            $table->decimal('expected_amount', 14, 2);
            $table->dateTime('collected_at', 6)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cod_remittance_items');
        Schema::dropIfExists('cod_remittance_batches');
    }
};
