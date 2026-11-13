<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CityLogisticsDemoTest extends TestCase
{
    use RefreshDatabase;
    private string $temporaryStorage;
    private string $originalStorage;

    protected function setUp(): void
    {
        parent::setUp();
        config(['hashing.bcrypt.rounds' => 4]);
        $this->originalStorage = storage_path();
        $this->temporaryStorage = sys_get_temp_dir().'/sari-city-logistics-'.bin2hex(random_bytes(5));
        mkdir($this->temporaryStorage, 0700, true);
        $this->app->useStoragePath($this->temporaryStorage);
    }

    protected function tearDown(): void
    {
        $this->app->useStoragePath($this->originalStorage);
        File::deleteDirectory($this->temporaryStorage);
        parent::tearDown();
    }

    public function test_official_dataset_has_149_cities_in_18_regions_with_valid_barangays(): void
    {
        $data = json_decode(file_get_contents(database_path('data/sari_city_logistics_demo.json')), true);
        $this->assertCount(149, $data['cities']);
        $this->assertCount(18, array_unique(array_column($data['cities'], 'region_code')));
        $this->assertCount(149, array_unique(array_column($data['cities'], 'psgc_code')));
        foreach ($data['cities'] as $city) {
            $this->assertNotEmpty($city['barangays']);
            $this->assertCount(min(50, $city['barangay_count']), $city['barangays']);
            $this->assertMatchesRegularExpression('/^\d{10}$/', $city['psgc_code']);
        }
    }

    public function test_dry_run_does_not_create_accounts_or_credentials(): void
    {
        $this->artisan('sari:seed-city-logistics', ['--dry-run' => true])->assertSuccessful();
        $this->assertDatabaseCount('logistics_accounts', 0);
        $this->assertDatabaseCount('courier_accounts', 0);
        $this->assertDirectoryDoesNotExist(storage_path('app/private/sari-city-logistics-demo'));
    }

    public function test_full_seed_links_exactly_50_riders_and_rerun_preserves_passwords_and_status(): void
    {
        $this->artisan('sari:seed-city-logistics')->assertSuccessful();
        $this->assertDatabaseCount('logistics_accounts', 149);
        $this->assertDatabaseCount('courier_accounts', 7450);
        $this->assertDatabaseCount('registration_applications', 7599);
        $counts = DB::table('courier_accounts')->select('logistics_account_id')->selectRaw('COUNT(*) AS total')->groupBy('logistics_account_id')->get();
        $this->assertCount(149, $counts);
        foreach ($counts as $row) $this->assertSame(50, (int) $row->total);
        $this->assertSame(7450, DB::table('courier_accounts')->where('availability_status', 'offline')->count());
        $this->assertDatabaseCount('marketplace_orders', 0);
        $this->assertDatabaseCount('logistics_facilities', 0);
        $provider = DB::table('logistics_accounts')->orderBy('id')->first();
        $rider = DB::table('courier_accounts')->where('logistics_account_id', $provider->id)->first();
        $this->assertSame($provider->municipality_name, $rider->duty_municipality_name);
        $planFile = glob(storage_path('app/private/sari-city-logistics-demo/*.json'))[0];
        $plan = json_decode(file_get_contents($planFile), true);
        $firstProvider = DB::table('logistics_accounts')->where('email', 'logistics.city-'.$plan['city_code'].'@example.test')->first();
        $this->assertTrue(Hash::check($plan['initial_password'], $firstProvider->password));
        $firstRider = DB::table('courier_accounts')->where('logistics_account_id', $firstProvider->id)->first();
        $this->assertTrue(Hash::check($plan['initial_password'], $firstRider->password));
        $this->post('/login', ['email' => $firstProvider->email, 'password' => $plan['initial_password']])
            ->assertRedirect(route('logistics.dashboard'))->assertSessionHas('logistics_account_id', $firstProvider->id);
        $this->post('/login', ['email' => $firstRider->email, 'password' => $plan['initial_password']])
            ->assertRedirect(route('courier.dashboard'))->assertSessionHas('courier_account_id', $firstRider->id);
        $this->postJson('/api/v1/auth/login', ['role' => 'logistics', 'email' => $firstProvider->email,
            'password' => $plan['initial_password'], 'device_name' => 'Demo QA'])->assertOk()->assertJsonPath('data.user.role', 'logistics');
        $this->postJson('/api/v1/auth/login', ['role' => 'rider', 'email' => $firstRider->email,
            'password' => $plan['initial_password'], 'device_name' => 'Demo QA'])->assertOk()->assertJsonPath('data.user.role', 'rider');

        $csv = storage_path('app/private/sari-city-logistics-demo/initial-login-credentials.csv');
        $this->assertFileExists($csv);
        $file = fopen($csv, 'r'); $csvRows = 0;
        while (fgetcsv($file) !== false) $csvRows++;
        fclose($file); $this->assertSame(7600, $csvRows);
        $changedHash = Hash::make('ManuallyChangedDemoPassword!123');
        DB::table('courier_accounts')->where('id', $firstRider->id)->update(['password' => $changedHash, 'availability_status' => 'online']);
        $this->artisan('sari:seed-city-logistics')->assertSuccessful();
        $this->assertDatabaseCount('courier_accounts', 7450);
        $this->assertDatabaseCount('logistics_accounts', 149);
        $this->assertSame($changedHash, DB::table('courier_accounts')->where('id', $firstRider->id)->value('password'));
        $this->assertSame('online', DB::table('courier_accounts')->where('id', $firstRider->id)->value('availability_status'));
        unlink($planFile);
        $this->artisan('sari:seed-city-logistics')->assertFailed();
        $this->assertSame($firstProvider->password, DB::table('logistics_accounts')->where('id', $firstProvider->id)->value('password'));
    }

    public function test_existing_unrelated_email_collision_stops_before_any_seed_writes(): void
    {
        $data = json_decode(file_get_contents(database_path('data/sari_city_logistics_demo.json')), true);
        DB::table('seller_accounts')->insert(['email' => 'logistics.city-'.$data['cities'][0]['psgc_code'].'@example.test', 'store_name' => 'Existing store', 'created_at' => now(), 'updated_at' => now()]);
        $this->artisan('sari:seed-city-logistics')->assertFailed();
        $this->assertDatabaseCount('logistics_accounts', 0);
        $this->assertDatabaseCount('courier_accounts', 0);
        $this->assertDatabaseCount('seller_accounts', 1);
    }

    public function test_city_failure_rolls_back_provider_and_registrations(): void
    {
        DB::unprepared("CREATE TRIGGER reject_demo_rider BEFORE INSERT ON courier_accounts BEGIN SELECT RAISE(ABORT, 'test rollback'); END;");
        $this->artisan('sari:seed-city-logistics')->assertFailed();
        $this->assertDatabaseCount('logistics_accounts', 0);
        $this->assertDatabaseCount('courier_accounts', 0);
        $this->assertDatabaseCount('registration_applications', 0);
        DB::unprepared('DROP TRIGGER reject_demo_rider');
    }

    public function test_nonlocal_environment_requires_explicit_option(): void
    {
        $this->app->detectEnvironment(fn () => 'production');
        $this->artisan('sari:seed-city-logistics')->assertFailed();
        $this->artisan('sari:seed-city-logistics', ['--allow-nonlocal' => true, '--dry-run' => true])->assertSuccessful();
        $this->assertDatabaseCount('logistics_accounts', 0);
    }
}
