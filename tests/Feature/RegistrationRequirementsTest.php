<?php

use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Services\Registration\AccountRegistrationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function (): void { $this->withoutVite(); });

function approvedLogisticsForRegistrationTests(): LogisticsAccount
{
    return LogisticsAccount::query()->create([
        'last_name' => 'Mendoza', 'first_name' => 'Carlos', 'sex' => 'Male',
        'email' => 'operations@bilishatid.example.com', 'contact_no' => '09285551234',
        'birthday' => '1990-01-01', 'age' => 36, 'province_code' => 'NCR', 'province_name' => 'Metro Manila',
        'municipality_code' => 'PASIG', 'municipality_name' => 'Pasig City', 'barangay_code' => 'KAP', 'barangay_name' => 'Kapitolyo',
        'street_address' => '88 West Capitol Drive', 'business_name' => 'Bilis Hatid Express Inc.',
        'password' => Hash::make('Hatid@2026'), 'account_status' => 'active', 'approved_at' => now(),
    ]);
}

test('philippine mobile formats normalize to one canonical value', function (): void {
    $policy = app(AccountRegistrationPolicy::class);
    expect($policy->normalizeMobile('09171234567'))->toBe('09171234567')
        ->and($policy->normalizeMobile('+639171234567'))->toBe('09171234567');
});

test('pending rider activation link activates once and cannot be reused', function (): void {
    $logistics = approvedLogisticsForRegistrationTests();
    $token = str_repeat('a', 64);
    $rider = CourierAccount::query()->create([
        'logistics_account_id' => $logistics->id, 'last_name' => 'Garcia', 'first_name' => 'Pedro', 'sex' => 'N/A',
        'email' => 'pedro@example.test', 'contact_no' => '09451239876', 'birthday' => '1995-03-22', 'age' => 31,
        'province_code' => 'NCR', 'province_name' => 'Metro Manila', 'municipality_code' => 'PASIG', 'municipality_name' => 'Pasig City',
        'barangay_code' => 'KAP', 'barangay_name' => 'Kapitolyo', 'street_address' => '88 West Capitol Drive',
        'password' => Hash::make('unusable-secret'), 'vehicle_type' => 'Motorcycle', 'plate_number' => '123ABC',
        'license_number' => 'N01-23-456789', 'license_expiry_date' => now()->addYear()->toDateString(),
        'account_status' => 'pending_activation', 'availability_status' => 'offline',
        'activation_token_hash' => hash('sha256', $token), 'activation_expires_at' => now()->addHours(24),
    ]);

    $this->post(route('rider.activation.activate', $token), ['password' => 'Rider@2026', 'password_confirmation' => 'Rider@2026'])
        ->assertRedirect(route('login'));
    expect($rider->fresh()->account_status)->toBe('active')->and(Hash::check('Rider@2026', $rider->fresh()->password))->toBeTrue();
    $this->get(route('rider.activation.show', $token))->assertNotFound();
});

test('expired rider activation link is rejected', function (): void {
    $logistics = approvedLogisticsForRegistrationTests();
    $token = str_repeat('b', 64);
    CourierAccount::query()->create([
        'logistics_account_id' => $logistics->id, 'last_name' => 'Garcia', 'first_name' => 'Pedro', 'sex' => 'N/A',
        'email' => 'expired@example.test', 'contact_no' => '09451239877', 'birthday' => '1995-03-22', 'age' => 31,
        'province_code' => 'NCR', 'province_name' => 'Metro Manila', 'municipality_code' => 'PASIG', 'municipality_name' => 'Pasig City',
        'barangay_code' => 'KAP', 'barangay_name' => 'Kapitolyo', 'street_address' => '88 West Capitol Drive',
        'password' => Hash::make('unusable-secret'), 'vehicle_type' => 'Motorcycle', 'plate_number' => '124ABC',
        'license_number' => 'N01-23-456788', 'license_expiry_date' => now()->addYear()->toDateString(),
        'account_status' => 'pending_activation', 'activation_token_hash' => hash('sha256', $token), 'activation_expires_at' => now()->subMinute(),
    ]);
    $this->get(route('rider.activation.show', $token))->assertStatus(410);
});

test('non active logistics session cannot use protected logistics routes', function (): void {
    $logistics = approvedLogisticsForRegistrationTests();
    $logistics->update(['account_status' => 'suspended']);
    $this->withSession(['is_logistics' => true, 'logistics_account_id' => $logistics->id, 'logistics_email' => $logistics->email])
        ->get(route('logistics.rider-management'))->assertForbidden();
});
