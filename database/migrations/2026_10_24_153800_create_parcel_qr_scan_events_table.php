<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parcel_qr_scan_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('marketplace_order_id')
                ->constrained('marketplace_orders')
                ->cascadeOnDelete();

            $table->string('scan_key', 191)->unique();
            $table->string('checkpoint', 80)->index();
            $table->string('actor_role', 30)->index();
            $table->unsignedBigInteger('actor_id')->nullable()->index();
            $table->unsignedBigInteger('logistics_facility_id')->nullable()->index();
            $table->string('context_type', 60)->nullable()->index();
            $table->unsignedBigInteger('context_id')->nullable()->index();
            $table->timestamp('scanned_at')->index();
            $table->timestamps();

            $table->index([
                'context_type',
                'context_id',
                'checkpoint',
            ], 'parcel_qr_context_checkpoint_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcel_qr_scan_events');
    }
};
