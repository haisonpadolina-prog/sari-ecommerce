<?php

use App\Models\Accounts\BuyerAccount;
use App\Models\Registration\RegistrationApplication;
use App\Services\Registration\RegistrationEmailVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();
    Storage::fake('public');
    Storage::fake('local');
    $this->mock(RegistrationEmailVerificationService::class, function ($mock): void {
        $mock->shouldReceive('sendCode')->andReturn(['ok' => true]);
        $mock->shouldReceive('hasActiveChallenge')->andReturn(true);
    });
});

function registrationProfileImageFixture(string $name): UploadedFile
{
    // Real PNG bytes exercise image validation without requiring GD generation.
    return UploadedFile::fake()->createWithContent($name, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADElEQVR4nGNwaDgAAAKEAYEml6crAAAAAElFTkSuQmCC'));
}

function profileUploadPayload(string $role): array
{
    $payload = [
        'role' => $role, 'first_name' => 'Maria', 'last_name' => 'Santos', 'sex' => 'Female',
        'email' => $role . '-photo@example.test', 'contact_no' => '09171234567',
        'birthday' => '1998-01-01', 'password' => 'Photo@2026', 'password_confirmation' => 'Photo@2026', 'terms' => '1',
        'region_code' => '020000000', 'region_name' => 'Cagayan Valley',
        'province_code' => '020900000', 'province_name' => 'Batanes',
        'municipality_code' => '020901000', 'municipality_name' => 'Basco',
        'barangay_code' => '020901002', 'barangay_name' => 'Ihubok I',
        'street_address' => 'Talangan', 'postal_code' => '3900',
        'business_name' => 'Santos Store', 'seller_type' => 'individual', 'line_of_business' => 'Books',
        'tin' => '123-456-789-000', 'sec_registration_no' => 'CS202612345',
        'position' => 'Manager', 'service_areas' => ['Batanes'],
        'profile_image_selected' => '1', 'profile_image' => registrationProfileImageFixture('profile.png'),
    ];
    if ($role !== 'buyer') {
        $payload['id_document'] = registrationProfileImageFixture('id.png');
    }
    if ($role === 'logistics') {
        foreach (['business_permit', 'bir_document', 'sec_document'] as $field) {
            $payload[$field] = registrationProfileImageFixture($field . '.png');
        }
    }

    return $payload;
}

it('persists uploaded registration photos and copies buyer photos into the account', function (string $role): void {
    $this->post(route('register.' . $role . '.submit'), profileUploadPayload($role))->assertSessionHasNoErrors();
    $application = RegistrationApplication::query()->where('email', $role . '-photo@example.test')->firstOrFail();
    expect($application->profile_image_path)->not->toBeEmpty();
    Storage::disk('public')->assertExists($application->profile_image_path);
    if ($role === 'buyer') {
        expect(BuyerAccount::query()->where('email', $application->email)->value('profile_image_path'))->toBe($application->profile_image_path);
    }
})->with(['buyer', 'seller', 'logistics']);

it('rejects a missing required photo for every shared registration role', function (string $role): void {
    $payload = profileUploadPayload($role);
    unset($payload['profile_image'], $payload['profile_image_selected']);
    $this->post(route('register.' . $role . '.submit'), $payload)->assertSessionHasErrors('profile_image');
    expect(RegistrationApplication::query()->where('email', $payload['email'])->exists())->toBeFalse();
})->with(['buyer', 'seller', 'logistics']);

it('rejects a non image without creating an application', function (): void {
    $payload = profileUploadPayload('seller');
    $payload['profile_image'] = UploadedFile::fake()->create('photo.txt', 10, 'text/plain');
    $this->post(route('register.seller.submit'), $payload)->assertSessionHasErrors('profile_image');
    expect(RegistrationApplication::query()->where('email', $payload['email'])->exists())->toBeFalse();
});

it('can attach a missing photo on an unverified retry with the original password', function (): void {
    $initial = profileUploadPayload('seller');
    $this->post(route('register.seller.submit'), $initial)->assertSessionHasNoErrors();
    RegistrationApplication::query()->where('email', $initial['email'])->update(['profile_image_path' => null]);
    $this->post(route('register.seller.submit'), profileUploadPayload('seller'))->assertSessionHasNoErrors();
    $application = RegistrationApplication::query()->where('email', $initial['email'])->firstOrFail();
    expect($application->profile_image_path)->not->toBeEmpty();
    Storage::disk('public')->assertExists($application->profile_image_path);
});

it('cannot attach a photo to an existing unverified registration with the wrong password', function (): void {
    $initial = profileUploadPayload('seller');
    $this->post(route('register.seller.submit'), $initial)->assertSessionHasNoErrors();
    RegistrationApplication::query()->where('email', $initial['email'])->update(['profile_image_path' => null]);
    $retry = profileUploadPayload('seller');
    $retry['password'] = $retry['password_confirmation'] = 'Different@2026';
    $this->post(route('register.seller.submit'), $retry)->assertSessionHasErrors('password');
    expect(RegistrationApplication::query()->where('email', $initial['email'])->value('profile_image_path'))->toBeNull();
});


it('rejects an oversized profile image without creating an application', function (): void {
    $payload = profileUploadPayload('seller');
    $payload['profile_image'] = registrationProfileImageFixture('large.png')->size(5121);
    $this->post(route('register.seller.submit'), $payload)->assertSessionHasErrors('profile_image');
    expect(RegistrationApplication::query()->where('email', $payload['email'])->exists())->toBeFalse();
});
