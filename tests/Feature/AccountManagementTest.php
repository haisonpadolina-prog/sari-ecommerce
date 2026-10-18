<?php

use App\Http\Middleware\Platform\EnforcePlatformOperationalRules;
use App\Http\Middleware\Shared\EnsureSessionAccountAccessible;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');
    $this->withoutVite();
});

function sariProfilePngBytes(): string
{
    $chunk = static fn (string $type, string $data): string => pack('N', strlen($data)).$type.$data.pack('N', crc32($type.$data));
    $pixels = str_repeat("\x00".str_repeat("\x00\x00\x00", 120), 120);

    return "\x89PNG\r\n\x1a\n"
        .$chunk('IHDR', pack('NNCCCCC', 120, 120, 8, 2, 0, 0, 0))
        .$chunk('IDAT', gzcompress($pixels)).$chunk('IEND', '');
}

function sariProfileAccount(string $role, string $email): mixed
{
    $class = match ($role) {
        'buyer' => BuyerAccount::class,
        'seller' => SellerAccount::class,
        'logistics' => LogisticsAccount::class,
        'courier' => CourierAccount::class,
    };

    $account = new $class;

    $account->forceFill([
        'first_name' => 'Original',
        'last_name' => 'Account',
        'sex' => 'Male',
        'email' => $email,
        'contact_no' => '09123456789',
        'birthday' => '2000-01-01',
        'age' => 26,
        'province_code' => '0434',
        'province_name' => 'Laguna',
        'municipality_code' => '043424',
        'municipality_name' => 'Santa Cruz',
        'barangay_code' => '043424001',
        'barangay_name' => 'Test Barangay',
        'street_address' => 'Original Street',
        'password' => Hash::make('original-password'),
        'account_status' => 'active',
        'approved_at' => now(),
    ]);

    if ($role === 'seller') {
        $account->forceFill([
            'store_name' => 'Actual Store',
            'line_of_business' => 'Fashion',
            'registration_status' => 'approved',
        ]);
    }

    if ($role === 'logistics') {
        $account->forceFill([
            'business_name' => 'Actual Logistics',
        ]);
    }

    if ($role === 'courier') {
        $account->forceFill([
            'vehicle_type' => 'Motorcycle',
            'plate_number' => 'ABC-123',
        ]);
    }

    $account->save();

    return $account;
}

test('account management loads and updates only the session owner', function (string $role, string $route): void {
    $owner = sariProfileAccount($role, $role.'@example.test');
    $other = sariProfileAccount($role, 'other-'.$role.'@example.test');

    $session = [
        'is_'.$role => true,
        $role.'_account_id' => $owner->id,
        $role.'_email' => $owner->email,
    ];

    $this->withSession($session)
        ->get(route($route))
        ->assertOk()
        ->assertSee($owner->email)
        ->assertDontSee($other->email);

    $payload = [
        'first_name' => 'Updated',
        'last_name' => 'Owner',
        'contact_no' => '09987654321',
        'email' => $owner->email,
        'street_address' => 'Updated Street',
        'sex' => 'Male',
        'birthday' => '2000-01-01',
        'province_name' => 'Laguna',
        'municipality_name' => 'Santa Cruz',
        'barangay_name' => 'Test Barangay',
        'store_name' => 'Updated Store',
        'line_of_business' => 'Fashion',
        'vehicle_type' => 'Motorcycle',
        'plate_number' => 'ABC-123',
        'profile_image' => UploadedFile::fake()->createWithContent(
            'avatar.png',
            sariProfilePngBytes()
        ),
        'account_id' => $other->id,
        $role.'_account_id' => $other->id,
        'account_status' => 'banned',
    ];

    $this->withSession($session)
        ->patch(route($route.'.update'), $payload)
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect($owner->fresh()->first_name)->toBe('Updated');
    expect($owner->fresh()->account_status)->toBe('active');
    expect($other->fresh()->first_name)->toBe('Original');

    Storage::disk('public')->assertExists(
        $owner->fresh()->profile_image_path
    );

    $this->withSession($session)
        ->get(route($route))
        ->assertOk()
        ->assertSee('Updated');
})->with([
    ['buyer', 'buyer.account'],
    ['seller', 'seller.account'],
    ['logistics', 'logistics.account-management'],
    ['courier', 'courier.profile'],
]);

test('seller password changes require the current password', function (): void {
    $account = sariProfileAccount(
        'seller',
        'seller-profile@example.test'
    );

    $payload = [
        'first_name' => 'Updated',
        'last_name' => 'Account',
        'sex' => 'Male',
        'contact_no' => '09123456789',
        'birthday' => '2000-01-01',
        'store_name' => 'Actual Store',
        'line_of_business' => 'Fashion',
        'street_address' => 'Original Street',
        'current_password' => 'wrong-password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ];

    $session = [
        'is_seller' => true,
        'seller_account_id' => $account->id,
    ];

    $this->withSession($session)
        ->patch(route('seller.account.password.update'), $payload)
        ->assertSessionHasErrors(
            'current_password',
            null,
            'password'
        );

    expect($account->fresh()->first_name)->toBe('Original');

    expect(
        Hash::check(
            'original-password',
            $account->fresh()->password
        )
    )->toBeTrue();

    $payload['current_password'] = 'original-password';

    $this->withSession($session)
        ->patch(route('seller.account.password.update'), $payload)
        ->assertSessionHasNoErrors();

    expect(
        Hash::check(
            'new-password',
            $account->fresh()->password
        )
    )->toBeTrue();
});

test('buyer login keeps previously edited demo profile details', function (): void {
    $account = sariProfileAccount('buyer', 'buyer@gmail.com');

    $account->update([
        'first_name' => 'Saved Profile',
    ]);

    $this->post(route('login.submit'), [
        'email' => 'buyer@gmail.com',
        'password' => 'buyer123',
    ])->assertRedirect(route('buyer.dashboard'));

    expect($account->fresh()->first_name)->toBe('Saved Profile');
});

test('another role cannot update a buyer account', function (): void {
    $account = sariProfileAccount(
        'buyer',
        'protected-buyer@example.test'
    );

    $this->withoutMiddleware([
        EnsureSessionAccountAccessible::class,
        EnforcePlatformOperationalRules::class,
    ])->withSession([
        'is_seller' => true,
        'buyer_account_id' => $account->id,
    ])->patch(route('buyer.account.update'), [
        'first_name' => 'Intruder',
    ])->assertForbidden();

    expect($account->fresh()->first_name)->toBe('Original');
});

test('invalid profile uploads do not modify the account', function (): void {
    $account = sariProfileAccount(
        'buyer',
        'invalid-photo@example.test'
    );

    $this->withSession([
        'is_buyer' => true,
        'buyer_account_id' => $account->id,
    ])->patch(route('buyer.account.update'), [
        'first_name' => 'Changed',
        'last_name' => 'Account',
        'contact_no' => '09123456789',
        'street_address' => 'Original Street',
        'profile_image' => UploadedFile::fake()->create(
            'photo.txt',
            1,
            'text/plain'
        ),
    ])->assertSessionHasErrors('profile_image');

    expect($account->fresh()->first_name)->toBe('Original');
    expect($account->fresh()->profile_image_path)->toBeNull();
});