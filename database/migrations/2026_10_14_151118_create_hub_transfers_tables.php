<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hub_transfers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('logistics_account_id')
                ->constrained('logistics_accounts')
                ->cascadeOnDelete();

            $table->foreignId('origin_facility_id')
                ->constrained('logistics_facilities')
                ->restrictOnDelete();

            $table->foreignId('destination_facility_id')
                ->constrained('logistics_facilities')
                ->restrictOnDelete();

            $table->string('transfer_code')->unique();

            $table->string('vehicle_type');
            $table->string('vehicle_plate')->nullable();
            $table->string('driver_name')->nullable();

            $table->string('status')->default('preparing');

            $table->text('notes')->nullable();

            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('received_at')->nullable();

            $table->timestamps();
        });

        Schema::create('hub_transfer_parcels', function (Blueprint $table) {
            $table->id();

            $table->foreignId('hub_transfer_id')
                ->constrained('hub_transfers')
                ->cascadeOnDelete();

            $table->foreignId('logistics_parcel_id')
                ->constrained('logistics_parcels')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique('logistics_parcel_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hub_transfer_parcels');
        Schema::dropIfExists('hub_transfers');
    }
};