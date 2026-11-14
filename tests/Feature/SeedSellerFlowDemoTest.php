<?php

use App\Models\Accounts\SellerAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->originalStoragePath = storage_path();
    $this->demoTestStorage = sys_get_temp_dir().'/sari-seller-flow-test-'.bin2hex(random_bytes(8));
    File::makeDirectory($this->demoTestStorage, 0700, true);
    $this->app->useStoragePath($this->demoTestStorage);
    Storage::fake('public');
    config(['hashing.bcrypt.rounds' => 4]);
});
afterEach(function () {
    $this->app->useStoragePath($this->originalStoragePath);
    File::deleteDirectory($this->demoTestStorage);
});

it('validates 50 profiles and media without creating accounts or credentials', function () {
    $this->artisan('sari:seed-seller-flow-demo', ['--dry-run' => true])->assertExitCode(0);
    expect(SellerAccount::count())->toBe(0)
        ->and(DB::table('registration_applications')->count())->toBe(0)
        ->and(Storage::disk('public')->allFiles())->toBe([])
        ->and(is_dir(storage_path('app/private/sari-seller-flow-demo')))->toBeFalse();
});

it('creates 50 profiles and private individual passwords without altering existing sellers or finances', function () {
    $original = SellerAccount::create(['email' => 'qa.seller.001@sari-demo.test', 'first_name' => 'Original', 'store_name' => 'Original Shop', 'store_status' => 'paused', 'password' => Hash::make('original-password')]);
    $originalHash = $original->password;
    $this->artisan('sari:seed-seller-flow-demo')->assertExitCode(0);
    expect(SellerAccount::count())->toBe(51)
        ->and(DB::table('registration_applications')->where('admin_note', 'SARI_SELLER_FLOW50_V1')->count())->toBe(50)
        ->and($original->fresh()->password)->toBe($originalHash)
        ->and($original->fresh()->store_status)->toBe('paused');
    $private = storage_path('app/private/sari-seller-flow-demo');
    $plan = json_decode(file_get_contents($private.'/initial-credentials.json'), true)['passwords'];
    expect(array_unique($plan))->toHaveCount(50);
    $seller = SellerAccount::where('email', 'seller.flow.001@example.test')->firstOrFail();
    expect(Hash::check($plan[$seller->email], $seller->password))->toBeTrue()
        ->and($seller->account_status)->toBe('active')
        ->and($seller->registration_status)->toBe('approved')
        ->and($seller->store_status)->toBe('open')
        ->and($seller->municipality_name)->toBe('City of Batac')
        ->and($seller->pickup_address)->toContain($seller->barangay_name, $seller->municipality_name, 'fictional')
        ->and($seller->store_description)->toContain('SARI_SELLER_FLOW50_V1');
    foreach (['profile_image_path', 'store_logo_path', 'store_banner_path'] as $key) {
        Storage::disk('public')->assertExists($seller->{$key});
    }
    expect(Storage::disk('public')->allFiles())->toHaveCount(150);
    $file = fopen($private.'/initial-login-credentials.csv', 'r');
    $header = fgetcsv($file);
    $row = fgetcsv($file);
    fclose($file);
    $login = array_combine($header, $row);
    expect($login['initial_password'])->toBe($plan[$seller->email])
        ->and($login['store_name'])->toBe($seller->store_name)
        ->and(count(file($private.'/initial-login-credentials.csv')))->toBe(51)
        ->and(DB::table('seller_products')->count())->toBe(0)
        ->and(DB::table('marketplace_orders')->count())->toBe(0)
        ->and(DB::table('finance_wallets')->count())->toBe(0);
    $seller->update(['store_status' => 'paused', 'profile_image_path' => 'custom/profile.png', 'password' => Hash::make('changed-password')]);
    $this->artisan('sari:seed-seller-flow-demo')->assertExitCode(0);
    expect(SellerAccount::count())->toBe(51)
        ->and($seller->fresh()->profile_image_path)->toBe('custom/profile.png')
        ->and($seller->fresh()->store_status)->toBe('paused')
        ->and(Hash::check('changed-password', $seller->fresh()->password))->toBeTrue();
    unlink($private.'/initial-credentials.json');
    $this->artisan('sari:seed-seller-flow-demo')->assertExitCode(1);
    expect(SellerAccount::count())->toBe(51)->and(Hash::check('changed-password', $seller->fresh()->password))->toBeTrue();
});

it('authenticates a generated password through the existing seller login route', function () {
    $this->artisan('sari:seed-seller-flow-demo')->assertExitCode(0);
    $plan = json_decode(file_get_contents(storage_path('app/private/sari-seller-flow-demo/initial-credentials.json')), true)['passwords'];
    $email = 'seller.flow.001@example.test';
    $seller = SellerAccount::where('email', $email)->firstOrFail();
    $this->post('/login', ['email' => $email, 'password' => $plan[$email]])
        ->assertSessionHasNoErrors()->assertRedirect(route('seller.dashboard'))
        ->assertSessionHas('is_seller', true)->assertSessionHas('seller_account_id', $seller->id);
});

it('refuses unrelated seller collisions without altering existing accounts', function () {
    $original = SellerAccount::create(['email' => 'seller.flow.001@example.test', 'store_name' => 'Preserve This']);
    $this->artisan('sari:seed-seller-flow-demo')->assertExitCode(1);
    expect(SellerAccount::count())->toBe(1)->and($original->fresh()->store_name)->toBe('Preserve This')
        ->and(DB::table('registration_applications')->count())->toBe(0);
});

it('preserves bans and refuses banned demo emails', function () {
    DB::table('account_email_bans')->insert(['email' => 'seller.flow.001@example.test', 'banned_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
    $this->artisan('sari:seed-seller-flow-demo', ['--dry-run' => true])->assertExitCode(1);
    expect(SellerAccount::count())->toBe(0)->and(DB::table('account_email_bans')->whereNull('unbanned_at')->count())->toBe(1);
});

it('does not restore archived demo sellers on rerun', function () {
    $this->artisan('sari:seed-seller-flow-demo')->assertExitCode(0);
    $seller = SellerAccount::where('email', 'seller.flow.001@example.test')->firstOrFail();
    $seller->delete();
    $this->artisan('sari:seed-seller-flow-demo')->assertExitCode(1);
    expect(SellerAccount::count())->toBe(49)->and(SellerAccount::withTrashed()->count())->toBe(50);
});

it('rolls back registrations and sellers together when a later insert fails', function () {
    DB::unprepared("CREATE TRIGGER stop_flow_seller BEFORE INSERT ON seller_accounts WHEN NEW.email = 'seller.flow.025@example.test' BEGIN SELECT RAISE(ABORT, 'PRIVATE_SEED_SENTINEL'); END;");
    $exit = Artisan::call('sari:seed-seller-flow-demo');
    expect($exit)->toBe(1)
        ->and(Artisan::output())->not->toContain('PRIVATE_SEED_SENTINEL')
        ->and(SellerAccount::count())->toBe(0)
        ->and(DB::table('registration_applications')->count())->toBe(0);
});

it('requires an explicit demo override outside local and testing', function () {
    $this->app['env'] = 'production';
    $this->artisan('sari:seed-seller-flow-demo')->assertExitCode(1);
    $this->artisan('sari:seed-seller-flow-demo', ['--dry-run' => true, '--allow-nonlocal' => true])->assertExitCode(0);
    expect(SellerAccount::count())->toBe(0);
});
