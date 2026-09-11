<?php

use App\Events\SellerOrderUpdated;
use App\Models\CommissionAuditLog;
use App\Models\MarketplaceOrder;
use App\Models\OrderCommission;
use App\Models\PlatformSetting;
use App\Models\SellerAccount;
use App\Models\SellerSettlement;
use App\Services\CommissionService;
use App\Services\MarketplaceOrderWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Event::fake([SellerOrderUpdated::class]);
});

test('a delivered and paid order receives one immutable commission snapshot', function (): void {
    $seller = sariFinanceSeller();
    $order = sariFinanceOrder($seller, subtotal: 990.00, deliveredAt: now()->addSecond());

    $service = app(CommissionService::class);

    $first = $service->recordForDeliveredOrder($order);
    $second = $service->recordForDeliveredOrder($order->fresh());

    expect($first->id)->toBe($second->id)
        ->and((float) $first->eligible_amount)->toBe(990.0)
        ->and((float) $first->rate_percent)->toBe(10.0)
        ->and((float) $first->gross_commission)->toBe(99.0)
        ->and((float) $first->net_commission)->toBe(99.0)
        ->and($first->status)->toBe('earned');

    expect(OrderCommission::query()->where('marketplace_order_id', $order->id)->count())->toBe(1);
});

test('changing the platform rate does not mutate an already earned commission', function (): void {
    $seller = sariFinanceSeller();
    $service = app(CommissionService::class);

    $firstOrder = sariFinanceOrder($seller, subtotal: 1000.00, deliveredAt: now()->addSecond());
    $oldCommission = $service->recordForDeliveredOrder($firstOrder);

    $this->travel(2)->minutes();

    $newRate = $service->setCurrentRate(12.0, null, 'Test rate change');
    $secondOrder = sariFinanceOrder($seller, subtotal: 1000.00, deliveredAt: now()->addSecond());
    $newCommission = $service->recordForDeliveredOrder($secondOrder);

    expect((float) $oldCommission->fresh()->rate_percent)->toBe(10.0)
        ->and((float) $oldCommission->fresh()->net_commission)->toBe(100.0)
        ->and((float) $newRate->rate_percent)->toBe(12.0)
        ->and((float) $newCommission->rate_percent)->toBe(12.0)
        ->and((float) $newCommission->net_commission)->toBe(120.0)
        ->and((float) PlatformSetting::valueOf('commission_rate'))->toBe(12.0);
});

test('delivery workflow creates all financial ledgers in the delivered transition', function (): void {
    $seller = sariFinanceSeller();
    $courier = sariFinanceCourier();
    $order = sariFinanceOrder(
        $seller,
        status: 'arrived_buyer',
        subtotal: 1250.00,
        deliveredAt: null,
        courier: $courier
    );

    $deliveredAt = now()->addSecond();
    $workflow = app(MarketplaceOrderWorkflowService::class);

    $updated = $workflow->transition(
        $order,
        'arrived_buyer',
        'delivered',
        'Order Delivered Successfully',
        'Test delivery completed.',
        'both',
        [
            'delivered_at' => $deliveredAt,
            'payment_status' => 'paid',
        ]
    );

    expect($updated->status)->toBe('delivered')
        ->and($updated->commission)->not->toBeNull()
        ->and((float) $updated->commission->eligible_amount)->toBe(1250.0)
        ->and((float) $updated->commission->net_commission)->toBe(125.0)
        ->and($updated->paymentTransactions)->toHaveCount(1)
        ->and($updated->sellerSettlement)->not->toBeNull()
        ->and((float) $updated->sellerSettlement->seller_net_amount)->toBe(1125.0)
        ->and($updated->riderEarning)->not->toBeNull()
        ->and((float) $updated->riderEarning->delivery_fee_amount)->toBe(80.0);
});

test('a non delivered order cannot earn commission', function (): void {
    $seller = sariFinanceSeller();
    $order = sariFinanceOrder($seller, status: 'in_transit', subtotal: 500.00, deliveredAt: null);

    app(CommissionService::class)->recordForDeliveredOrder($order);
})->throws(\Illuminate\Validation\ValidationException::class);

test('a delivered but unpaid order cannot earn commission', function (): void {
    $seller = sariFinanceSeller();
    $order = sariFinanceOrder(
        $seller,
        status: 'delivered',
        subtotal: 500.00,
        deliveredAt: now(),
        paymentStatus: 'pending'
    );

    app(CommissionService::class)->recordForDeliveredOrder($order);
})->throws(\Illuminate\Validation\ValidationException::class);

test('signed commission adjustments also reconcile the seller payable', function (): void {
    $seller = sariFinanceSeller();
    $courier = sariFinanceCourier();
    $order = sariFinanceOrder($seller, subtotal: 1000.00, deliveredAt: now()->addSecond(), courier: $courier);

    app(\App\Services\FinancialFlowService::class)->recordCompletedOrder($order);

    $service = app(CommissionService::class);
    $commission = $order->fresh()->commission;

    $adjustment = $service->addAdjustment(
        $commission,
        -25.00,
        'manual_adjustment',
        'Approved finance correction'
    );

    $commission->refresh();
    $settlement = SellerSettlement::query()
        ->where('marketplace_order_id', $order->id)
        ->firstOrFail();

    expect((float) $adjustment->amount)->toBe(-25.0)
        ->and((float) $commission->adjustment_total)->toBe(-25.0)
        ->and((float) $commission->net_commission)->toBe(75.0)
        ->and($commission->status)->toBe('adjusted')
        ->and((float) $settlement->platform_commission_amount)->toBe(75.0)
        ->and((float) $settlement->seller_net_amount)->toBe(925.0);

    expect(CommissionAuditLog::query()
        ->where('order_commission_id', $commission->id)
        ->where('action', 'commission_adjusted')
        ->exists())->toBeTrue();
});
