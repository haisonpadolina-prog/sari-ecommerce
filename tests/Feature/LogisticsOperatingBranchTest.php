<?php

use App\Models\Accounts\LogisticsAccount;
use App\Models\Delivery\LogisticsFacility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function branchFixtureProvider(string $email): LogisticsAccount
{
    return LogisticsAccount::query()->create([
        'first_name' => 'Branch',
        'last_name' => 'Manager',
        'business_name' => 'Branch Test Logistics',
        'email' => $email,
        'sex' => 'Male',
        'contact_no' => '09171234567',
        'birthday' => '2000-01-01',
        'age' => 26,
        'province_code' => '042100000',
        'province_name' => 'Cavite',
        'municipality_code' => '042103000',
        'municipality_name' => 'Bacoor',
        'barangay_code' => '042103001',
        'barangay_name' => 'Habay',
        'street_address' => 'Branch Test Street',
        'password' => Hash::make('BranchTest123!'),
        'account_status' => 'active',
    ]);
}

function branchFixtureFacility(
    LogisticsAccount $provider,
    string $name,
    string $code,
    string $municipality
): LogisticsFacility {
    return LogisticsFacility::query()->create([
        'logistics_account_id' => $provider->id,
        'code' => $code,
        'name' => $name,
        'address' => $name.', Philippines',
        'province_name' => 'Cavite',
        'municipality_name' => $municipality,
        'barangay_name' => 'Central',
        'latitude' => 14.40,
        'longitude' => 120.95,
        'is_active' => true,
    ]);
}

function branchManagerSession(LogisticsAccount $provider): array
{
    return [
        'is_logistics' => true,
        'logistics_role' => 'manager',
        'logistics_account_id' => $provider->id,
        'logistics_email' => $provider->email,
        'logistics_name' => $provider->displayName(),
    ];
}

test('manager must select an operating branch before dashboard operations', function (): void {
    $provider = branchFixtureProvider('branch-manager@example.test');
    branchFixtureFacility($provider, 'SARI Bacoor Test Hub', 'BR-BACOOR', 'Bacoor');

    $this->withSession(branchManagerSession($provider))
        ->get(route('logistics.dashboard'))
        ->assertRedirect(route('logistics.branch.select'));

    $this->withSession(branchManagerSession($provider))
        ->get(route('logistics.branch.select'))
        ->assertOk()
        ->assertSee('Select Operating Branch')
        ->assertSee('SARI Bacoor Test Hub');
});

test('manager can select only an active facility from own logistics network', function (): void {
    $provider = branchFixtureProvider('branch-owner@example.test');
    $other = branchFixtureProvider('branch-other@example.test');

    $own = branchFixtureFacility($provider, 'SARI Bacoor Own Hub', 'OWN-BAC', 'Bacoor');
    $foreign = branchFixtureFacility($other, 'Private Foreign Hub', 'FOREIGN', 'Makati');

    $this->withSession(branchManagerSession($provider))
        ->post(route('logistics.branch.store'), [
            'facility_id' => $foreign->id,
        ])
        ->assertSessionHasErrors('facility_id');

    $response = $this->withSession(branchManagerSession($provider))
        ->post(route('logistics.branch.store'), [
            'facility_id' => $own->id,
        ]);

    $response
        ->assertRedirect(route('logistics.dashboard'))
        ->assertSessionHas('logistics_operating_facility_id', $own->id)
        ->assertSessionHas('logistics_operating_facility_name', $own->name)
        ->assertSessionMissing('is_logistics_hub_operator')
        ->assertSessionMissing('logistics_facility_id');
});

test('manager can switch operating branch without becoming a hub operator', function (): void {
    $provider = branchFixtureProvider('branch-switch@example.test');
    $bacoor = branchFixtureFacility($provider, 'SARI Bacoor Hub', 'SW-BAC', 'Bacoor');
    $makati = branchFixtureFacility($provider, 'SARI Makati Hub', 'SW-MAK', 'Makati');

    $session = array_merge(branchManagerSession($provider), [
        'logistics_operating_facility_id' => $bacoor->id,
        'logistics_operating_facility_name' => $bacoor->name,
        'logistics_operating_facility_code' => $bacoor->code,
    ]);

    $this->withSession($session)
        ->post(route('logistics.branch.store'), [
            'facility_id' => $makati->id,
        ])
        ->assertRedirect(route('logistics.dashboard'))
        ->assertSessionHas('logistics_operating_facility_id', $makati->id)
        ->assertSessionMissing('is_logistics_hub_operator')
        ->assertSessionMissing('logistics_facility_id');
});
