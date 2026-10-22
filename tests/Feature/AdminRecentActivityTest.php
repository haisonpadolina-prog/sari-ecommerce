<?php

use App\Models\Accounts\AdminAccount;
use App\Models\Platform\AdminUserActivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();
});

function recentActivityAdmin(string $email = 'recent-admin@example.test'): AdminAccount
{
    return AdminAccount::query()->create([
        'name' => 'Recent Admin',
        'email' => $email,
        'password' => Hash::make('admin-secret'),
        'role' => 'super_admin',
        'can_manage_platform_settings' => true,
    ]);
}

test('admin login and logout are recorded on the separate recent activity log', function (): void {
    $admin = recentActivityAdmin();

    $this->post(route('login.submit'), [
        'email' => $admin->email,
        'password' => 'admin-secret',
    ])->assertRedirect(route('admin.dashboard'));

    $login = AdminUserActivity::query()
        ->where('user_role', 'admin')
        ->where('user_id', $admin->id)
        ->where('action', 'login')
        ->latest('id')
        ->first();

    expect($login)->not->toBeNull()
        ->and($login->admin_account_id)->toBe($admin->id);

    $admin->refresh();
    expect((int) $admin->login_count)->toBe(1)
        ->and($admin->last_login_at)->not->toBeNull();

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->post(route('admin.logout'))
        ->assertRedirect(route('login'));

    expect(
        AdminUserActivity::query()
            ->where('user_role', 'admin')
            ->where('user_id', $admin->id)
            ->where('action', 'logout')
            ->exists()
    )->toBeTrue();
});

test('successful admin state-changing requests are recorded without request payloads', function (): void {
    $admin = recentActivityAdmin('action-admin@example.test');

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->patch(route('admin.account.profile.update'), [
        'name' => 'Updated Admin',
        'email' => 'updated-admin@example.test',
    ])->assertRedirect();

    $activity = AdminUserActivity::query()
        ->where('user_role', 'admin')
        ->where('user_id', $admin->id)
        ->where('action', 'admin_account_profile_update')
        ->latest('id')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->metadata['route_name'] ?? null)->toBe('admin.account.profile.update')
        ->and($activity->metadata['method'] ?? null)->toBe('PATCH')
        ->and(json_encode($activity->metadata))->not->toContain('updated-admin@example.test');
});

test('admin recent activity page only shows the current administrator records', function (): void {
    $admin = recentActivityAdmin('current-admin@example.test');
    $other = recentActivityAdmin('other-admin@example.test');

    AdminUserActivity::query()->create([
        'admin_account_id' => $admin->id,
        'user_role' => 'admin',
        'user_id' => $admin->id,
        'action' => 'login',
        'description' => 'Current administrator signed in.',
        'metadata' => ['ip' => '127.0.0.1'],
    ]);

    AdminUserActivity::query()->create([
        'admin_account_id' => $other->id,
        'user_role' => 'admin',
        'user_id' => $other->id,
        'action' => 'login',
        'description' => 'Other administrator secret event.',
        'metadata' => ['ip' => '10.0.0.2'],
    ]);

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->get(route('admin.recent-activity'))
        ->assertOk()
        ->assertSee('Admin Recent Activity')
        ->assertSee('Current administrator signed in.')
        ->assertDontSee('Other administrator secret event.');
});

test('account management links to both separated activity pages', function (): void {
    $admin = recentActivityAdmin('links-admin@example.test');

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->get(route('admin.account'))
        ->assertOk()
        ->assertSee(route('admin.user-activity'), false)
        ->assertSee(route('admin.recent-activity'), false);
});
