<?php

use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\BuyerAccount;
use App\Models\Platform\AdminUserActivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();
});

function activityBuyer(string $email = 'activity-buyer@example.test'): BuyerAccount
{
    return BuyerAccount::query()->create([
        'last_name' => 'Buyer',
        'first_name' => 'Activity',
        'sex' => 'Other',
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
        'password' => Hash::make('secret123'),
        'account_status' => 'active',
        'approved_at' => now(),
    ]);
}

test('buyer login and logout are recorded without storing the password', function (): void {
    $buyer = activityBuyer();

    $this->post(route('login.submit'), [
        'email' => $buyer->email,
        'password' => 'secret123',
    ])->assertRedirect(route('buyer.dashboard'));

    $login = AdminUserActivity::query()
        ->where('user_role', 'buyer')
        ->where('user_id', $buyer->id)
        ->where('action', 'login')
        ->latest('id')
        ->first();

    expect($login)->not->toBeNull()
        ->and($login->description)->toContain('Signed in');

    $serializedLogin = json_encode([
        'description' => $login->description,
        'metadata' => $login->metadata,
    ]);

    expect($serializedLogin)->not->toContain('secret123');

    $this->withSession([
        'is_buyer' => true,
        'buyer_account_id' => $buyer->id,
        'buyer_email' => $buyer->email,
        'buyer_name' => 'Activity Buyer',
    ])->post(route('buyer.logout'))
        ->assertRedirect(route('login'));

    expect(
        AdminUserActivity::query()
            ->where('user_role', 'buyer')
            ->where('user_id', $buyer->id)
            ->where('action', 'logout')
            ->exists()
    )->toBeTrue();
});

test('authenticated buyer state-changing actions are added to the audit trail', function (): void {
    $buyer = activityBuyer('buyer-action@example.test');

    $this->withSession([
        'is_buyer' => true,
        'buyer_account_id' => $buyer->id,
        'buyer_email' => $buyer->email,
        'buyer_name' => 'Activity Buyer',
    ])->patch(route('buyer.account.update'), [
        'first_name' => 'Updated',
        'last_name' => 'Buyer',
        'contact_no' => '09123456789',
        'street_address' => 'Updated Street',
    ])->assertRedirect();

    $activity = AdminUserActivity::query()
        ->where('user_role', 'buyer')
        ->where('user_id', $buyer->id)
        ->where('action', 'buyer_account_update')
        ->latest('id')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->metadata['route_name'] ?? null)->toBe('buyer.account.update')
        ->and($activity->metadata['method'] ?? null)->toBe('PATCH')
        ->and($activity->metadata['source'] ?? null)->toBe('user');
});

test('separate user activity page shows the centralized user activity log', function (): void {
    $admin = AdminAccount::query()->create([
        'name' => 'Audit Admin',
        'email' => 'audit-admin@example.test',
        'password' => Hash::make('admin-secret'),
        'role' => 'super_admin',
        'can_manage_platform_settings' => true,
    ]);

    $buyer = activityBuyer('visible-buyer@example.test');

    AdminUserActivity::query()->create([
        'admin_account_id' => null,
        'user_role' => 'buyer',
        'user_id' => $buyer->id,
        'action' => 'login',
        'description' => 'Signed in to SARI.',
        'metadata' => [
            'source' => 'user',
            'ip' => '127.0.0.1',
            'route_name' => 'login.submit',
        ],
    ]);

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->get(route('admin.user-activity'))
        ->assertOk()
        ->assertSee('User Activity Log')
        ->assertSee('visible-buyer@example.test')
        ->assertSee('Signed in')
        ->assertSee('127.0.0.1');
});

test('non-admin sessions cannot open the user activity viewer', function (): void {
    $buyer = activityBuyer('blocked-buyer@example.test');

    $this->withSession([
        'is_buyer' => true,
        'buyer_account_id' => $buyer->id,
    ])->get(route('admin.user-activity'))
        ->assertForbidden();
});
