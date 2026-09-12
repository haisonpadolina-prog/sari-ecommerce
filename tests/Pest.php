<?php

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
 // ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

function sariFinanceSeller(): \App\Models\SellerAccount
{
    return \App\Models\SellerAccount::query()->create([
        'email' => fake()->unique()->safeEmail(),
        'store_name' => 'Finance Test Store',
    ]);
}

function sariFinanceCourier(): \App\Models\CourierAccount
{
    return \App\Models\CourierAccount::query()->create([
        'registration_application_id' => null,
        'last_name' => 'Rider',
        'first_name' => 'Finance',
        'middle_initial' => null,
        'sex' => 'Male',
        'email' => fake()->unique()->safeEmail(),
        'contact_no' => '09123456789',
        'birthday' => '2000-01-01',
        'age' => 26,
        'province_code' => 'TEST',
        'province_name' => 'Test Province',
        'municipality_code' => 'TEST',
        'municipality_name' => 'Test City',
        'barangay_code' => 'TEST',
        'barangay_name' => 'Test Barangay',
        'street_address' => 'Test Address',
        'password' => \Illuminate\Support\Facades\Hash::make('finance-test-password'),
        'vehicle_type' => 'Motorcycle',
        'plate_number' => fake()->unique()->bothify('???-####'),
        'account_status' => 'active',
        'approved_at' => now(),
        'availability_status' => 'online',
        'rating' => 5.00,
    ]);
}

function sariFinanceOrder(
    \App\Models\SellerAccount $seller,
    string $status = 'delivered',
    float $subtotal = 990.00,
    mixed $deliveredAt = null,
    ?\App\Models\CourierAccount $courier = null,
    ?string $paymentStatus = null
): \App\Models\MarketplaceOrder {
    return \App\Models\MarketplaceOrder::query()->create([
        'order_number' => 'SARI-TEST-' . fake()->unique()->numerify('########'),
        'seller_account_id' => $seller->id,
        'buyer_name' => 'Test Buyer',
        'buyer_address' => 'Test Address',
        'payment_method' => 'COD',
        'payment_status' => $paymentStatus ?? ($status === 'delivered' ? 'paid' : 'pending'),
        'subtotal' => $subtotal,
        'delivery_fee' => 80,
        'total' => $subtotal + 80,
        'status' => $status,
        'courier_name' => $courier ? trim($courier->first_name . ' ' . $courier->last_name) : null,
        'courier_email' => $courier?->email,
        'delivered_at' => $deliveredAt ?? ($status === 'delivered' ? now() : null),
    ]);
}
