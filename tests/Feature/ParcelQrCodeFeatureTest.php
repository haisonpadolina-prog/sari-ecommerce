<?php

use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Orders\MarketplaceOrderEvent;
use App\Models\Platform\LogisticsNotification;
use App\Models\Platform\SellerNotification;
use App\Services\Delivery\ParcelQrScanRecorder;
use App\Support\ParcelQrCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

function qrFixtureOrder(string $orderNumber = 'SARI-QR-TEST-001'): MarketplaceOrder
{
    $seller = SellerAccount::query()->create([
        'email' => strtolower($orderNumber).'@seller.test',
        'store_name' => 'QR Test Store',
        'store_status' => 'open',
    ]);

    return MarketplaceOrder::query()->create([
        'order_number' => $orderNumber,
        'seller_account_id' => $seller->id,
        'buyer_name' => 'QR Test Buyer',
        'buyer_phone' => '09123456789',
        'buyer_address' => 'Test Address',
        'payment_method' => 'COD',
        'payment_status' => 'pending',
        'items' => [],
        'subtotal' => 100,
        'delivery_fee' => 20,
        'total' => 120,
        'pickup_name' => 'QR Test Store',
        'pickup_address' => 'Test Pickup Address',
        'status' => 'ready_for_pickup',
    ]);
}

test('parcel QR payload resolves only when its signature is valid', function (): void {
    $order = qrFixtureOrder();
    $qr = app(ParcelQrCode::class);

    $payload = $qr->payload($order);
    $resolved = $qr->resolve($payload);

    expect($payload)->toStartWith('SARI-PARCEL|1|')
        ->and($resolved->is($order))->toBeTrue();

    $tampered = str_replace(
        $order->order_number,
        'SARI-QR-TAMPERED',
        $payload
    );

    expect(fn () => $qr->resolve($tampered))
        ->toThrow(ValidationException::class);
});

test('parcel QR scan recording is idempotent per scan key', function (): void {
    $order = qrFixtureOrder('SARI-QR-TEST-002');
    $recorder = app(ParcelQrScanRecorder::class);

    $recorder->record(
        $order,
        'pickup-rider:order:'.$order->id,
        'pickup_rider_handoff',
        'rider',
        10,
        null,
        'order',
        $order->id
    );

    $recorder->record(
        $order,
        'pickup-rider:order:'.$order->id,
        'pickup_rider_handoff',
        'rider',
        10,
        null,
        'order',
        $order->id
    );

    expect(
        $recorder->countForContext(
            'order',
            $order->id,
            'pickup_rider_handoff'
        )
    )->toBe(1);
});


test('one QR checkpoint notifies Buyer Seller and Logistics without duplicate spam', function (): void {
    $logistics = LogisticsAccount::query()->create([
        'last_name' => 'Network',
        'first_name' => 'SARI',
        'sex' => 'N/A',
        'email' => 'qr-logistics@example.test',
        'contact_no' => '09000000000',
        'birthday' => '2000-01-01',
        'age' => 26,
        'province_code' => 'TEST-CAVITE',
        'province_name' => 'Cavite',
        'municipality_code' => 'TEST-IMUS',
        'municipality_name' => 'Imus',
        'barangay_code' => 'TEST-MALAGASANG',
        'barangay_name' => 'Malagasang I-G',
        'street_address' => 'Test Logistics Address',
        'password' => bcrypt('logistics123'),
        'account_status' => 'active',
        'approved_at' => now(),
    ]);

    $order = qrFixtureOrder('SARI-QR-NOTIFY-001');
    $order->forceFill([
        'logistics_account_id' => $logistics->id,
        'status' => 'in_transit',
    ])->save();

    $recorder = app(ParcelQrScanRecorder::class);

    $scan = $recorder->record(
        $order,
        'pickup-rider:order:'.$order->id,
        'pickup_rider_handoff',
        'rider',
        10,
        null,
        'order',
        $order->id
    );

    expect(
        MarketplaceOrderEvent::query()
            ->where('marketplace_order_id', $order->id)
            ->where('audience', 'buyer')
            ->where('type', 'qr_scan_pickup_rider_handoff')
            ->count()
    )->toBe(1)
        ->and(
            SellerNotification::query()
                ->where('seller_account_id', $order->seller_account_id)
                ->where('type', 'parcel_scan')
                ->where('message', 'like', '%'.$order->order_number.'%')
                ->count()
        )->toBe(1)
        ->and(
            LogisticsNotification::query()
                ->where('logistics_account_id', $logistics->id)
                ->where('recipient_type', LogisticsNotification::RECIPIENT_MANAGER)
                ->where('event_key', 'parcel_qr_scan:'.$scan->id)
                ->count()
        )->toBe(1);

    $recorder->record(
        $order,
        'pickup-rider:order:'.$order->id,
        'pickup_rider_handoff',
        'rider',
        10,
        null,
        'order',
        $order->id
    );

    expect(
        MarketplaceOrderEvent::query()
            ->where('marketplace_order_id', $order->id)
            ->where('audience', 'buyer')
            ->where('type', 'qr_scan_pickup_rider_handoff')
            ->count()
    )->toBe(1)
        ->and(
            SellerNotification::query()
                ->where('seller_account_id', $order->seller_account_id)
                ->where('type', 'parcel_scan')
                ->where('message', 'like', '%'.$order->order_number.'%')
                ->count()
        )->toBe(1)
        ->and(
            LogisticsNotification::query()
                ->where('logistics_account_id', $logistics->id)
                ->where('recipient_type', LogisticsNotification::RECIPIENT_MANAGER)
                ->where('event_key', 'parcel_qr_scan:'.$scan->id)
                ->count()
        )->toBe(1);
});

test('new logistics and final delivery QR checkpoints fan out notifications idempotently', function (): void {
    $logistics = LogisticsAccount::query()->create([
        'last_name' => 'QR Flow',
        'first_name' => 'SARI',
        'sex' => 'N/A',
        'email' => 'qr-flow-logistics@example.test',
        'contact_no' => '09000000001',
        'birthday' => '2000-01-01',
        'age' => 26,
        'province_code' => 'TEST-CAVITE-2',
        'province_name' => 'Cavite',
        'municipality_code' => 'TEST-IMUS-2',
        'municipality_name' => 'Imus',
        'barangay_code' => 'TEST-MALAGASANG-2',
        'barangay_name' => 'Malagasang I-G',
        'street_address' => 'QR Flow Address',
        'password' => bcrypt('logistics123'),
        'account_status' => 'active',
        'approved_at' => now(),
    ]);

    $order = qrFixtureOrder('SARI-QR-FLOW-001');
    $order->forceFill([
        'logistics_account_id' => $logistics->id,
        'status' => 'in_transit',
    ])->save();

    $recorder = app(ParcelQrScanRecorder::class);

    foreach ([
        ['origin_hub_transfer_scan', 'origin-transfer:99:order:'.$order->id],
        ['destination_ready_for_delivery', 'ready-delivery:88:order:'.$order->id],
        ['final_delivery_scan', 'final-delivery:88:order:'.$order->id],
    ] as [$checkpoint, $scanKey]) {
        $scan = $recorder->record(
            $order,
            $scanKey,
            $checkpoint,
            'logistics_manager',
            $logistics->id,
            null,
            'order',
            $order->id
        );

        $recorder->record(
            $order,
            $scanKey,
            $checkpoint,
            'logistics_manager',
            $logistics->id,
            null,
            'order',
            $order->id
        );

        expect(
            MarketplaceOrderEvent::query()
                ->where('marketplace_order_id', $order->id)
                ->where('audience', 'buyer')
                ->where('type', 'qr_scan_'.$checkpoint)
                ->count()
        )->toBe(1)
            ->and(
                LogisticsNotification::query()
                    ->where('logistics_account_id', $logistics->id)
                    ->where('event_key', 'parcel_qr_scan:'.$scan->id)
                    ->count()
            )->toBe(1);
    }
});
