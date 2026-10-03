<?php

use App\Models\Accounts\SellerAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();
    Storage::fake('public');
    Storage::fake('local');
});

function managedSellerAccount(array $overrides = []): SellerAccount
{
    return SellerAccount::query()->create(array_merge([
        'last_name' => 'Seller',
        'first_name' => 'Sari',
        'middle_initial' => 'A',
        'sex' => 'Male',
        'email' => 'seller-account@example.test',
        'contact_no' => '09123456789',
        'birthday' => '2000-05-12',
        'age' => 26,
        'province_code' => '0410',
        'province_name' => 'Batangas',
        'municipality_code' => '041014',
        'municipality_name' => 'Lipa City',
        'barangay_code' => 'TEST',
        'barangay_name' => 'Test Barangay',
        'street_address' => 'Verified Registration Address',
        'pickup_address' => 'Warehouse Pickup Address',
        'password' => Hash::make('secret123'),
        'profile_image_path' => 'profile-images/seller/avatar.jpg',
        'store_name' => 'SARI Test Store',
        'line_of_business' => 'General Merchandise',
        'store_description' => 'Original description',
        'store_phone' => '09110000000',
        'store_public_email' => 'store@example.test',
        'store_status' => 'open',
        'pickup_instructions' => 'Use the receiving gate.',
        'id_path' => 'registration-documents/seller/id/valid-id.pdf',
        'business_permit_path' => 'registration-documents/seller/permit/business-permit.pdf',
        'registration_status' => 'approved',
        'account_status' => 'active',
        'approved_at' => now(),
    ], $overrides));
}

function sellerSession(SellerAccount $seller): array
{
    return [
        'is_seller' => true,
        'seller_account_id' => $seller->id,
    ];
}

test('seller account update changes identity without overwriting store or verified address', function (): void {
    $seller = managedSellerAccount();

    $this->withSession(sellerSession($seller))
        ->patch(route('seller.account.update'), [
            'first_name' => 'Updated',
            'last_name' => 'Seller',
            'middle_initial' => 'B',
            'sex' => 'Female',
            'contact_no' => '09999999999',
            'birthday' => '1999-06-15',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $seller->refresh();

    expect($seller->first_name)->toBe('Updated')
        ->and($seller->sex)->toBe('Female')
        ->and($seller->contact_no)->toBe('09999999999')
        ->and($seller->store_name)->toBe('SARI Test Store')
        ->and($seller->street_address)->toBe('Verified Registration Address');
});

test('seller can securely view their stored profile photo and verification documents', function (): void {
    $seller = managedSellerAccount();

    Storage::disk('public')->put($seller->profile_image_path, 'profile-photo-bytes');
    Storage::disk('local')->put($seller->id_path, 'valid-id-bytes');
    Storage::disk('local')->put($seller->business_permit_path, 'permit-bytes');

    $this->withSession(sellerSession($seller))
        ->get(route('seller.account.profile-photo'))
        ->assertOk();

    $this->withSession(sellerSession($seller))
        ->get(route('seller.account.documents.show', 'id'))
        ->assertOk();

    $this->withSession(sellerSession($seller))
        ->get(route('seller.account.documents.show', 'permit'))
        ->assertOk();
});


test('removing the active profile photo does not delete the original registration evidence', function (): void {
    $seller = managedSellerAccount();
    Storage::disk('public')->put($seller->profile_image_path, 'registration-profile-photo');

    $this->withSession(sellerSession($seller))
        ->patch(route('seller.account.update'), [
            'first_name' => $seller->first_name,
            'last_name' => $seller->last_name,
            'middle_initial' => $seller->middle_initial,
            'sex' => $seller->sex,
            'contact_no' => $seller->contact_no,
            'birthday' => $seller->birthday->format('Y-m-d'),
            'remove_profile_image' => '1',
        ])
        ->assertRedirect();

    expect($seller->fresh()->profile_image_path)->toBeNull();
    Storage::disk('public')->assertExists('profile-images/seller/avatar.jpg');
});

test('seller password update requires the current password and stores the new hash', function (): void {
    $seller = managedSellerAccount();

    $this->withSession(sellerSession($seller))
        ->patch(route('seller.account.password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'new-secret-123',
            'password_confirmation' => 'new-secret-123',
        ])
        ->assertSessionHasErrors('current_password', null, 'password');

    $this->withSession(sellerSession($seller))
        ->patch(route('seller.account.password.update'), [
            'current_password' => 'secret123',
            'password' => 'new-secret-123',
            'password_confirmation' => 'new-secret-123',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(Hash::check('new-secret-123', (string) $seller->fresh()->password))->toBeTrue();
});

test('store management updates public and pickup settings without changing verified registration address', function (): void {
    $seller = managedSellerAccount();

    $this->withSession(sellerSession($seller))
        ->patch(route('seller.store.update'), [
            'store_name' => 'Updated Public Store',
            'store_description' => 'Updated buyer-facing description.',
            'store_phone' => '09220000000',
            'store_public_email' => 'public@example.test',
            'store_status' => 'paused',
            'pickup_address' => 'New Warehouse, Lipa City, Batangas',
            'pickup_instructions' => 'Call receiving staff before arrival.',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $seller->refresh();

    expect($seller->store_name)->toBe('Updated Public Store')
        ->and($seller->store_status)->toBe('paused')
        ->and($seller->pickup_address)->toBe('New Warehouse, Lipa City, Batangas')
        ->and($seller->street_address)->toBe('Verified Registration Address');
});
