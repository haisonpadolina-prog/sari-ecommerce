<?php

use App\Models\Accounts\LogisticsAccount;
use App\Models\Registration\RegistrationApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();
    Storage::fake('local');
    Storage::fake('public');
    Mail::fake();
    config(['mail.default' => 'array']);
});

function publicRiderLogistics(array $overrides = []): LogisticsAccount
{
    return LogisticsAccount::query()->create(array_merge([
        'registration_application_id' => null,
        'last_name' => 'Provider',
        'first_name' => 'Bilis',
        'middle_initial' => null,
        'sex' => null,
        'email' => 'provider@example.test',
        'contact_no' => '09181112222',
        'birthday' => null,
        'age' => null,
        'region_code' => '130000000',
        'region_name' => 'National Capital Region',
        'province_code' => '137400000',
        'province_name' => 'Metro Manila',
        'municipality_code' => '137401000',
        'municipality_name' => 'Manila',
        'barangay_code' => '137401001',
        'barangay_name' => 'Barangay 1',
        'street_address' => '1 Logistics Avenue',
        'business_name' => 'Bilis Hatid Express Inc.',
        'password' => Hash::make('Provider@2026'),
        'account_status' => 'active',
        'approved_at' => now(),
    ], $overrides));
}

function publicRiderVerifiedSession(LogisticsAccount $logistics, string $email): array
{
    return [
        'registration_email_otp' => [
            'email' => strtolower($email),
            'purpose' => 'registration',
            'hash' => hash_hmac('sha256', '482913', (string) config('app.key')),
            'expires_at' => now()->addMinutes(5)->timestamp,
            'last_sent_at' => now()->timestamp,
            'attempts' => 0,
            'verified_at' => now()->timestamp,
        ],
        'rider_registration_verified_email' => strtolower($email),
        'rider_registration_verified_logistics_id' => $logistics->id,
    ];
}

function publicRiderPayload(string $email, string $mobile, string $license, string $plate): array
{
    return [
        'last_name' => 'Garcia',
        'first_name' => 'Pedro Jose',
        'middle_initial' => 'M',
        'sex' => 'Male',
        'email' => $email,
        'contact_no' => $mobile,
        'birthday' => now()->subYears(24)->toDateString(),
        'province_code' => '137400000',
        'province_name' => 'Metro Manila',
        'municipality_code' => '137401000',
        'municipality_name' => 'Manila',
        'barangay_code' => '137401001',
        'barangay_name' => 'Barangay 1',
        'street_address' => '25 Rider Street',
        'license_number' => $license,
        'license_expiry_date' => now()->addYear()->toDateString(),
        'profile_image' => UploadedFile::fake()->createWithContent('profile.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADElEQVR4nGNwaDgAAAKEAYEml6crAAAAAElFTkSuQmCC')),
        'vehicle_type' => 'Motorcycle',
        'plate_number' => $plate,
        'id_document' => UploadedFile::fake()->create('driver-license.jpg', 256, 'image/jpeg'),
        'orcr_document' => UploadedFile::fake()->create('orcr.jpg', 256, 'image/jpeg'),
        'password' => 'Rider@2026',
        'password_confirmation' => 'Rider@2026',
        'terms' => '1',
    ];
}

test('public rider chooses an active logistics provider before applying', function (): void {
    $logistics = publicRiderLogistics();

    $this->get(route('rider.logistics.index'))
        ->assertOk()
        ->assertSee('Bilis Hatid Express Inc.');

    $this->get(route('rider.logistics.apply', $logistics))
        ->assertOk();
});

test('public rider application accepts 09 mobile and stores canonical value', function (): void {
    $logistics = publicRiderLogistics();
    $email = 'rider09@example.test';

    $this->withSession(publicRiderVerifiedSession($logistics, $email))
        ->post(route('rider.logistics.submit', $logistics), publicRiderPayload(
            $email,
            '09171234567',
            'N01-23-456789',
            'ABC1234'
        ))
        ->assertRedirect(route('registration.pending'));

    $application = RegistrationApplication::query()->where('email', $email)->firstOrFail();
    expect($application->contact_no)->toBe('09171234567')
        ->and($application->logistics_account_id)->toBe($logistics->id)
        ->and($application->status)->toBe('pending');
});

test('public rider application accepts +63 mobile and normalizes it to 09', function (): void {
    $logistics = publicRiderLogistics();
    $email = 'rider63@example.test';

    $this->withSession(publicRiderVerifiedSession($logistics, $email))
        ->post(route('rider.logistics.submit', $logistics), publicRiderPayload(
            $email,
            '+639171234567',
            'N02-23-456789',
            'XYZ5678'
        ))
        ->assertRedirect(route('registration.pending'));

    expect(RegistrationApplication::query()->where('email', $email)->value('contact_no'))
        ->toBe('09171234567');
});

test('public rider application rejects invalid mobile length and characters', function (string $mobile): void {
    $logistics = publicRiderLogistics();
    $email = 'invalid-mobile@example.test';

    $this->withSession(publicRiderVerifiedSession($logistics, $email))
        ->post(route('rider.logistics.submit', $logistics), publicRiderPayload(
            $email,
            $mobile,
            'N03-23-456789',
            'MNO9012'
        ))
        ->assertSessionHasErrors('contact_no');
})->with([
    'letters' => '0917ABC4567',
    '10 digits' => '0917123456',
    '12 digits' => '091712345678',
    'missing plus prefix' => '639171234567',
]);


test('public rider application requires an actual profile photo', function (): void {
    $logistics = publicRiderLogistics();
    $email = 'rider-no-photo@example.test';
    $payload = publicRiderPayload($email, '09171234567', 'N01-26-123456', 'ABC 1234');
    unset($payload['profile_image']);

    $this->withSession(publicRiderVerifiedSession($logistics, $email))
        ->post(route('rider.logistics.submit', $logistics), $payload)
        ->assertSessionHasErrors('profile_image');

    expect(RegistrationApplication::query()->where('email', $email)->exists())->toBeFalse();
});

test('public rider application persists its required profile photo', function (): void {
    $logistics = publicRiderLogistics();
    $email = 'rider-photo@example.test';
    $payload = publicRiderPayload($email, '09171234567', 'N01-26-123456', 'ABC 1234');

    $this->withSession(publicRiderVerifiedSession($logistics, $email))
        ->post(route('rider.logistics.submit', $logistics), $payload)
        ->assertSessionHasNoErrors();

    $application = RegistrationApplication::query()->where('email', $email)->firstOrFail();
    expect($application->profile_image_path)->not->toBeEmpty();
    Storage::disk('public')->assertExists($application->profile_image_path);
});
