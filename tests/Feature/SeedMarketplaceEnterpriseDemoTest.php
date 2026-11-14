<?php

use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Finance\FinanceWallet;
use App\Models\Finance\FinanceWalletEntry;
use App\Services\Finance\WalletService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->originalStoragePath = storage_path();
    $this->demoTestStorage = sys_get_temp_dir().'/sari-enterprise-test-'.bin2hex(random_bytes(8));
    File::makeDirectory($this->demoTestStorage, 0700, true);
    $this->app->useStoragePath($this->demoTestStorage);
    Storage::fake('public');
    config(['hashing.bcrypt.rounds' => 4]);
});
afterEach(function () {
    $this->app->useStoragePath($this->originalStoragePath);
    File::deleteDirectory($this->demoTestStorage);
});

function sariEnterpriseLogistics(): LogisticsAccount
{
    return LogisticsAccount::create(['last_name' => 'Demo', 'first_name' => 'Logistics', 'sex' => 'N/A', 'email' => 'enterprise-logistics@example.test', 'contact_no' => '00000000000', 'birthday' => '2000-01-01', 'age' => 26, 'province_code' => 'TEST', 'province_name' => 'Test', 'municipality_code' => 'TEST', 'municipality_name' => 'Test', 'barangay_code' => 'TEST', 'barangay_name' => 'Test', 'street_address' => 'Test', 'password' => Hash::make('test-only'), 'account_status' => 'active']);
}

it('validates all 150 profiles without creating accounts credentials avatars or wallets', function () {
    $this->artisan('sari:seed-enterprise-demo', ['--dry-run' => true])->assertExitCode(0);
    expect(BuyerAccount::count())->toBe(0)->and(FinanceWallet::count())->toBe(0)
        ->and(file_exists(storage_path('app/private/sari-enterprise-demo')))->toBeFalse()
        ->and(Storage::disk('public')->allFiles())->toBe([]);
});

it('creates 150 Buyers with unique private credentials and profiles and credits each eligible wallet once', function () {
    $seller = SellerAccount::create(['email' => 'enterprise-seller@example.test', 'store_name' => 'Original Shop', 'account_status' => 'active']);
    $logistics = sariEnterpriseLogistics();
    $wallets = app(WalletService::class);
    $wallets->credit($wallets->wallet('seller', $seller->id), 200, 'existing_credit', 'existing-demo-test-credit');
    $this->artisan('sari:seed-enterprise-demo')->assertExitCode(0);
    expect(BuyerAccount::count())->toBe(150)->and(DB::table('registration_applications')->where('admin_note', 'SARI_ENTERPRISE_DEMO_V1')->count())->toBe(150);
    expect((float) $wallets->platformWallet()->available_balance)->toBe(100000000.0)
        ->and((float) $wallets->wallet('seller', $seller->id)->available_balance)->toBe(500200.0)
        ->and((float) $wallets->wallet('logistics', $logistics->id)->available_balance)->toBe(1000000.0)
        ->and(FinanceWallet::where('owner_type', 'buyer')->sum('available_balance'))->toEqual(0);
    $private = storage_path('app/private/sari-enterprise-demo');
    $plan = json_decode(file_get_contents($private.'/initial-credentials.json'), true)['passwords'];
    expect(array_unique($plan))->toHaveCount(150);
    $buyer = BuyerAccount::where('email', 'buyer.demo.001@example.test')->firstOrFail();
    expect(Hash::check($plan[$buyer->email], $buyer->password))->toBeTrue()->and($buyer->account_status)->toBe('active');
    expect(Storage::disk('public')->allFiles())->toHaveCount(150);
    $lines = file($private.'/initial-login-credentials.csv');
    expect($lines)->toHaveCount(151);
    $wallets->reserve($wallets->platformWallet(), 100, 'demo-reserve-test', 'Existing reservation');
    $buyer->update(['profile_image_path' => 'custom/avatar.png', 'password' => Hash::make('changed-test-password'), 'account_status' => 'blocked']);
    $this->artisan('sari:seed-enterprise-demo')->assertExitCode(0);
    expect(BuyerAccount::count())->toBe(150)
        ->and(FinanceWalletEntry::where('category', 'demo_seed_credit')->count())->toBe(3)
        ->and((float) $wallets->platformWallet()->available_balance)->toBe(99999900.0)
        ->and((float) $wallets->platformWallet()->reserved_balance)->toBe(100.0)
        ->and($buyer->fresh()->profile_image_path)->toBe('custom/avatar.png')
        ->and(Hash::check('changed-test-password', $buyer->fresh()->password))->toBeTrue()
        ->and($buyer->fresh()->account_status)->toBe('blocked')
        ->and($seller->fresh()->store_name)->toBe('Original Shop')
        ->and(DB::table('marketplace_orders')->count())->toBe(0)
        ->and(DB::table('finance_payout_requests')->count())->toBe(0);
    expect(FinanceWalletEntry::where('category', 'demo_seed_credit')->first()->metadata['demo'])->toBeTrue();
    unlink($private.'/initial-credentials.json');
    $this->artisan('sari:seed-enterprise-demo')->assertExitCode(1);
    expect(BuyerAccount::count())->toBe(150)
        ->and(FinanceWalletEntry::where('category', 'demo_seed_credit')->count())->toBe(3)
        ->and(Hash::check('changed-test-password', $buyer->fresh()->password))->toBeTrue();
});

it('stops on an unrelated email collision before creating anything', function () {
    SellerAccount::create(['email' => 'buyer.demo.001@example.test', 'store_name' => 'Preserve Me']);
    $this->artisan('sari:seed-enterprise-demo')->assertExitCode(1);
    expect(BuyerAccount::count())->toBe(0)->and(FinanceWallet::count())->toBe(0)
        ->and(file_exists(storage_path('app/private/sari-enterprise-demo')))->toBeFalse();
});

it('keeps bans intact and refuses to seed a banned identity', function () {
    DB::table('account_email_bans')->insert(['email' => 'buyer.demo.001@example.test', 'banned_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
    $this->artisan('sari:seed-enterprise-demo', ['--dry-run' => true])->assertExitCode(1);
    expect(BuyerAccount::count())->toBe(0)->and(DB::table('account_email_bans')->whereNull('unbanned_at')->count())->toBe(1);
});

it('rolls back all new Buyers and credits together if a credit fails', function () {
    sariEnterpriseLogistics();
    $actual = new WalletService;
    $mock = Mockery::mock(WalletService::class)->makePartial();
    $mock->shouldReceive('credit')->andReturnUsing(function (...$args) use ($actual) {
        if ($args[0]->owner_type === 'logistics') {
            throw new RuntimeException('Synthetic test credit failure.');
        }

        return $actual->credit(...$args);
    });
    $this->app->instance(WalletService::class, $mock);
    $this->artisan('sari:seed-enterprise-demo')->assertExitCode(1);
    expect(BuyerAccount::count())->toBe(0)->and(FinanceWallet::count())->toBe(0)
        ->and(FinanceWalletEntry::count())->toBe(0)
        ->and(DB::table('registration_applications')->where('admin_note', 'SARI_ENTERPRISE_DEMO_V1')->count())->toBe(0);
});

it('does not run outside local or testing without the explicit demo override', function () {
    $this->app['env'] = 'production';
    $this->artisan('sari:seed-enterprise-demo')->assertExitCode(1);
    expect(BuyerAccount::count())->toBe(0)->and(FinanceWallet::count())->toBe(0);
    $this->artisan('sari:seed-enterprise-demo', ['--dry-run' => true, '--allow-nonlocal' => true])->assertExitCode(0);
});

it('does not expose SQL bindings or passwords when a database credit fails', function () {
    $mock = Mockery::mock(WalletService::class)->makePartial();
    $mock->shouldReceive('credit')->andThrow(new QueryException('sqlite', 'PRIVATE_CREDENTIAL_SENTINEL', [], new PDOException('PRIVATE_CREDENTIAL_SENTINEL')));
    $this->app->instance(WalletService::class, $mock);
    $exit = Artisan::call('sari:seed-enterprise-demo');
    expect($exit)->toBe(1)
        ->and(Artisan::output())->not->toContain('PRIVATE_CREDENTIAL_SENTINEL')
        ->and(BuyerAccount::count())->toBe(0)
        ->and(FinanceWalletEntry::count())->toBe(0);
});
