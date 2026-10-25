<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cod_remittance_batches', function (Blueprint $table): void {
            $table->dateTime('central_reconciled_at', 6)
                ->nullable()
                ->after('verified_at')
                ->index();
        });

        // Before this two-stage treasury model, an exact Rider remittance
        // immediately released wallets. Preserve that historical fact so old
        // verified batches do not re-enter the new Branch -> Main queue.
        DB::table('cod_remittance_batches')
            ->where('status', 'verified')
            ->whereNull('central_reconciled_at')
            ->update([
                'central_reconciled_at' => DB::raw('verified_at'),
                'updated_at' => now(),
            ]);

        Schema::create('branch_remittance_batches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('logistics_account_id')
                ->constrained('logistics_accounts')
                ->restrictOnDelete();
            $table->foreignId('origin_facility_id')
                ->constrained('logistics_facilities')
                ->restrictOnDelete();
            $table->foreignId('destination_facility_id')
                ->constrained('logistics_facilities')
                ->restrictOnDelete();
            $table->string('batch_code', 40)->unique();
            $table->string('origin_name_snapshot', 191);
            $table->string('destination_name_snapshot', 191);
            $table->decimal('expected_amount', 14, 2);
            $table->decimal('submitted_amount', 14, 2);
            $table->decimal('confirmed_amount', 14, 2)->nullable();
            $table->decimal('difference_amount', 14, 2)->default(0);
            $table->char('currency', 3)->default('PHP');
            $table->string('status', 30)->default('submitted')->index();
            $table->text('branch_note')->nullable();
            $table->text('main_branch_note')->nullable();
            $table->string('prepared_by_type', 40)->nullable();
            $table->unsignedBigInteger('prepared_by_id')->nullable();
            $table->string('verified_by_type', 40)->nullable();
            $table->unsignedBigInteger('verified_by_id')->nullable();
            $table->dateTime('submitted_at', 6)->index();
            $table->dateTime('verified_at', 6)->nullable();
            $table->timestamps();

            $table->index(
                ['logistics_account_id', 'origin_facility_id', 'status'],
                'branch_remit_origin_status_idx'
            );
            $table->index(
                ['logistics_account_id', 'destination_facility_id', 'status'],
                'branch_remit_destination_status_idx'
            );
        });

        Schema::create('branch_remittance_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_remittance_batch_id')
                ->constrained('branch_remittance_batches')
                ->cascadeOnDelete();
            $table->foreignId('cod_remittance_batch_id')
                ->unique()
                ->constrained('cod_remittance_batches')
                ->restrictOnDelete();
            $table->decimal('expected_amount', 14, 2);
            $table->dateTime('branch_received_at', 6)->nullable();
            $table->dateTime('central_released_at', 6)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_remittance_items');
        Schema::dropIfExists('branch_remittance_batches');

        Schema::table('cod_remittance_batches', function (Blueprint $table): void {
            $table->dropIndex('cod_remittance_batches_central_reconciled_at_index');
            $table->dropColumn('central_reconciled_at');
        });
    }
};
