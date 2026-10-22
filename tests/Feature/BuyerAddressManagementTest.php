<?php

use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\BuyerAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();
});

function buyerAddressTestAccount(string $email): BuyerAccount
{
    $buyer = new BuyerAccount();
    $buyer->forceFill([
        'first_name' => 'Test',
        'last_name' => 'Buyer',
        'sex' => 'Male',
        'email' => $email,
        'contact_no' => '09123456789',
        'birthday' => '2000-01-01',
        'age' => 26,
        'province_code' => '1339',
        'province_name' => 'Metro Manila',
        'municipality_code' => '133900',
        'municipality_name' => 'Manila',
        'barangay_code' => '133900001',
        'barangay_name' => 'Test Barangay',
        'street_address' => '100 Test Street',
        'password' => Hash::make('Buyer@2026'),
        'account_status' => 'active',
        'approved_at' => now(),
    ]);
    $buyer->save();

    return $buyer;
}

function buyerSession(BuyerAccount $buyer): array
{
    return [
        'is_buyer' => true,
        'buyer_account_id' => $buyer->id,
        'buyer_email' => $buyer->email,
    ];
}

function addressPayload(array $overrides = []): array
{
    return array_merge([
        'label' => 'Home',
        'recipient_name' => 'Test Buyer',
        'contact_no' => '09123456789',
        'street_address' => '25 Sample Street',
        'barangay_name' => 'Central',
        'municipality_name' => 'Manila',
        'province_name' => 'Metro Manila',
        'postal_code' => '1000',
    ], $overrides);
}

test('buyer account UI uses the edit icon as the only visible profile photo control', function (): void {
    $buyer = buyerAddressTestAccount('profile-ui@example.test');

    $this->withSession(buyerSession($buyer))
        ->get(route('buyer.account'))
        ->assertOk()
        ->assertSee('Change profile photo', false)
        ->assertDontSee('Choose image')
        ->assertDontSee('No new image selected')
        ->assertSee('Delivery Address Book');
});

test('buyer can add addresses and the first address becomes default', function (): void {
    $buyer = buyerAddressTestAccount('address-owner@example.test');

    $this->withSession(buyerSession($buyer))
        ->post(route('buyer.account.addresses.store'), addressPayload())
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $address = BuyerAddress::query()->where('buyer_account_id', $buyer->id)->first();

    expect($address)->not->toBeNull();
    expect($address->label)->toBe('Home');
    expect($address->is_default)->toBeTrue();
});

test('buyer can change the default address', function (): void {
    $buyer = buyerAddressTestAccount('default-address@example.test');

    $first = $buyer->addresses()->create(array_merge(addressPayload(), [
        'is_default' => true,
    ]));

    $second = $buyer->addresses()->create(array_merge(
        addressPayload([
            'label' => 'Office',
            'street_address' => '12F Office Tower',
        ]),
        ['is_default' => false]
    ));

    $this->withSession(buyerSession($buyer))
        ->post(route('buyer.account.addresses.default', $second))
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect($first->fresh()->is_default)->toBeFalse();
    expect($second->fresh()->is_default)->toBeTrue();
});

test('buyer cannot edit or delete another buyer address', function (): void {
    $owner = buyerAddressTestAccount('owner-address@example.test');
    $other = buyerAddressTestAccount('other-address@example.test');

    $otherAddress = $other->addresses()->create(array_merge(
        addressPayload(),
        ['is_default' => true]
    ));

    $this->withSession(buyerSession($owner))
        ->patch(
            route('buyer.account.addresses.update', $otherAddress),
            addressPayload(['label' => 'Intruder'])
        )
        ->assertNotFound();

    $this->withSession(buyerSession($owner))
        ->delete(route('buyer.account.addresses.destroy', $otherAddress))
        ->assertNotFound();

    expect($otherAddress->fresh())->not->toBeNull();
    expect($otherAddress->fresh()->label)->toBe('Home');
});

test('deleting the default address promotes another saved address', function (): void {
    $buyer = buyerAddressTestAccount('delete-default@example.test');

    $default = $buyer->addresses()->create(array_merge(
        addressPayload(),
        ['is_default' => true]
    ));

    $backup = $buyer->addresses()->create(array_merge(
        addressPayload([
            'label' => 'Office',
            'street_address' => 'Office Address',
        ]),
        ['is_default' => false]
    ));

    $this->withSession(buyerSession($buyer))
        ->delete(route('buyer.account.addresses.destroy', $default))
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect(BuyerAddress::query()->find($default->id))->toBeNull();
    expect($backup->fresh()->is_default)->toBeTrue();
});
