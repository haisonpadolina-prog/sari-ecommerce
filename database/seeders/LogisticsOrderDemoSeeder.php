<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class LogisticsOrderDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        /*
        |--------------------------------------------------------------------------
        | Logistics account
        |--------------------------------------------------------------------------
        */

        $logistics = DB::table('logistics_accounts')
            ->where('email', 'logistics@gmail.com')
            ->first();

        if (! $logistics) {
            $this->command->error(
                'Demo Logistics account not found. Run LogisticsDemoSeeder first.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Existing demo Seller
        |--------------------------------------------------------------------------
        */

        $seller = DB::table('seller_accounts')
            ->where('email', 'demo.seller@sari.test')
            ->first();

        if (! $seller) {
            $this->command->error(
                'Demo Seller not found. Run LogisticsDemoSeeder / previous demo setup first.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Hubs
        |--------------------------------------------------------------------------
        */

        $originHub = DB::table('logistics_facilities')
            ->where('logistics_account_id', $logistics->id)
            ->where('code', 'DEMO-SAN-PABLO-HUB')
            ->first();

        $destinationHub = DB::table('logistics_facilities')
            ->where('logistics_account_id', $logistics->id)
            ->where('code', 'DEMO-STA-CRUZ-HUB')
            ->first();

        if (! $originHub || ! $destinationHub) {
            $this->command->error(
                'Demo hubs not found. Run LogisticsDemoSeeder first.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Different Buyers + Different Products
        |--------------------------------------------------------------------------
        |
        | SAME Seller
        | SAME pickup address
        | SAME origin hub
        |
        | Therefore they should become ONE pickup group.
        |
        */

        $demoOrders = [
            [
                'order_number' => 'DEMO-LOG-0001',
                'buyer_name' => 'Ana Santos',
                'buyer_phone' => '09170000001',
                'buyer_email' => 'ana@sari.test',
                'buyer_address' => 'Barangay Bubukal, Santa Cruz, Laguna',
                'product' => 'Wireless Headphones',
                'price' => 1299,
            ],

            [
                'order_number' => 'DEMO-LOG-0002',
                'buyer_name' => 'Mark Reyes',
                'buyer_phone' => '09170000002',
                'buyer_email' => 'mark@sari.test',
                'buyer_address' => 'Barangay Pagsawitan, Santa Cruz, Laguna',
                'product' => 'Running Shoes',
                'price' => 1899,
            ],

            [
                'order_number' => 'DEMO-LOG-0003',
                'buyer_name' => 'Carlo Mendoza',
                'buyer_phone' => '09170000003',
                'buyer_email' => 'carlo@sari.test',
                'buyer_address' => 'Barangay Gatid, Santa Cruz, Laguna',
                'product' => 'Laptop Backpack',
                'price' => 850,
            ],

            [
                'order_number' => 'DEMO-LOG-0004',
                'buyer_name' => 'Bea Garcia',
                'buyer_phone' => '09170000004',
                'buyer_email' => 'bea@sari.test',
                'buyer_address' => 'Barangay Patimbao, Santa Cruz, Laguna',
                'product' => 'Smart Watch',
                'price' => 2199,
            ],

            [
                'order_number' => 'DEMO-LOG-0005',
                'buyer_name' => 'Joshua Cruz',
                'buyer_phone' => '09170000005',
                'buyer_email' => 'joshua@sari.test',
                'buyer_address' => 'Barangay Santo Angel Central, Santa Cruz, Laguna',
                'product' => 'Mechanical Keyboard',
                'price' => 2499,
            ],
        ];

        $demoOrderNumbers = collect($demoOrders)
            ->pluck('order_number')
            ->values();

        DB::transaction(function () use (
            $demoOrders,
            $demoOrderNumbers,
            $logistics,
            $seller,
            $originHub,
            $destinationHub,
            $now
        ): void {
            /*
            |--------------------------------------------------------------------------
            | Safe reset of previous demo workflow state
            |--------------------------------------------------------------------------
            |
            | This cleanup is intentionally limited to DEMO-LOG-0001..0005.
            | Real Marketplace orders are not touched.
            |
            */

            $existingOrderIds = DB::table('marketplace_orders')
                ->whereIn(
                    'order_number',
                    $demoOrderNumbers->all()
                )
                ->pluck('id')
                ->map(
                    fn ($id): int =>
                        (int) $id
                )
                ->values();

            if ($existingOrderIds->isNotEmpty()) {
                /*
                |--------------------------------------------------------------------------
                | Refuse to erase externally-paid demo financial records
                |--------------------------------------------------------------------------
                */

                if (Schema::hasTable('seller_settlements')) {
                    $externallySettled =
                        DB::table('seller_settlements')
                            ->whereIn(
                                'marketplace_order_id',
                                $existingOrderIds->all()
                            )
                            ->where(
                                function ($query): void {
                                    $query
                                        ->whereNotNull(
                                            'paid_at'
                                        )
                                        ->orWhereNotNull(
                                            'external_settlement_reference'
                                        );
                                }
                            )
                            ->exists();

                    if ($externallySettled) {
                        throw new RuntimeException(
                            'Demo reset stopped because a demo Seller settlement was already externally paid.'
                        );
                    }
                }

                if (Schema::hasTable('rider_earnings')) {
                    $earningIds =
                        DB::table('rider_earnings')
                            ->whereIn(
                                'marketplace_order_id',
                                $existingOrderIds->all()
                            )
                            ->pluck('id')
                            ->map(
                                fn ($id): int =>
                                    (int) $id
                            )
                            ->values();

                    $paidEarningExists =
                        DB::table('rider_earnings')
                            ->whereIn(
                                'marketplace_order_id',
                                $existingOrderIds->all()
                            )
                            ->whereNotNull(
                                'paid_at'
                            )
                            ->exists();

                    if ($paidEarningExists) {
                        throw new RuntimeException(
                            'Demo reset stopped because a demo Rider earning was already paid.'
                        );
                    }

                    if (
                        $earningIds->isNotEmpty()
                        &&
                        Schema::hasTable(
                            'rider_payout_request_items'
                        )
                        &&
                        DB::table(
                            'rider_payout_request_items'
                        )
                            ->whereIn(
                                'rider_earning_id',
                                $earningIds->all()
                            )
                            ->exists()
                    ) {
                        throw new RuntimeException(
                            'Demo reset stopped because a demo Rider earning is already attached to a payout request.'
                        );
                    }
                }

                if (Schema::hasTable('payment_transactions')) {
                    $externalPaymentExists =
                        DB::table(
                            'payment_transactions'
                        )
                            ->whereIn(
                                'marketplace_order_id',
                                $existingOrderIds->all()
                            )
                            ->where(
                                'provider',
                                '!=',
                                'internal'
                            )
                            ->exists();

                    if ($externalPaymentExists) {
                        throw new RuntimeException(
                            'Demo reset stopped because a demo payment transaction was recorded by an external provider.'
                        );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Last-mile links / empty assignments
                |--------------------------------------------------------------------------
                */

                if (
                    Schema::hasTable(
                        'last_mile_assignment_orders'
                    )
                    &&
                    Schema::hasTable(
                        'last_mile_assignments'
                    )
                ) {
                    $lastMileAssignmentIds =
                        DB::table(
                            'last_mile_assignment_orders'
                        )
                            ->whereIn(
                                'marketplace_order_id',
                                $existingOrderIds->all()
                            )
                            ->pluck(
                                'last_mile_assignment_id'
                            )
                            ->map(
                                fn ($id): int =>
                                    (int) $id
                            )
                            ->unique()
                            ->values();

                    DB::table(
                        'last_mile_assignment_orders'
                    )
                        ->whereIn(
                            'marketplace_order_id',
                            $existingOrderIds->all()
                        )
                        ->delete();

                    foreach (
                        $lastMileAssignmentIds
                        as $assignmentId
                    ) {
                        $hasRemainingOrders =
                            DB::table(
                                'last_mile_assignment_orders'
                            )
                                ->where(
                                    'last_mile_assignment_id',
                                    $assignmentId
                                )
                                ->exists();

                        if (! $hasRemainingOrders) {
                            DB::table(
                                'last_mile_assignments'
                            )
                                ->where(
                                    'id',
                                    $assignmentId
                                )
                                ->delete();
                        }
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Hub-transfer links / empty manifests
                |--------------------------------------------------------------------------
                */

                $parcelIds = collect();

                if (Schema::hasTable('logistics_parcels')) {
                    $parcelIds =
                        DB::table(
                            'logistics_parcels'
                        )
                            ->whereIn(
                                'marketplace_order_id',
                                $existingOrderIds->all()
                            )
                            ->pluck('id')
                            ->map(
                                fn ($id): int =>
                                    (int) $id
                            )
                            ->values();
                }

                if (
                    $parcelIds->isNotEmpty()
                    &&
                    Schema::hasTable(
                        'hub_transfer_parcels'
                    )
                    &&
                    Schema::hasTable(
                        'hub_transfers'
                    )
                ) {
                    $transferIds =
                        DB::table(
                            'hub_transfer_parcels'
                        )
                            ->whereIn(
                                'logistics_parcel_id',
                                $parcelIds->all()
                            )
                            ->pluck(
                                'hub_transfer_id'
                            )
                            ->map(
                                fn ($id): int =>
                                    (int) $id
                            )
                            ->unique()
                            ->values();

                    DB::table(
                        'hub_transfer_parcels'
                    )
                        ->whereIn(
                            'logistics_parcel_id',
                            $parcelIds->all()
                        )
                        ->delete();

                    foreach (
                        $transferIds
                        as $transferId
                    ) {
                        $hasRemainingParcels =
                            DB::table(
                                'hub_transfer_parcels'
                            )
                                ->where(
                                    'hub_transfer_id',
                                    $transferId
                                )
                                ->exists();

                        if (! $hasRemainingParcels) {
                            DB::table(
                                'hub_transfers'
                            )
                                ->where(
                                    'id',
                                    $transferId
                                )
                                ->delete();
                        }
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Finance rows from previous demo completion
                |--------------------------------------------------------------------------
                */

                if (Schema::hasTable('seller_settlements')) {
                    DB::table(
                        'seller_settlements'
                    )
                        ->whereIn(
                            'marketplace_order_id',
                            $existingOrderIds->all()
                        )
                        ->delete();
                }

                if (Schema::hasTable('payment_transactions')) {
                    DB::table(
                        'payment_transactions'
                    )
                        ->whereIn(
                            'marketplace_order_id',
                            $existingOrderIds->all()
                        )
                        ->delete();
                }

                if (Schema::hasTable('rider_earnings')) {
                    DB::table(
                        'rider_earnings'
                    )
                        ->whereIn(
                            'marketplace_order_id',
                            $existingOrderIds->all()
                        )
                        ->delete();
                }

                if (Schema::hasTable('order_commissions')) {
                    $commissionIds =
                        DB::table(
                            'order_commissions'
                        )
                            ->whereIn(
                                'marketplace_order_id',
                                $existingOrderIds->all()
                            )
                            ->pluck('id')
                            ->map(
                                fn ($id): int =>
                                    (int) $id
                            )
                            ->values();

                    if (
                        $commissionIds->isNotEmpty()
                        &&
                        Schema::hasTable(
                            'commission_audit_logs'
                        )
                    ) {
                        DB::table(
                            'commission_audit_logs'
                        )
                            ->whereIn(
                                'order_commission_id',
                                $commissionIds->all()
                            )
                            ->delete();
                    }

                    DB::table(
                        'order_commissions'
                    )
                        ->whereIn(
                            'marketplace_order_id',
                            $existingOrderIds->all()
                        )
                        ->delete();
                }

                /*
                |--------------------------------------------------------------------------
                | Old workflow events
                |--------------------------------------------------------------------------
                |
                | buyer_notification_reads cascade automatically from these.
                |
                */

                if (Schema::hasTable('marketplace_order_events')) {
                    DB::table(
                        'marketplace_order_events'
                    )
                        ->whereIn(
                            'marketplace_order_id',
                            $existingOrderIds->all()
                        )
                        ->delete();
                }
            }

            foreach ($demoOrders as $demo) {
                $subtotal = $demo['price'];
                $deliveryFee = 50;
                $total =
                    $subtotal
                    + $deliveryFee;

                /*
                |--------------------------------------------------------------------------
                | Marketplace Order
                |--------------------------------------------------------------------------
                */

                $orderValues = [
                    'seller_account_id' =>
                        $seller->id,

                    'logistics_account_id' =>
                        $logistics->id,

                    'buyer_name' =>
                        $demo['buyer_name'],

                    'buyer_phone' =>
                        $demo['buyer_phone'],

                    'buyer_email' =>
                        $demo['buyer_email'],

                    'buyer_address' =>
                        $demo['buyer_address'],

                    'payment_method' =>
                        'COD',

                    'payment_status' =>
                        'pending',

                    'items' =>
                        json_encode([
                            [
                                'name' =>
                                    $demo['product'],

                                'quantity' =>
                                    1,

                                'price' =>
                                    $demo['price'],
                            ],
                        ]),

                    'subtotal' =>
                        $subtotal,

                    'delivery_fee' =>
                        $deliveryFee,

                    'total' =>
                        $total,

                    /*
                    |--------------------------------------------------------------------------
                    | Same pickup details for all orders
                    |--------------------------------------------------------------------------
                    */

                    'pickup_name' =>
                        'Demo San Pablo Store',

                    'pickup_address' =>
                        'San Pablo City, Laguna',

                    /*
                    |--------------------------------------------------------------------------
                    | Ready for Logistics pickup assignment
                    |--------------------------------------------------------------------------
                    */

                    'status' =>
                        'ready_for_pickup',

                    'courier_name' =>
                        null,

                    'courier_email' =>
                        null,

                    'accepted_at' =>
                        null,

                    'ready_at' =>
                        $now,

                    'pickup_started_at' =>
                        null,

                    'arrived_pickup_at' =>
                        null,

                    'picked_up_at' =>
                        null,

                    'arrived_buyer_at' =>
                        null,

                    'delivered_at' =>
                        null,

                    'updated_at' =>
                        $now,

                    'created_at' =>
                        $now,
                ];

                /*
                |--------------------------------------------------------------------------
                | Optional Marketplace fields introduced by later migrations
                |--------------------------------------------------------------------------
                */

                if (
                    Schema::hasColumn(
                        'marketplace_orders',
                        'cancelled_at'
                    )
                ) {
                    $orderValues[
                        'cancelled_at'
                    ] = null;
                }

                if (
                    Schema::hasColumn(
                        'marketplace_orders',
                        'cancellation_reason'
                    )
                ) {
                    $orderValues[
                        'cancellation_reason'
                    ] = null;
                }

                if (
                    Schema::hasColumn(
                        'marketplace_orders',
                        'original_subtotal'
                    )
                ) {
                    $orderValues[
                        'original_subtotal'
                    ] = $subtotal;
                }

                if (
                    Schema::hasColumn(
                        'marketplace_orders',
                        'discount_amount'
                    )
                ) {
                    $orderValues[
                        'discount_amount'
                    ] = 0;
                }

                if (
                    Schema::hasColumn(
                        'marketplace_orders',
                        'voucher_code'
                    )
                ) {
                    $orderValues[
                        'voucher_code'
                    ] = null;
                }

                if (
                    Schema::hasColumn(
                        'marketplace_orders',
                        'seller_voucher_id'
                    )
                ) {
                    $orderValues[
                        'seller_voucher_id'
                    ] = null;
                }

                if (
                    Schema::hasColumn(
                        'marketplace_orders',
                        'original_delivery_fee'
                    )
                ) {
                    $orderValues[
                        'original_delivery_fee'
                    ] = $deliveryFee;
                }

                if (
                    Schema::hasColumn(
                        'marketplace_orders',
                        'shipping_discount_amount'
                    )
                ) {
                    $orderValues[
                        'shipping_discount_amount'
                    ] = 0;
                }

                if (
                    Schema::hasColumn(
                        'marketplace_orders',
                        'seller_free_shipping_promotion_id'
                    )
                ) {
                    $orderValues[
                        'seller_free_shipping_promotion_id'
                    ] = null;
                }

                if (
                    Schema::hasColumn(
                        'marketplace_orders',
                        'shipping_discount_funder'
                    )
                ) {
                    $orderValues[
                        'shipping_discount_funder'
                    ] = null;
                }

                DB::table(
                    'marketplace_orders'
                )
                    ->updateOrInsert(
                        [
                            'order_number' =>
                                $demo['order_number'],
                        ],
                        $orderValues
                    );

                $order =
                    DB::table(
                        'marketplace_orders'
                    )
                        ->where(
                            'order_number',
                            $demo['order_number']
                        )
                        ->first();

                /*
                |--------------------------------------------------------------------------
                | Verified Logistics Parcel
                |--------------------------------------------------------------------------
                */

                DB::table(
                    'logistics_parcels'
                )
                    ->updateOrInsert(
                        [
                            'marketplace_order_id' =>
                                $order->id,
                        ],
                        [
                            'status' =>
                                'pickup_verified',

                            'sorting_zone' =>
                                null,

                            'notes' =>
                                'Demo verified pickup request.',

                            'received_at' =>
                                null,

                            'sorted_at' =>
                                null,

                            'updated_at' =>
                                $now,

                            'created_at' =>
                                $now,
                        ]
                    );

                /*
                |--------------------------------------------------------------------------
                | Reset route stops
                |--------------------------------------------------------------------------
                */

                DB::table(
                    'delivery_route_stops'
                )
                    ->where(
                        'marketplace_order_id',
                        $order->id
                    )
                    ->delete();

                /*
                |--------------------------------------------------------------------------
                | Route
                |--------------------------------------------------------------------------
                |
                | Seller
                |   ↓
                | San Pablo Hub
                |   ↓
                | Sta. Cruz Hub
                |   ↓
                | Buyer
                |
                */

                DB::table(
                    'delivery_route_stops'
                )
                    ->insert([
                        [
                            'marketplace_order_id' =>
                                $order->id,

                            'logistics_facility_id' =>
                                null,

                            'sequence' =>
                                1,

                            'stop_key' =>
                                'seller',

                            'type' =>
                                'seller',

                            'name' =>
                                'Demo San Pablo Store',

                            'address_snapshot' =>
                                'San Pablo City, Laguna',

                            'latitude' =>
                                14.0683,

                            'longitude' =>
                                121.3256,

                            'reached_at' =>
                                null,

                            'created_at' =>
                                $now,

                            'updated_at' =>
                                $now,
                        ],

                        [
                            'marketplace_order_id' =>
                                $order->id,

                            'logistics_facility_id' =>
                                $originHub->id,

                            'sequence' =>
                                2,

                            'stop_key' =>
                                'origin_facility',

                            'type' =>
                                'sorting_facility',

                            'name' =>
                                $originHub->name,

                            'address_snapshot' =>
                                $originHub->address,

                            'latitude' =>
                                $originHub->latitude,

                            'longitude' =>
                                $originHub->longitude,

                            'reached_at' =>
                                null,

                            'created_at' =>
                                $now,

                            'updated_at' =>
                                $now,
                        ],

                        [
                            'marketplace_order_id' =>
                                $order->id,

                            'logistics_facility_id' =>
                                $destinationHub->id,

                            'sequence' =>
                                3,

                            'stop_key' =>
                                'destination_facility',

                            'type' =>
                                'sorting_facility',

                            'name' =>
                                $destinationHub->name,

                            'address_snapshot' =>
                                $destinationHub->address,

                            'latitude' =>
                                $destinationHub->latitude,

                            'longitude' =>
                                $destinationHub->longitude,

                            'reached_at' =>
                                null,

                            'created_at' =>
                                $now,

                            'updated_at' =>
                                $now,
                        ],

                        [
                            'marketplace_order_id' =>
                                $order->id,

                            'logistics_facility_id' =>
                                null,

                            'sequence' =>
                                4,

                            'stop_key' =>
                                'buyer',

                            'type' =>
                                'buyer',

                            'name' =>
                                $demo['buyer_name'],

                            'address_snapshot' =>
                                $demo['buyer_address'],

                            /*
                            | Demo coordinates only.
                            */

                            'latitude' =>
                                14.2814,

                            'longitude' =>
                                121.4161,

                            'reached_at' =>
                                null,

                            'created_at' =>
                                $now,

                            'updated_at' =>
                                $now,
                        ],
                    ]);
            }
        });

        $this->command->info(
            '5 demo Logistics orders were safely reset to verified pickup-ready state.'
        );
    }
}