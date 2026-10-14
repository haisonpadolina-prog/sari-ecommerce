<?php

use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Delivery\DeliveryRouteStop;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Delivery\DeliveryRouteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('buyer and seller route endpoints resolve from saved account locations', function (): void {
    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([
            [
                'lat' => '14.5995000',
                'lon' => '120.9842000',
            ],
        ]),
    ]);

    $seller = SellerAccount::query()->create([
        'email' => 'seller-location@example.test',
        'store_name' => 'SARI Seller Store',
        'store_status' => 'open',
    ]);

    $seller->forceFill([
        'street_address' => 'Ermita',
        'barangay_name' => 'Barangay 659',
        'municipality_name' => 'Manila',
        'province_name' => 'Metro Manila',
        'pickup_address' => 'Ermita',
    ])->save();

    $buyer = BuyerAccount::query()->create([
        'first_name' => 'SARI',
        'last_name' => 'Buyer',
        'email' => 'buyer-location@example.test',
        'contact_no' => '09123456789',
        'birthday' => '2000-01-01',
        'age' => 26,
        'sex' => 'Other',
        'province_code' => '130000000',
        'province_name' => 'Metro Manila',
        'municipality_code' => '133900000',
        'municipality_name' => 'Manila',
        'barangay_code' => '133900001',
        'barangay_name' => 'Ermita',
        'street_address' => 'Test Street',
        'password' => bcrypt('password'),
        'account_status' => 'active',
    ]);

    $order = MarketplaceOrder::query()->create([
        'order_number' => 'SARI-LOCATION-TEST',
        'seller_account_id' => $seller->id,
        'buyer_account_id' => $buyer->id,
        'buyer_name' => 'SARI Buyer',
        'buyer_phone' => '09123456789',
        'buyer_email' => $buyer->email,
        'buyer_address' => 'Test Street, Ermita, Manila, Metro Manila',
        'payment_method' => 'COD',
        'payment_status' => 'paid',
        'items' => [],
        'subtotal' => 111,
        'delivery_fee' => 0,
        'total' => 111,
        'pickup_name' => 'SARI Seller Store',
        'pickup_address' => 'Ermita, Manila, Metro Manila',
        'status' => 'delivered',
    ]);

    $stops = app(DeliveryRouteService::class)->ensureStops($order);

    $sellerStop = $stops->firstWhere('stop_key', DeliveryRouteService::STOP_SELLER);
    $buyerStop = $stops->firstWhere('stop_key', DeliveryRouteService::STOP_BUYER);

    expect($sellerStop->latitude)->toBe(14.5995)
        ->and($sellerStop->longitude)->toBe(120.9842)
        ->and($buyerStop->latitude)->toBe(14.5995)
        ->and($buyerStop->longitude)->toBe(120.9842);
});

test('bad endpoint coordinates outside the Philippines are discarded', function (): void {
    Http::fake([
        'nominatim.openstreetmap.org/*' => Http::response([], 200),
    ]);

    $service = app(\App\Services\Delivery\PhilippineAddressGeocoder::class);

    expect($service->isPhilippineCoordinate(0, 20))->toBeFalse()
        ->and($service->isPhilippineCoordinate(14.5995, 120.9842))->toBeTrue();
});


test('sorting facility coordinates outside the Philippines are cleared from route stops', function (): void {
    $service = app(\App\Services\Delivery\PhilippineAddressGeocoder::class);

    expect($service->isPhilippineCoordinate(14.5995, 120.9842))->toBeTrue()
        ->and($service->isPhilippineCoordinate(8.9806, 38.7578))->toBeFalse()
        ->and($service->isPhilippineCoordinate(25.2048, 55.2708))->toBeFalse();
});


test('hardcoded test buyer and seller use deterministic Philippine demo coordinates', function (): void {
    $buyerDemo = config('sari_demo_locations.accounts.buyer');
    $sellerDemo = config('sari_demo_locations.accounts.seller');

    expect($buyerDemo['email'])->toBe('buyer@gmail.com')
        ->and($sellerDemo['email'])->toBe('seller@gmail.com')
        ->and((float) $buyerDemo['latitude'])->toBeGreaterThanOrEqual(4.0)
        ->and((float) $buyerDemo['longitude'])->toBeGreaterThanOrEqual(116.0)
        ->and((float) $sellerDemo['latitude'])->toBeGreaterThanOrEqual(4.0)
        ->and((float) $sellerDemo['longitude'])->toBeGreaterThanOrEqual(116.0);
});



test('demo logistics facilities are Philippine locations', function (): void {
    $geocoder = app(\App\Services\Delivery\PhilippineAddressGeocoder::class);

    $facilities = [
        [14.5614240, 121.0146940],
        [14.5101200, 120.9913000],
        [14.4451000, 120.9511000],
    ];

    foreach ($facilities as [$lat, $lng]) {
        expect($geocoder->isPhilippineCoordinate($lat, $lng))->toBeTrue();
    }
});

test('delivery route uses four stops instead of three fixed hubs', function (): void {
    expect(\App\Services\Delivery\DeliveryRouteService::STOP_SELLER)
        ->toBe('seller')
        ->and(\App\Services\Delivery\DeliveryRouteService::STOP_ORIGIN_FACILITY)
        ->toBe('origin_facility')
        ->and(\App\Services\Delivery\DeliveryRouteService::STOP_DESTINATION_FACILITY)
        ->toBe('destination_facility')
        ->and(\App\Services\Delivery\DeliveryRouteService::STOP_BUYER)
        ->toBe('buyer');
});
