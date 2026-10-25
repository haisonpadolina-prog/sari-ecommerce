<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Reconcile partially-existing local schemas
        |--------------------------------------------------------------------------
        |
        | Some development databases already contain these tables even though this
        | migration is not recorded as executed. Guard each create independently so
        | Laravel can safely reconcile the migration history without dropping or
        | replacing existing delivery data.
        |
        */
        if (! Schema::hasTable('last_mile_assignments')) {
            Schema::create('last_mile_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('logistics_account_id')
                ->constrained('logistics_accounts')
                ->cascadeOnDelete();

            $table->foreignId('destination_facility_id')
                ->constrained('logistics_facilities')
                ->restrictOnDelete();

            $table->foreignId('courier_account_id')
                ->constrained('courier_accounts')
                ->restrictOnDelete();

            $table->string('assignment_code')->unique();

            // Barangay/service-area snapshot.
            $table->string('delivery_area', 150);

            $table->string('status', 30)
                ->default('assigned')
                ->index();

            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

                $table->index([
                    'logistics_account_id',
                    'destination_facility_id',
                    'status',
                ]);
            });
        }

        if (! Schema::hasTable('last_mile_assignment_orders')) {
            Schema::create('last_mile_assignment_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('last_mile_assignment_id')
                ->constrained('last_mile_assignments')
                ->cascadeOnDelete();

            $table->foreignId('marketplace_order_id')
                ->constrained('marketplace_orders')
                ->cascadeOnDelete();

            $table->timestamps();

                // V1: one last-mile assignment per order.
                $table->unique('marketplace_order_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('last_mile_assignment_orders');
        Schema::dropIfExists('last_mile_assignments');
    }
};