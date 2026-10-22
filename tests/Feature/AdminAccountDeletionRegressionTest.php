<?php

use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Registration\RegistrationApplication;
use App\Services\Registration\AccountRegistrationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();
});

function deletionRegressionAdmin(): AdminAccount
{
    return AdminAccount::query()->create([
        'name' => 'Deletion Regression Admin',
        'email' => 'deletion-admin@example.test',
        'password' => Hash::make('Admin@2026'),
        'role' => 'super_admin',
        'can_manage_platform_settings' => true,
    ]);
}

function deletionRegressionApplication(string $email, string $role = 'buyer', string $password = 'OldPass@2026'): RegistrationApplication
{
    return RegistrationApplication::query()->create([
        'role' => $role,
        'last_name' => 'Deleted',
        'first_name' => 'Test',
        'middle_initial' => null,
        'sex' => 'Other',
        'email' => $email,
        'contact_no' => '09171234567',
        'birthday' => '2000-01-01',
        'age' => 26,
        'province_code' => 'PH-40',
        'province_name' => 'Cavite',
        'municipality_code' => 'IMUS',
        'municipality_name' => 'Imus',
        'barangay_code' => 'TEST',
        'barangay_name' => 'Anabu',
        'street_address' => 'Deletion Test Street',
        'password' => Hash::make($password),
        'status' => 'approved',
        'approved_at' => now(),
    ]);
}

function deletionRegressionBuyer(RegistrationApplication $application, string $password = 'OldPass@2026'): BuyerAccount
{
    return BuyerAccount::query()->create([
        'registration_application_id' => $application->id,
        'last_name' => $application->last_name,
        'first_name' => $application->first_name,
        'middle_initial' => $application->middle_initial,
        'sex' => $application->sex,
        'email' => $application->email,
        'contact_no' => $application->contact_no,
        'birthday' => $application->birthday,
        'age' => $application->age,
        'province_code' => $application->province_code,
        'province_name' => $application->province_name,
        'municipality_code' => $application->municipality_code,
        'municipality_name' => $application->municipality_name,
        'barangay_code' => $application->barangay_code,
        'barangay_name' => $application->barangay_name,
        'street_address' => $application->street_address,
        'password' => Hash::make($password),
        'account_status' => 'active',
        'approved_at' => now(),
    ]);
}

function deletionRegressionSeller(string $email = 'deleted-seller@example.test'): SellerAccount
{
    return SellerAccount::query()->create([
        'email' => $email,
        'store_name' => 'Deletion Regression Store',
        'first_name' => 'Seller',
        'last_name' => 'Deleted',
        'contact_no' => '09171234568',
        'province_name' => 'Cavite',
        'municipality_name' => 'Imus',
        'barangay_name' => 'Anabu',
        'street_address' => 'Seller Test Street',
        'password' => Hash::make('SellerOld@2026'),
        'registration_status' => 'approved',
        'account_status' => 'active',
        'approved_at' => now(),
    ]);
}

function deletionRegressionLogistics(string $email = 'logistics@gmail.com'): LogisticsAccount
{
    return LogisticsAccount::query()->create([
        'last_name' => 'Deleted',
        'first_name' => 'Logistics',
        'sex' => 'Other',
        'email' => $email,
        'contact_no' => '09171234569',
        'birthday' => '1990-01-01',
        'age' => 36,
        'province_code' => 'PH-40',
        'province_name' => 'Cavite',
        'municipality_code' => 'IMUS',
        'municipality_name' => 'Imus',
        'barangay_code' => 'TEST',
        'barangay_name' => 'Anabu',
        'street_address' => 'Logistics Test Street',
        'business_name' => 'Deleted Logistics Test',
        'password' => Hash::make('logistics123'),
        'account_status' => 'active',
        'approved_at' => now(),
    ]);
}

test('admin deletion removes a buyer from monitoring and blocks the deleted credentials while releasing the email', function (): void {
    $admin = deletionRegressionAdmin();
    $originalEmail = 'buyer@gmail.com';
    $oldPassword = 'buyer123';
    $application = deletionRegressionApplication($originalEmail, 'buyer', $oldPassword);
    $buyer = deletionRegressionBuyer($application, $oldPassword);
    $buyerId = $buyer->id;

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->delete(route('admin.users.destroy', ['role' => 'buyer', 'id' => $buyerId]))
        ->assertStatus(302);

    expect(BuyerAccount::query()->find($buyerId))->toBeNull();

    $deletedBuyer = BuyerAccount::withTrashed()->findOrFail($buyerId);
    expect($deletedBuyer->deleted_at)->not->toBeNull()
        ->and(strtolower($deletedBuyer->email))->not->toBe($originalEmail)
        ->and(Hash::check($oldPassword, $deletedBuyer->password))->toBeFalse();

    $application->refresh();
    expect($application->status)->toBe('rejected')
        ->and(Hash::check($oldPassword, $application->password))->toBeFalse()
        ->and(strtolower((string) $application->admin_note))->toContain('deleted or released');

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->get(route('admin.monitoring', [
        'section' => 'buyers',
        'q' => $originalEmail,
    ]))->assertOk()->assertViewHas('records', function ($records) use ($buyerId): bool {
        return !collect($records->items())->contains(
            fn ($record) => (int) $record->id === $buyerId && $record->role_key === 'buyer'
        );
    });

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->get(route('admin.monitoring.buyers.show', [
        'role' => 'buyer',
        'id' => $buyerId,
    ]))->assertNotFound();

    $this->post(route('login.submit'), [
        'email' => $originalEmail,
        'password' => $oldPassword,
    ])->assertSessionHasErrors('email');

    expect(BuyerAccount::query()->whereRaw('LOWER(email) = ?', [$originalEmail])->exists())->toBeFalse();
    expect(app(AccountRegistrationPolicy::class)->emailInUse($originalEmail))->toBeFalse();
});

test('a logged in marketplace session is invalidated on the next request after its account is deleted', function (): void {
    $application = deletionRegressionApplication('stale-session@example.test');
    $buyer = deletionRegressionBuyer($application);

    $buyer->delete();

    $this->withSession([
        'is_buyer' => true,
        'buyer_account_id' => $buyer->id,
        'buyer_email' => 'stale-session@example.test',
        'buyer_name' => 'Test Deleted',
    ])->get(route('buyer.dashboard'))
        ->assertRedirect(route('login'))
        ->assertSessionMissing('is_buyer')
        ->assertSessionMissing('buyer_account_id');
});

test('seller deletion uses soft deletes and old seller credentials cannot sign in', function (): void {
    $admin = deletionRegressionAdmin();
    $seller = deletionRegressionSeller();
    $sellerId = $seller->id;
    $originalEmail = $seller->email;

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->delete(route('admin.users.destroy', ['role' => 'seller', 'id' => $sellerId]))
        ->assertStatus(302);

    expect(SellerAccount::query()->find($sellerId))->toBeNull();
    $deletedSeller = SellerAccount::withTrashed()->findOrFail($sellerId);
    expect($deletedSeller->deleted_at)->not->toBeNull()
        ->and(strtolower($deletedSeller->email))->not->toBe(strtolower($originalEmail));

    $this->post(route('login.submit'), [
        'email' => $originalEmail,
        'password' => 'SellerOld@2026',
    ])->assertSessionHasErrors('email');
});

test('deleting the old development logistics account does not let rider registration recreate it', function (): void {
    $admin = deletionRegressionAdmin();
    $logistics = deletionRegressionLogistics();
    $id = $logistics->id;

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->delete(route('admin.users.destroy', ['role' => 'logistics', 'id' => $id]))
        ->assertStatus(302);

    expect(LogisticsAccount::query()->where('email', 'logistics@gmail.com')->exists())->toBeFalse();

    $this->get(route('rider.logistics.index'))->assertOk();

    expect(LogisticsAccount::query()->where('email', 'logistics@gmail.com')->exists())->toBeFalse();
});
