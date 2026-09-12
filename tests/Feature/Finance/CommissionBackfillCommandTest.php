<?php

use App\Events\SellerOrderUpdated;
use App\Models\MarketplaceOrder;
use App\Models\OrderCommission;
use App\Models\SellerAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

test('commission backfill snapshots legacy delivered orders and is idempotent', function (): void {
    Event::fake([SellerOrderUpdated::class]);

    $seller = SellerAccount::query()->create([
        'email' => 'legacy-seller@example.test',
        'store_name' => 'Legacy Seller',
    ]);

    $order = MarketplaceOrder::query()->create([
        'order_number' => 'SARI-LEGACY-0001',
        'seller_account_id' => $seller->id,
        'buyer_name' => 'Legacy Buyer',
        'buyer_address' => 'Legacy Address',
        'payment_method' => 'COD',
        'payment_status' => 'paid',
        'subtotal' => 1500,
        'delivery_fee' => 80,
        'total' => 1580,
        'status' => 'delivered',
        'delivered_at' => now()->addSecond(),
    ]);

    $this->artisan('commissions:backfill', ['--force' => true])
        ->assertSuccessful();

    expect(OrderCommission::query()->where('marketplace_order_id', $order->id)->count())->toBe(1);

    $this->artisan('commissions:backfill', ['--force' => true])
        ->assertSuccessful();

    expect(OrderCommission::query()->where('marketplace_order_id', $order->id)->count())->toBe(1);
});
