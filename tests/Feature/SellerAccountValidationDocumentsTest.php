<?php

use App\Models\Accounts\SellerAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();
    Storage::fake('public');
    Storage::fake('local');
});

function validatedSeller(array $overrides = []): SellerAccount
{
    $seller = new SellerAccount();

    $seller->forceFill(array_merge([
        'last_name' => 'Seller',
        'first_name' => 'Sari',
        'middle_initial' => 'A',
        'sex' => 'Male',
        'email' => 'seller-validation@example.test',
        'contact_no' => '09123456789',
        'birthday' => '2000-05-12',
        'age' => 26,
        'province_name' => 'Cavite',
        'municipality_name' => 'General Trias',
        'barangay_name' => 'San Francisco',
        'street_address' => '123 Sample Street',
        'password' => Hash::make('secret123'),
        'store_name' => 'SARI Validation Store',
        'line_of_business' => 'General Merchandise',
        'id_path' => 'registration-documents/seller/id/original-id.pdf',
        'business_permit_path' =>
            'registration-documents/seller/permit/original-permit.pdf',
        'registration_status' => 'approved',
        'account_status' => 'active',
        'approved_at' => now(),    ], $overrides));

    $seller->save();

    $seller = $seller->fresh();

    expect($seller->getRawOriginal('birthday'))
        ->not->toBeNull()
        ->and($seller->id_path)
        ->not->toBeNull()
        ->and($seller->business_permit_path)
        ->not->toBeNull();

    return $seller;
}

function validatedSellerSession(SellerAccount $seller): array
{
    return [
        'is_seller' => true,
        'seller_account_id' => $seller->id,
    ];
}

function validAccountPayload(SellerAccount $seller): array
{
    return [
        'first_name' => $seller->first_name,
        'last_name' => $seller->last_name,
        'middle_initial' => $seller->middle_initial,
        'sex' => $seller->sex,
        'contact_no' => $seller->contact_no,
        'birthday' => (string) (
            $seller->getRawOriginal('birthday')
            ?: $seller->getAttribute('birthday')
        ),
        'street_address' => $seller->street_address,
        'barangay_name' => $seller->barangay_name,
        'municipality_name' => $seller->municipality_name,
        'province_name' => $seller->province_name,
    ];
}

function testPngBytes(
    int $width = 600,
    int $height = 600
): string {
    $chunk = static function (
        string $type,
        string $data
    ): string {
        return pack('N', strlen($data))
            .$type
            .$data
            .pack('N', crc32($type.$data));
    };

    $signature = "\x89PNG\r\n\x1a\n";

    $ihdr = pack(
        'NNCCCCC',
        $width,
        $height,
        8,
        2,
        0,
        0,
        0
    );

    $pixel = "\xD5\x96\x17";
    $row = "\x00".str_repeat(
        $pixel,
        $width
    );

    $raw = str_repeat(
        $row,
        $height
    );

    return $signature
        .$chunk('IHDR', $ihdr)
        .$chunk('IDAT', gzcompress($raw, 9))
        .$chunk('IEND', '');
}

test('name fields reject numbers and enforce text limits', function (): void {
    $seller = validatedSeller();

    $payload = validAccountPayload($seller);
    $payload['first_name'] = 'Sari123';
    $payload['last_name'] = str_repeat('A', 61);
    $payload['middle_initial'] = '9';

    $this->withSession(validatedSellerSession($seller))
        ->patch(route('seller.account.update'), $payload)
        ->assertSessionHasErrors([
            'first_name',
            'last_name',
            'middle_initial',
        ]);
});

test('contact number accepts digits only and exactly eleven digits starting with 09', function (): void {
    $seller = validatedSeller();

    foreach ([
        '09ABC456789',
        '0912345678',
        '08123456789',
        '091234567890',
    ] as $badContact) {
        $payload = validAccountPayload($seller);
        $payload['contact_no'] = $badContact;

        $this->withSession(validatedSellerSession($seller))
            ->patch(route('seller.account.update'), $payload)
            ->assertSessionHasErrors('contact_no');
    }

    $payload = validAccountPayload($seller);
    $payload['contact_no'] = '09987654321';

    $this->withSession(validatedSellerSession($seller))
        ->patch(route('seller.account.update'), $payload)
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect($seller->fresh()->contact_no)
        ->toBe('09987654321');
});

test('seller can update account address fields', function (): void {
    $seller = validatedSeller();

    $payload = validAccountPayload($seller);
    $payload['street_address'] = '45 Enterprise Avenue, Phase 2';
    $payload['barangay_name'] = 'Barangay 123';
    $payload['municipality_name'] = 'General Trias';
    $payload['province_name'] = 'Cavite';

    $this->withSession(validatedSellerSession($seller))
        ->patch(route('seller.account.update'), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    $seller->refresh();

    expect($seller->street_address)
        ->toBe('45 Enterprise Avenue, Phase 2')
        ->and($seller->barangay_name)
        ->toBe('Barangay 123')
        ->and($seller->municipality_name)
        ->toBe('General Trias')
        ->and($seller->province_name)
        ->toBe('Cavite');
});

test('seller profile image is stored and persisted', function (): void {
    $seller = validatedSeller();

    $payload = validAccountPayload($seller);
    $payload['profile_image'] =
        UploadedFile::fake()->createWithContent(
            'profile.png',
            testPngBytes(600, 600)
        );

    $this->withSession(validatedSellerSession($seller))
        ->patch(route('seller.account.update'), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    $seller->refresh();

    expect($seller->profile_image_path)
        ->not->toBeNull()
        ->and($seller->profile_image_path)
        ->toStartWith(
            'account-profiles/seller_accounts/'
        );

    Storage::disk('public')->assertExists(
        $seller->profile_image_path
    );
});

test('seller can replace verification documents without deleting original registration evidence', function (): void {
    $seller = validatedSeller();

    Storage::disk('local')->put(
        $seller->id_path,
        'original-id'
    );

    Storage::disk('local')->put(
        $seller->business_permit_path,
        'original-permit'
    );

    $payload = validAccountPayload($seller);
    $payload['government_id'] =
        UploadedFile::fake()->create(
            'replacement-id.pdf',
            256,
            'application/pdf'
        );

    $payload['business_permit'] =
        UploadedFile::fake()->create(
            'replacement-permit.pdf',
            384,
            'application/pdf'
        );

    $this->withSession(validatedSellerSession($seller))
        ->patch(route('seller.account.update'), $payload)
        ->assertRedirect()
        ->assertSessionHas('success');

    $seller->refresh();

    expect($seller->id_path)
        ->toStartWith(
            'account-documents/seller_accounts/'
        )
        ->and($seller->business_permit_path)
        ->toStartWith(
            'account-documents/seller_accounts/'
        );

    Storage::disk('local')->assertExists(
        $seller->id_path
    );

    Storage::disk('local')->assertExists(
        $seller->business_permit_path
    );

    Storage::disk('local')->assertExists(
        'registration-documents/seller/id/original-id.pdf'
    );

    Storage::disk('local')->assertExists(
        'registration-documents/seller/permit/original-permit.pdf'
    );
});
