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
            $table->foreignId('logistics_facility_id')
                ->nullable()
                ->after('logistics_account_id')
                ->constrained('logistics_facilities')
                ->nullOnDelete();

            $table->string('hub_name_snapshot', 191)
                ->nullable()
                ->after('batch_code');

            $table->text('hub_address_snapshot')
                ->nullable()
                ->after('hub_name_snapshot');
        });

        // New COD remittances are physical-cash-only. Any still-open legacy
        // test batch is normalized to the new workflow. Verified history is
        // intentionally preserved as an immutable audit record.
        DB::table('cod_remittance_batches')
            ->whereIn('status', ['submitted', 'discrepancy'])
            ->where('remittance_method', '!=', 'cash_hub')
            ->update([
                'remittance_method' => 'cash_hub',
                'rider_reference' => null,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        Schema::table('cod_remittance_batches', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('logistics_facility_id');
            $table->dropColumn(['hub_name_snapshot', 'hub_address_snapshot']);
        });
    }
};
