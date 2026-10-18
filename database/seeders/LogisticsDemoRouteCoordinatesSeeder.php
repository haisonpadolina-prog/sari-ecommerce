<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LogisticsDemoRouteCoordinatesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        /*
        |--------------------------------------------------------------------------
        | Demo Seller Pickup Point
        |--------------------------------------------------------------------------
        |
        | Use a realistic demo point in San Francisco,
        | San Pablo City instead of placing the Seller
        | directly on top of the San Pablo Hub.
        |
        */

        $sellerLocation = [
            'address' => 'Barangay San Francisco, San Pablo City, Laguna',
            'latitude' => 14.0581,
            'longitude' => 121.3294,
        ];

        /*
        |--------------------------------------------------------------------------
        | Demo Buyer Delivery Points
        |--------------------------------------------------------------------------
        |
        | These are barangay-level demo coordinates.
        |
        | They are intentionally different from the
        | Sta. Cruz Sorting Hub coordinates.
        |
        */

        $buyerLocations = [

            'DEMO-LOG-0001' => [
                'name' => 'Ana Santos',
                'address' => 'Barangay Bubukal, Santa Cruz, Laguna',
                'latitude' => 14.2593,
                'longitude' => 121.3985,
            ],

            'DEMO-LOG-0002' => [
                'name' => 'Mark Reyes',
                'address' => 'Barangay Pagsawitan, Santa Cruz, Laguna',
                'latitude' => 14.2658,
                'longitude' => 121.4265,
            ],

            'DEMO-LOG-0003' => [
                'name' => 'Carlo Mendoza',
                'address' => 'Barangay Gatid, Santa Cruz, Laguna',
                'latitude' => 14.2606,
                'longitude' => 121.3852,
            ],

            'DEMO-LOG-0004' => [
                'name' => 'Bea Garcia',
                'address' => 'Barangay Patimbao, Santa Cruz, Laguna',
                'latitude' => 14.2607,
                'longitude' => 121.4089,
            ],

            'DEMO-LOG-0005' => [
                'name' => 'Joshua Cruz',
                'address' => 'Barangay Santo Angel Central, Santa Cruz, Laguna',
                'latitude' => 14.2854,
                'longitude' => 121.4087,
            ],

        ];

        /*
        |--------------------------------------------------------------------------
        | Update Demo Route Endpoints Only
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | This does NOT reset:
        |
        | - Marketplace order status
        | - Logistics parcel status
        | - Pickup workflow
        | - Hub Transfers
        | - Last-Mile Assignments
        | - Rider delivery state
        | - Rider earnings
        | - Payout requests
        |
        | Only the Seller / Buyer route coordinates and
        | demo address snapshots are corrected.
        |
        */

        DB::transaction(
            function () use (
                $sellerLocation,
                $buyerLocations,
                $now
            ): void {

                foreach (
                    $buyerLocations
                    as $orderNumber => $buyerLocation
                ) {

                    $order = DB::table('marketplace_orders')
                        ->where(
                            'order_number',
                            $orderNumber
                        )
                        ->first();

                    /*
                     * Skip missing demo orders safely.
                     */

                    if (! $order) {
                        $this->command?->warn(
                            $orderNumber
                            . ' was not found. Skipped.'
                        );

                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Marketplace Order Addresses
                    |--------------------------------------------------------------------------
                    |
                    | Make the Seller pickup address more specific so a later
                    | "Refresh nearest facilities" can geocode the Seller
                    | somewhere inside San Pablo instead of using the same
                    | generic city point as the Hub.
                    |
                    */

                    DB::table('marketplace_orders')
                        ->where('id', $order->id)
                        ->update([
                            'pickup_address' =>
                                $sellerLocation['address'],

                            'buyer_address' =>
                                $buyerLocation['address'],

                            'updated_at' =>
                                $now,
                        ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Seller Route Stop
                    |--------------------------------------------------------------------------
                    */

                    DB::table('delivery_route_stops')
                        ->where(
                            'marketplace_order_id',
                            $order->id
                        )
                        ->where(
                            'stop_key',
                            'seller'
                        )
                        ->update([
                            'name' =>
                                'Demo San Pablo Store',

                            'address_snapshot' =>
                                $sellerLocation['address'],

                            'latitude' =>
                                $sellerLocation['latitude'],

                            'longitude' =>
                                $sellerLocation['longitude'],

                            'updated_at' =>
                                $now,
                        ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Buyer Route Stop
                    |--------------------------------------------------------------------------
                    */

                    DB::table('delivery_route_stops')
                        ->where(
                            'marketplace_order_id',
                            $order->id
                        )
                        ->where(
                            'stop_key',
                            'buyer'
                        )
                        ->update([
                            'name' =>
                                $buyerLocation['name'],

                            'address_snapshot' =>
                                $buyerLocation['address'],

                            'latitude' =>
                                $buyerLocation['latitude'],

                            'longitude' =>
                                $buyerLocation['longitude'],

                            'updated_at' =>
                                $now,
                        ]);
                }
            },
            3
        );

        $this->command?->info(
            'SARI demo Seller and Buyer route coordinates updated successfully.'
        );
    }
}