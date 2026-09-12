<?php

use App\Events\SellerOrderUpdated;
use App\Models\PaymentTransaction;
use App\Models\RiderEarning;
use App\Models\SellerSettlement;
use App\Services\FinancialFlowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Event::fake([SellerOrderUpdated::class]);
});

test('financial flow separates merchandise commission seller payable and rider delivery earning', function (): void {
    $seller = sariFinanceSeller();
    $courier = sariFinanceCourier();
    $order = sariFinanceOrder(
        $seller,
        subtotal: 1000.00,
        deliveredAt: now(),
        courier: $courier
    );

    $result = app(FinancialFlowService::class)->recordCompletedOrder($order);

    expect((float) $result['commission']->eligible_amount)->toBe(1000.0)
        ->and((float) $result['commission']->net_commission)->toBe(100.0)
        ->and((float) $result['payment']->amount)->toBe(1080.0)
        ->and((float) $result['seller_settlement']->merchandise_amount)->toBe(1000.0)
        ->and((float) $result['seller_settlement']->platform_commission_amount)->toBe(100.0)
        ->and((float) $result['seller_settlement']->withholding_tax_amount)->toBe(0.0)
        ->and($result['seller_settlement']->withholding_status)->toBe('not_evaluated')
        ->and((float) $result['seller_settlement']->seller_net_amount)->toBe(900.0)
        ->and((float) $result['rider_earning']->delivery_fee_amount)->toBe(80.0);

    expect(PaymentTransaction::query()->where('marketplace_order_id', $order->id)->count())->toBe(1)
        ->and(SellerSettlement::query()->where('marketplace_order_id', $order->id)->count())->toBe(1)
        ->and(RiderEarning::query()->where('marketplace_order_id', $order->id)->count())->toBe(1);
});

test('financial flow is idempotent for the same completed order', function (): void {
    $seller = sariFinanceSeller();
    $courier = sariFinanceCourier();
    $order = sariFinanceOrder($seller, subtotal: 700.00, deliveredAt: now(), courier: $courier);
    $service = app(FinancialFlowService::class);

    $service->recordCompletedOrder($order);
    $service->recordCompletedOrder($order->fresh());

    expect(PaymentTransaction::query()->where('marketplace_order_id', $order->id)->count())->toBe(1)
        ->and(SellerSettlement::query()->where('marketplace_order_id', $order->id)->count())->toBe(1)
        ->and(RiderEarning::query()->where('marketplace_order_id', $order->id)->count())->toBe(1);
});
