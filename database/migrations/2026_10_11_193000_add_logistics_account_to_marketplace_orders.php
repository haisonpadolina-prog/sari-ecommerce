<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasTable('marketplace_orders')
            && !Schema::hasColumn('marketplace_orders', 'logistics_account_id')
        ) {
            Schema::table('marketplace_orders', function (Blueprint $table): void {
                $table->foreignId('logistics_account_id')
                    ->nullable()
                    ->constrained('logistics_accounts')
                    ->nullOnDelete();

                $table->index(
                    ['logistics_account_id', 'status'],
                    'marketplace_orders_logistics_status_idx'
                );
            });
        }

        if (
            Schema::hasTable('delivery_route_stops')
            && Schema::hasTable('logistics_facilities')
            && Schema::hasColumn('delivery_route_stops', 'logistics_facility_id')
            && Schema::hasColumn('marketplace_orders', 'logistics_account_id')
        ) {
            /*
             * Backfill only route snapshots whose origin/destination facilities
             * agree on one Logistics owner. Mixed legacy snapshots are left
             * unowned so DeliveryRouteService can rebuild them safely.
             */
            $routeOwners = DB::table('delivery_route_stops')
                ->join(
                    'logistics_facilities',
                    'logistics_facilities.id',
                    '=',
                    'delivery_route_stops.logistics_facility_id'
                )
                ->whereNotNull('logistics_facilities.logistics_account_id')
                ->whereIn(
                    'delivery_route_stops.stop_key',
                    ['origin_facility', 'destination_facility']
                )
                ->groupBy('delivery_route_stops.marketplace_order_id')
                ->havingRaw(
                    'COUNT(DISTINCT logistics_facilities.logistics_account_id) = 1'
                )
                ->selectRaw(
                    'delivery_route_stops.marketplace_order_id, MIN(logistics_facilities.logistics_account_id) AS logistics_account_id'
                )
                ->get();

            foreach ($routeOwners as $owner) {
                DB::table('marketplace_orders')
                    ->where('id', $owner->marketplace_order_id)
                    ->whereNull('logistics_account_id')
                    ->update([
                        'logistics_account_id' => $owner->logistics_account_id,
                    ]);
            }
        }

        if (
            Schema::hasTable('courier_accounts')
            && Schema::hasColumn('courier_accounts', 'logistics_account_id')
            && Schema::hasColumn('marketplace_orders', 'courier_email')
        ) {
            $orders = DB::table('marketplace_orders')
                ->whereNull('logistics_account_id')
                ->whereNotNull('courier_email')
                ->select(['id', 'courier_email'])
                ->get();

            foreach ($orders as $order) {
                $logisticsId = DB::table('courier_accounts')
                    ->whereRaw(
                        'LOWER(email) = ?',
                        [strtolower((string) $order->courier_email)]
                    )
                    ->value('logistics_account_id');

                if ($logisticsId) {
                    DB::table('marketplace_orders')
                        ->where('id', $order->id)
                        ->whereNull('logistics_account_id')
                        ->update([
                            'logistics_account_id' => $logisticsId,
                        ]);
                }
            }
        }
    }

    public function down(): void
    {
        if (
            Schema::hasTable('marketplace_orders')
            && Schema::hasColumn('marketplace_orders', 'logistics_account_id')
        ) {
            Schema::table('marketplace_orders', function (Blueprint $table): void {
                $table->dropIndex('marketplace_orders_logistics_status_idx');
                $table->dropConstrainedForeignId('logistics_account_id');
            });
        }
    }
};
