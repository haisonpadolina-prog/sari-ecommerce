<?php

use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\SellerAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();
});

function monitoringUiAdmin(): AdminAccount
{
    return AdminAccount::query()->create([
        'name' => 'Monitoring UI Admin',
        'email' => 'ui-monitoring-admin@example.test',
        'password' => Hash::make('secret123'),
        'role' => 'super_admin',
        'can_manage_platform_settings' => true,
    ]);
}

function monitoringUiStore(int $index): SellerAccount
{
    return SellerAccount::query()->create([
        'email' => sprintf('monitoring-ui-seller-%02d@example.test', $index),
        'store_name' => sprintf('Monitoring UI Store %02d', $index),
        'first_name' => 'Store',
        'last_name' => 'Owner',
        'contact_no' => '09123456789',
        'province_name' => 'Cavite',
        'municipality_name' => 'Imus',
        'barangay_name' => 'Anabu',
        'street_address' => 'Monitoring UI Street',
        'password' => Hash::make('secret123'),
        'registration_status' => 'approved',
        'account_status' => 'active',
        'approved_at' => now(),
    ]);
}

test('monitoring shows ten records per page and no standalone Rider tab', function (): void {
    $admin = monitoringUiAdmin();
    for ($i = 1; $i <= 12; $i++) monitoringUiStore($i);

    $this->withSession(['is_admin' => true, 'admin_account_id' => $admin->id])
        ->get(route('admin.monitoring', ['section' => 'sellers']))
        ->assertOk()
        ->assertSee('Showing 1–10 of 12 results')
        ->assertSee('data-pm-all', false)
        ->assertSee('data-pm-check', false)
        ->assertSee('Export PDF')
        ->assertDontSee('section=riders', false);

    $this->withSession(['is_admin' => true, 'admin_account_id' => $admin->id])
        ->get(route('admin.monitoring', ['section' => 'sellers', 'page' => 2]))
        ->assertOk()
        ->assertSee('Showing 11–12 of 12 results');
});

test('admin PDF export contains only matching monitoring records', function (): void {
    $admin = monitoringUiAdmin();
    monitoringUiStore(1);
    monitoringUiStore(2);

    $response = $this->withSession(['is_admin' => true, 'admin_account_id' => $admin->id])
        ->get(route('admin.monitoring.export-pdf', ['section' => 'sellers', 'q' => 'Store 01']));

    $response->assertOk()->assertHeader('Content-Type', 'application/pdf');
    $pdf = $response->getContent();
    expect($pdf)->toStartWith('%PDF-1.4');
    expect($pdf)->toContain('Monitoring UI Store 01');
    expect($pdf)->not->toContain('Monitoring UI Store 02');
});

test('PDF export is admin only and Rider exports are not available', function (): void {
    $this->get(route('admin.monitoring.export-pdf', ['section' => 'sellers']))
        ->assertRedirect(route('login'));
    $admin = monitoringUiAdmin();

    $this->withSession(['is_admin' => true, 'admin_account_id' => $admin->id])
        ->get(route('admin.monitoring.export-pdf', ['section' => 'riders']))
        ->assertNotFound();
});

test('Rider section URL does not expose a separate Admin Rider directory', function (): void {
    $admin = monitoringUiAdmin();
    monitoringUiStore(1);

    $this->withSession(['is_admin' => true, 'admin_account_id' => $admin->id])
        ->get(route('admin.monitoring', ['section' => 'riders']))
        ->assertOk()
        ->assertSee('Stores &amp; Sellers', false)
        ->assertDontSee('View rider', false);
});
