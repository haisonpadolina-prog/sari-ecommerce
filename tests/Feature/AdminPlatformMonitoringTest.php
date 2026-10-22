<?php

use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\SellerProduct;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();
});

function monitoringAdmin(): AdminAccount
{
    return AdminAccount::query()->create([
        'name' => 'Monitoring Admin',
        'email' => 'monitoring-admin@example.test',
        'password' => Hash::make('admin-secret'),
        'role' => 'super_admin',
        'can_manage_platform_settings' => true,
    ]);
}

function monitoringSeller(string $email = 'store@example.test'): SellerAccount
{
    return SellerAccount::query()->create([
        'email' => $email,
        'store_name' => 'Davao Test Store',
        'first_name' => 'Seller',
        'last_name' => 'Owner',
        'contact_no' => '09123456789',
        'province_name' => 'Davao del Sur',
        'municipality_name' => 'Davao City',
        'barangay_name' => 'Buhangin',
        'street_address' => 'Test Street',
        'password' => Hash::make('secret123'),
        'registration_status' => 'approved',
        'account_status' => 'active',
        'approved_at' => now(),
    ]);
}

function monitoringBuyer(string $email = 'buyer-monitor@example.test'): BuyerAccount
{
    return BuyerAccount::query()->create([
        'last_name' => 'Buyer',
        'first_name' => 'Monitor',
        'sex' => 'Other',
        'email' => $email,
        'contact_no' => '09123456789',
        'birthday' => '2000-01-01',
        'age' => 26,
        'province_code' => 'PH-40',
        'province_name' => 'Cavite',
        'municipality_code' => 'IMUS',
        'municipality_name' => 'Imus',
        'barangay_code' => 'TEST',
        'barangay_name' => 'Malagasang',
        'street_address' => 'Buyer Street',
        'password' => Hash::make('secret123'),
        'account_status' => 'active',
        'approved_at' => now(),
    ]);
}

function monitoringLogistics(string $email = 'logistics-monitor@example.test'): LogisticsAccount
{
    return LogisticsAccount::query()->create([
        'last_name' => 'Operator',
        'first_name' => 'Logistics',
        'sex' => 'Other',
        'email' => $email,
        'contact_no' => '09123456780',
        'birthday' => '1990-01-01',
        'age' => 36,
        'province_code' => 'PH-40',
        'province_name' => 'Cavite',
        'municipality_code' => 'IMUS',
        'municipality_name' => 'Imus',
        'barangay_code' => 'TEST',
        'barangay_name' => 'Anabu',
        'street_address' => 'Logistics Street',
        'business_name' => 'SARI Test Logistics',
        'position' => 'Operations Manager',
        'service_areas' => 'Imus, Bacoor, Dasmariñas',
        'password' => Hash::make('secret123'),
        'account_status' => 'active',
        'approved_at' => now(),
    ]);
}

function monitoringRider(LogisticsAccount $logistics, string $email = 'rider-monitor@example.test'): CourierAccount
{
    return CourierAccount::query()->create([
        'logistics_account_id' => $logistics->id,
        'last_name' => 'Rider',
        'first_name' => 'Pedro',
        'sex' => 'Other',
        'email' => $email,
        'contact_no' => '09123456781',
        'birthday' => '1995-01-01',
        'age' => 31,
        'province_code' => 'PH-40',
        'province_name' => 'Cavite',
        'municipality_code' => 'IMUS',
        'municipality_name' => 'Imus',
        'barangay_code' => 'TEST',
        'barangay_name' => 'Anabu',
        'street_address' => 'Rider Street',
        'password' => Hash::make('secret123'),
        'vehicle_type' => 'Motorcycle',
        'plate_number' => 'SARI-123',
        'account_status' => 'active',
        'approved_at' => now(),
        'availability_status' => 'online',
    ]);
}

function monitoringOrder(
    SellerAccount $seller,
    BuyerAccount $buyer,
    ?LogisticsAccount $logistics = null,
    ?CourierAccount $rider = null,
    string $status = 'new',
    string $number = 'SARI-MON-001'
): MarketplaceOrder {
    return MarketplaceOrder::query()->create([
        'order_number' => $number,
        'seller_account_id' => $seller->id,
        'buyer_account_id' => $buyer->id,
        'logistics_account_id' => $logistics?->id,
        'buyer_name' => 'Monitor Buyer',
        'buyer_phone' => '09123456789',
        'buyer_email' => $buyer->email,
        'buyer_address' => 'Buyer Street, Imus, Cavite',
        'payment_method' => 'COD',
        'payment_status' => 'pending',
        'items' => [[
            'product_id' => 100,
            'name' => 'Monitoring Product',
            'variant_label' => 'Black / Large',
            'qty' => 2,
            'price' => 250,
            'line_total' => 500,
        ]],
        'subtotal' => 500,
        'delivery_fee' => 80,
        'total' => 580,
        'pickup_name' => $seller->store_name,
        'pickup_address' => 'Seller Pickup Address',
        'status' => $status,
        'courier_name' => $rider ? trim($rider->first_name.' '.$rider->last_name) : null,
        'courier_email' => $rider?->email,
        'delivered_at' => $status === 'delivered' ? now() : null,
        'cancelled_at' => $status === 'cancelled' ? now() : null,
    ]);
}

test('non admin cannot open platform monitoring', function (): void {
    $this->get(route('admin.monitoring'))
        ->assertRedirect(route('login'));
});

test('admin can inspect a seller store and its products', function (): void {
    $admin = monitoringAdmin();
    $seller = monitoringSeller();

    SellerProduct::query()->create([
        'seller_account_id' => $seller->id,
        'name' => 'Cacao Test Product',
        'category' => 'Food',
        'sku' => 'CACAO-01',
        'price' => 350,
        'stock' => 15,
        'discount' => 0,
        'moderation_status' => 'approved',
        'screening_risk' => 'low',
    ]);

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->get(route('admin.monitoring.sellers.show', $seller))
        ->assertOk()
        ->assertSee('Davao Test Store')
        ->assertSee('Cacao Test Product')
        ->assertSee('Administrative controls');
});

test('admin can inspect logistics hubs and riders', function (): void {
    $admin = monitoringAdmin();
    $logistics = monitoringLogistics();
    $rider = monitoringRider($logistics);

    LogisticsFacility::query()->create([
        'logistics_account_id' => $logistics->id,
        'code' => 'IMUS-HUB',
        'name' => 'Imus Sorting Hub',
        'address' => 'Imus Boulevard, Cavite',
        'province_name' => 'Cavite',
        'municipality_name' => 'Imus',
        'barangay_name' => 'Anabu',
        'latitude' => 14.4297,
        'longitude' => 120.9367,
        'is_active' => true,
    ]);

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->get(route('admin.monitoring.logistics.show', $logistics))
        ->assertOk()
        ->assertSee('Imus Sorting Hub')
        ->assertSee('Pedro Rider')
        ->assertSee('Open map');
});

test('admin can see buyer successful and cancelled order counts', function (): void {
    $admin = monitoringAdmin();
    $seller = monitoringSeller();
    $buyer = monitoringBuyer();

    monitoringOrder($seller, $buyer, status: 'delivered', number: 'SARI-MON-DONE');
    monitoringOrder($seller, $buyer, status: 'cancelled', number: 'SARI-MON-CANCEL');

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->get(route('admin.monitoring.buyers.show', ['role' => 'buyer', 'id' => $buyer->id]))
        ->assertOk()
        ->assertSee('Successful')
        ->assertSee('Cancelled')
        ->assertSee('SARI-MON-DONE')
        ->assertSee('SARI-MON-CANCEL');
});

test('admin can see rider current product load and delivery history', function (): void {
    $admin = monitoringAdmin();
    $seller = monitoringSeller();
    $buyer = monitoringBuyer();
    $logistics = monitoringLogistics();
    $rider = monitoringRider($logistics);

    monitoringOrder($seller, $buyer, $logistics, $rider, 'in_transit', 'SARI-MON-ACTIVE');
    monitoringOrder($seller, $buyer, $logistics, $rider, 'delivered', 'SARI-MON-DELIVERED');

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->get(route('admin.monitoring.riders.show', $rider))
        ->assertOk()
        ->assertSee('Monitoring Product')
        ->assertSee('Black / Large')
        ->assertSee('SARI-MON-ACTIVE')
        ->assertSee('SARI-MON-DELIVERED');
});

test('admin block is reversible and prevents buyer login while blocked', function (): void {
    $admin = monitoringAdmin();
    $buyer = monitoringBuyer('blocked-monitor@example.test');

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->post(route('admin.users.block', ['role' => 'buyer', 'id' => $buyer->id]), [
        'reason' => 'Security review required before account access continues.',
    ])->assertRedirect();

    expect($buyer->fresh()->account_status)->toBe('blocked');

    $this->post(route('login.submit'), [
        'email' => $buyer->email,
        'password' => 'secret123',
    ])->assertSessionHasErrors('email');

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->post(route('admin.users.unblock', ['role' => 'buyer', 'id' => $buyer->id]))
        ->assertRedirect();

    expect($buyer->fresh()->account_status)->toBe('active');
});
