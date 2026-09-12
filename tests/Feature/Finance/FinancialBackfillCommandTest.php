<?php

use App\Events\SellerOrderUpdated;
use App\Models\PaymentTransaction;
use App\Models\RiderEarning;
use App\Models\SellerSettlement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

test('finance backfill creates ledgers only for delivered and paid orders and is idempotent', function (): void {
    Event::fake([SellerOrderUpdated::class]);

    $seller = sariFinanceSeller();
    $courier = sariFinanceCourier();
    $eligible = sariFinanceOrder($seller, subtotal: 1500.00, deliveredAt: now(), courier: $courier);
    $unpaid = sariFinanceOrder(
        $seller,
        subtotal: 500.00,
        deliveredAt: now(),
        courier: $courier,
        paymentStatus: 'pending'
    );

    $this->artisan('finance:backfill', ['--force' => true])->assertSuccessful();
    $this->artisan('finance:backfill', ['--force' => true])->assertSuccessful();

    expect(PaymentTransaction::query()->where('marketplace_order_id', $eligible->id)->count())->toBe(1)
        ->and(SellerSettlement::query()->where('marketplace_order_id', $eligible->id)->count())->toBe(1)
        ->and(RiderEarning::query()->where('marketplace_order_id', $eligible->id)->count())->toBe(1)
        ->and(PaymentTransaction::query()->where('marketplace_order_id', $unpaid->id)->count())->toBe(0);
});
