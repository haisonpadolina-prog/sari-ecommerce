<?php

use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();
});

function pdfNoRidersIdentity(string $email): array
{
    return [
        'first_name' => 'PDF', 'last_name' => 'Tester',
        'sex' => 'Other', 'email' => $email, 'contact_no' => '09171234567',
        'birthday' => '2000-01-01', 'age' => 26,
        'province_code' => '043400000', 'province_name' => 'Laguna',
        'municipality_code' => '043426000', 'municipality_name' => 'Santa Cruz',
        'barangay_code' => '043426001', 'barangay_name' => 'Bubukal',
        'street_address' => '123 PDF Test Street',
        'password' => Hash::make('PDFTest@2026'),
    ];
}

it('lists only Buyer, Seller and Logistics, excluding Riders from the admin roster and metrics', function (): void {
    $logistics = LogisticsAccount::query()->create(pdfNoRidersIdentity('logistics-pdf-test@example.test'));
    $rider = CourierAccount::query()->create(array_merge(pdfNoRidersIdentity('rider-pdf-test@example.test'), [
        'logistics_account_id' => $logistics->id,
        'vehicle_type' => 'Motorcycle',
        'plate_number' => 'TEST-123',
        'account_status' => 'active',
    ]));

    $this->withSession(['is_admin' => true])->get(route('admin.users'))
        ->assertOk()
        ->assertViewHas('accounts', fn ($accounts) => $accounts->every(fn ($account) => $account['role'] !== 'Rider'))
        ->assertViewHas('stats', fn ($stats) => $stats['total'] === 1 && $stats['logistics'] === 1)
        ->assertDontSee('Rider accounts')
        ->assertDontSee('rider-pdf-test@example.test');

    // Manually accessing rider management URLs cannot bypass the UI exclusion.
    $this->withSession(['is_admin' => true])
        ->post(route('admin.users.suspend', ['rider', $rider->id]), ['reason' => 'Test rider access blocked'])
        ->assertNotFound();
    expect($rider->fresh()->account_status)->toBe('active');
});

it('downloads a genuine PDF of only the requested authorized account rows', function (): void {
    $buyer = BuyerAccount::query()->create(pdfNoRidersIdentity('buyer-pdf-test@example.test'));
    $logistics = LogisticsAccount::query()->create(pdfNoRidersIdentity('logistics-pdf-test@example.test'));
    $rider = CourierAccount::query()->create(array_merge(pdfNoRidersIdentity('rider-secret-pdf-test@example.test'), [
        'logistics_account_id' => $logistics->id,
        'vehicle_type' => 'Motorcycle',
        'plate_number' => 'TEST-456',
        'account_status' => 'active',
    ]));

    $url = route('admin.users.export-pdf');
    $res = $this->withSession(['is_admin' => true])->post($url, [
        'records_json' => json_encode(['buyer:'.$buyer->id, 'logistics:'.$logistics->id]),
    ]);

    $res->assertOk()
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertHeader('Cache-Control', 'private, no-store, max-age=0');
    expect($res->headers->get('Content-Disposition'))->toContain('.pdf');
    expect(substr($res->getContent(), 0, 8))->toBe('%PDF-1.4');
    expect($res->getContent())->toContain('buyer-pdf-test@example.test')
        ->toContain('logistics-pdf-test@example.test')
        ->not->toContain('rider-secret-pdf-test@example.test');

    $this->withSession(['is_admin' => true])->withHeader('Accept', 'application/json')->post($url, [
        'records_json' => json_encode(['rider:'.$rider->id]),
    ])->assertUnprocessable();
});
