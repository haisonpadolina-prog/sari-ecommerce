<?php

use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\BuyerAccount;
use App\Models\Platform\AdminUserActivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();

    config([
        'session.driver' => 'array',
        'session.encrypt' => false,
        'session.table' => 'sessions',
        'session.lifetime' => 120,
    ]);

    app('session')->forgetDrivers();
});

afterEach(function (): void {
    config([
        'session.driver' => 'array',
        'session.encrypt' => false,
        'session.table' => 'sessions',
        'session.lifetime' => 120,
    ]);

    app('session')->forgetDrivers();
});

function securityCenterAdmin(string $email = 'security-admin@example.test'): AdminAccount
{
    return AdminAccount::query()->create([
        'name' => 'Security Admin',
        'email' => $email,
        'password' => Hash::make('admin-secret'),
        'role' => 'super_admin',
        'can_manage_platform_settings' => true,
    ]);
}

function securityCenterBuyer(string $email = 'security-buyer@example.test'): BuyerAccount
{
    return BuyerAccount::query()->create([
        'last_name' => 'Security',
        'first_name' => 'Buyer',
        'sex' => 'Other',
        'email' => $email,
        'contact_no' => '09123456789',
        'birthday' => '2000-01-01',
        'age' => 26,
        'province_code' => 'PH-40',
        'province_name' => 'Cavite',
        'municipality_code' => 'IMUS',
        'municipality_name' => 'Imus',
        'barangay_code' => 'TEST',
        'barangay_name' => 'Anabu',
        'street_address' => 'Security Test Street',
        'password' => Hash::make('correct-password'),
        'account_status' => 'active',
        'approved_at' => now(),
    ]);
}

function seedCriticalFailedLoginBurst(string $email, int $attempts = 8): void
{
    $email = strtolower($email);

    foreach (range(1, $attempts) as $index) {
        AdminUserActivity::query()->create([
            'admin_account_id' => null,
            'user_role' => 'security',
            'user_id' => 0,
            'action' => 'failed_login',
            'description' => 'Unsuccessful sign-in attempt recorded.',
            'metadata' => [
                'email_hash' => hash('sha256', $email),
                'email_masked' => 'se******@example.test',
                'ip' => '127.0.0.1',
                'user_agent' => 'Test Browser',
                'source' => 'security_monitor',
                'attempt' => $index,
            ],
            'created_at' => now()->subSeconds($attempts - $index),
            'updated_at' => now()->subSeconds($attempts - $index),
        ]);
    }
}

test('non admin cannot open security center', function (): void {
    $this->get(route('admin.security'))
        ->assertRedirect(route('login'));
});

test('admin can open security center and existing audit trail is reused', function (): void {
    $admin = securityCenterAdmin();
    $buyer = securityCenterBuyer();

    AdminUserActivity::query()->create([
        'admin_account_id' => $admin->id,
        'user_role' => 'buyer',
        'user_id' => $buyer->id,
        'action' => 'blocked',
        'description' => 'Security review required.',
        'metadata' => [
            'reason' => 'Security review required.',
            'before' => ['status' => 'active'],
            'after' => ['status' => 'blocked'],
            'ip' => '127.0.0.1',
        ],
    ]);

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->get(route('admin.security'))
        ->assertOk()
        ->assertSee('Security Center')
        ->assertSee('Smart risk &amp; fraud flags', false)
        ->assertSee('Active account sessions')
        ->assertSee('Audit timeline')
        ->assertSee('Security review required.')
        ->assertSee('Before')
        ->assertSee('After');
});

test('failed password login is recorded without storing the submitted password', function (): void {
    $buyer = securityCenterBuyer('failed-login@example.test');

    $response = $this->post(route('login.submit'), [
        'email' => $buyer->email,
        'password' => 'definitely-wrong-password',
    ]);

    $response->assertRedirect();
    $response->assertSessionMissing('is_buyer');

    $event = AdminUserActivity::query()
        ->where('action', 'failed_login')
        ->latest('id')
        ->first();

    expect($event)->not->toBeNull();
    expect($event->user_role)->toBe('buyer');
    expect($event->user_id)->toBe($buyer->id);
    expect($event->metadata['email_hash'] ?? null)->toBe(hash('sha256', strtolower($buyer->email)));
    expect(json_encode($event->metadata))->not->toContain('definitely-wrong-password');
});

test('eight recent failed sign ins create a critical alert that admin can acknowledge', function (): void {
    $admin = securityCenterAdmin();
    seedCriticalFailedLoginBurst('security-target@example.test', 8);

    $response = $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->getJson(route('admin.security.alert-feed'))
        ->assertOk()
        ->assertJsonPath('count', 1)
        ->assertJsonPath('alerts.0.severity', 'critical')
        ->assertJsonPath('alerts.0.title', 'Repeated failed sign-in attempts');

    $fingerprint = (string) $response->json('alerts.0.fingerprint');
    expect($fingerprint)->not->toBe('');

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->postJson(route('admin.security.alerts.acknowledge'), [
        'fingerprint' => $fingerprint,
    ])->assertOk()->assertJson(['ok' => true]);

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->getJson(route('admin.security.alert-feed'))
        ->assertOk()
        ->assertJsonPath('count', 0);

    expect(AdminUserActivity::query()->where('action', 'security_alert_acknowledged')->exists())->toBeTrue();
});

test('admin can force logout another database session and action is audited', function (): void {
    config([
        'session.driver' => 'database',
        'session.encrypt' => false,
        'session.table' => 'sessions',
        'session.lifetime' => 120,
    ]);

    app('session')->forgetDrivers();

    $admin = securityCenterAdmin();
    $buyer = securityCenterBuyer('remote-session@example.test');
    $targetSessionId = str_repeat('a', 40);

    DB::table('sessions')->insert([
        'id' => $targetSessionId,
        'user_id' => null,
        'ip_address' => '192.0.2.15',
        'user_agent' => 'Mozilla/5.0 Windows Chrome/155',
        'payload' => base64_encode(serialize([
            '_token' => 'test-token',
            'is_buyer' => true,
            'buyer_account_id' => $buyer->id,
            'buyer_email' => $buyer->email,
        ])),
        'last_activity' => now()->timestamp,
    ]);

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->post(route('admin.security.sessions.force-logout', hash_hmac('sha256', $targetSessionId, (string) config('app.key'))))
        ->assertRedirect();

    expect(DB::table('sessions')->where('id', $targetSessionId)->exists())->toBeFalse();

    $audit = AdminUserActivity::query()->where('action', 'force_logout')->latest('id')->first();
    expect($audit)->not->toBeNull();
    expect($audit->user_role)->toBe('buyer');
    expect($audit->user_id)->toBe($buyer->id);
    expect($audit->metadata['before']['session'] ?? null)->toBe('active');
    expect($audit->metadata['after']['session'] ?? null)->toBe('terminated');
});


test('account status actions add before and after state to the existing audit record', function (): void {
    $admin = securityCenterAdmin('snapshot-admin@example.test');
    $buyer = securityCenterBuyer('snapshot-buyer@example.test');

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->post(route('admin.users.block', ['role' => 'buyer', 'id' => $buyer->id]), [
        'reason' => 'Security review required.',
    ])->assertRedirect();

    $event = AdminUserActivity::query()
        ->where('user_role', 'buyer')
        ->where('user_id', $buyer->id)
        ->where('action', 'blocked')
        ->latest('id')
        ->first();

    expect($event)->not->toBeNull();
    expect($event->metadata['before']['status'] ?? null)->toBe('active');
    expect($event->metadata['after']['status'] ?? null)->toBe('blocked');
    expect($event->metadata['reason'] ?? null)->toBe('Security review required.');
});
