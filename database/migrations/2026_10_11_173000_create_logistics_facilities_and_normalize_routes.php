<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('logistics_facilities')) {
            Schema::create('logistics_facilities', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('logistics_account_id')
                    ->nullable()
                    ->constrained('logistics_accounts')
                    ->nullOnDelete();

                $table->string('code', 60)->unique();
                $table->string('name', 140);
                $table->text('address');
                $table->string('province_name', 120)->nullable();
                $table->string('municipality_name', 120)->nullable();
                $table->string('barangay_name', 120)->nullable();

                $table->decimal('latitude', 10, 7);
                $table->decimal('longitude', 10, 7);

                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['is_active', 'province_name']);
                $table->index(['latitude', 'longitude']);
            });
        }

        if (
            Schema::hasTable('delivery_route_stops')
            && !Schema::hasColumn('delivery_route_stops', 'logistics_facility_id')
        ) {
            Schema::table('delivery_route_stops', function (Blueprint $table): void {
                $table->foreignId('logistics_facility_id')
                    ->nullable()
                    ->constrained('logistics_facilities')
                    ->nullOnDelete();
            });
        }

        /*
         * Remove the old fixed hub_1 / hub_2 / hub_3 route structure.
         * Seller remains sequence 1; Buyer moves to sequence 4.
         * DeliveryRouteService will create the two new nearest-facility stops.
         */
        if (Schema::hasTable('delivery_route_stops')) {
            DB::table('delivery_route_stops')
                ->where(function ($query): void {
                    $query->where('type', 'sorting_facility')
                        ->orWhereIn('stop_key', ['hub_1', 'hub_2', 'hub_3']);
                })
                ->delete();

            DB::table('delivery_route_stops')
                ->where('stop_key', 'buyer')
                ->update(['sequence' => 4]);
        }
    }

    public function down(): void
    {
        if (
            Schema::hasTable('delivery_route_stops')
            && Schema::hasColumn('delivery_route_stops', 'logistics_facility_id')
        ) {
            Schema::table('delivery_route_stops', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('logistics_facility_id');
            });
        }

        Schema::dropIfExists('logistics_facilities');
    }
};
