<?php

use App\Models\Accounts\AccountEmailBan;
use App\Models\Accounts\BuyerAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();
    config(['mail.default' => 'array']);
});

function managedBuyer(string $email): BuyerAccount
{
    return BuyerAccount::query()->create([
        'last_name' => 'User',
        'first_name' => 'Managed',
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
        'street_address' => 'Test Street',
        'password' => Hash::make('secret123'),
        'account_status' => 'active',
        'approved_at' => now(),
    ]);
}

test('admin ban blocks login and registration email verification', function (): void {
    $buyer = managedBuyer('ban-me@example.test');

    $this->withSession(['is_admin' => true, 'admin_account_id' => 1])
        ->post(route('admin.users.ban', ['buyer', $buyer->id]), ['reason' => 'Fraud review required'])
        ->assertRedirect();

    expect($buyer->fresh()->account_status)->toBe('banned');
    expect(AccountEmailBan::query()->where('email', 'ban-me@example.test')->whereNull('unbanned_at')->exists())->toBeTrue();

    $this->post(route('login.submit'), [
        'email' => 'ban-me@example.test',
        'password' => 'secret123',
    ])->assertSessionHasErrors('email');

    $this->postJson(route('register.submit'), [
        '_registration_action' => 'otp_send',
        'email' => 'ban-me@example.test',
    ])->assertStatus(422)
        ->assertJsonValidationErrors('email');
});

test('admin can cancel a ban and release the email for registration', function (): void {
    $buyer = managedBuyer('restore-me@example.test');

    $this->withSession(['is_admin' => true, 'admin_account_id' => 1])
        ->post(route('admin.users.ban', ['buyer', $buyer->id]), ['reason' => 'Temporary investigation'])
        ->assertRedirect();

    $this->withSession(['is_admin' => true, 'admin_account_id' => 1])
        ->post(route('admin.users.unban', ['buyer', $buyer->id]))
        ->assertRedirect();

    expect(BuyerAccount::query()->find($buyer->id))->toBeNull();
    expect(AccountEmailBan::query()->where('email', 'restore-me@example.test')->whereNull('unbanned_at')->exists())->toBeFalse();

    $this->postJson(route('register.submit'), [
        '_registration_action' => 'otp_send',
        'email' => 'restore-me@example.test',
    ])->assertOk();
});

test('deleting an account releases its original email for registration', function (): void {
    $buyer = managedBuyer('reuse-me@example.test');

    $this->withSession(['is_admin' => true, 'admin_account_id' => 1])
        ->delete(route('admin.users.destroy', ['buyer', $buyer->id]))
        ->assertRedirect();

    expect(BuyerAccount::query()->find($buyer->id))->toBeNull();
    expect(BuyerAccount::withTrashed()->find($buyer->id))->not->toBeNull();
    expect(BuyerAccount::withTrashed()->find($buyer->id)->email)->not->toBe('reuse-me@example.test');
    expect(AccountEmailBan::query()->where('email', 'reuse-me@example.test')->whereNull('unbanned_at')->exists())->toBeFalse();

    $this->postJson(route('register.submit'), [
        '_registration_action' => 'otp_send',
        'email' => 'reuse-me@example.test',
    ])->assertOk();
});
