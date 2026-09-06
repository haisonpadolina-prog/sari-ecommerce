<?php

namespace Database\Seeders;

use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderEvent;
use App\Models\SellerAccount;
use Illuminate\Database\Seeder;

class MarketplaceOrderDemoSeeder extends Seeder
{
    public function run(): void
    {
        $seller = SellerAccount::query()->first();

        if (!$seller) {
            $seller = SellerAccount::create([
                'email' => 'seller@gmail.com',
                'store_name' => 'SARI Seller Store',
            ]);
        }

        $rows = [
            [
                'order_number' => 'SARI-DEMO-1001',
                'buyer_name' => 'Angela Ramos',
                'buyer_phone' => '0917 555 1101',
                'buyer_address' => 'Pasay City, Metro Manila',
                'payment_method' => 'COD',
                'payment_status' => 'pending',
                'items' => [
                    ['name' => 'Wireless Earbuds Pro', 'qty' => 1, 'price' => 1299],
                    ['name' => 'Canvas Tote Bag', 'qty' => 1, 'price' => 399],
                ],
                'subtotal' => 1698,
                'delivery_fee' => 120,
                'total' => 1818,
            ],
            [
                'order_number' => 'SARI-DEMO-1002',
                'buyer_name' => 'Mika Santos',
                'buyer_phone' => '0917 555 1102',
                'buyer_address' => 'Taguig City, Metro Manila',
                'payment_method' => 'Paid Online',
                'payment_status' => 'paid',
                'items' => [
                    ['name' => 'Linen Storage Basket', 'qty' => 2, 'price' => 450],
                ],
                'subtotal' => 900,
                'delivery_fee' => 95,
                'total' => 995,
            ],
        ];

        foreach ($rows as $data) {
            $order = MarketplaceOrder::updateOrCreate(
                ['order_number' => $data['order_number']],
                array_merge($data, [
                    'seller_account_id' => $seller->id,
                    'pickup_name' => $seller->store_name ?: 'SARI Seller Store',
                    'pickup_address' => 'Makati City, Metro Manila',
                    'status' => 'new',
                    'courier_name' => null,
                    'courier_email' => null,
                    'accepted_at' => null,
                    'ready_at' => null,
                    'pickup_started_at' => null,
                    'arrived_pickup_at' => null,
                    'picked_up_at' => null,
                    'arrived_buyer_at' => null,
                    'delivered_at' => null,
                ])
            );

            if (!$order->events()->exists()) {
                MarketplaceOrderEvent::create([
                    'marketplace_order_id' => $order->id,
                    'seller_account_id' => $seller->id,
                    'audience' => 'seller',
                    'type' => 'new',
                    'title' => 'New Order Received',
                    'message' => 'Demo order ' . $order->order_number . ' is ready for seller preparation.',
                    'status' => 'new',
                ]);
            }
        }
    }
}
