<?php

use App\Events\SellerOrderUpdated;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Delivery\DeliveryRouteService;
use App\Services\Delivery\PhilippineAddressGeocoder;
use App\Support\LogisticsServiceArea;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->originalStoragePath = storage_path();
    $this->hubTestStorage = sys_get_temp_dir().'/sari-hubs-test-'.bin2hex(random_bytes(8));
    File::makeDirectory($this->hubTestStorage, 0700, true);
    $this->app->useStoragePath($this->hubTestStorage);
    config(['hashing.bcrypt.rounds' => 4]);
    Http::preventStrayRequests();
    Event::fake([SellerOrderUpdated::class]);
    $this->hubs = json_decode(file_get_contents(database_path('data/sari_city_hubs_demo/hubs.json')), true)['hubs'];
    $hash = Hash::make('test-only-fixture-password');
    foreach ($this->hubs as $index => $hub) {
        $common = ['first_name' => 'City', 'last_name' => 'Demo', 'sex' => 'N/A', 'email' => $hub['provider_email'], 'contact_no' => '00000000000', 'birthday' => '2000-01-01', 'age' => 26, 'province_code' => 'TEST', 'province_name' => $hub['province_name'], 'municipality_code' => 'TEST', 'municipality_name' => $hub['municipality_name'], 'barangay_code' => 'TEST', 'barangay_name' => $hub['first_barangay'], 'street_address' => 'Synthetic test address', 'password' => $hash, 'approved_at' => now(), 'created_at' => now(), 'updated_at' => now()];
        $appId = DB::table('registration_applications')->insertGetId($common + ['role' => 'logistics', 'status' => 'approved', 'admin_note' => 'SARI_CITY149_DEMO_V1:'.$hub['city_code']]);
        $providerId = DB::table('logistics_accounts')->insertGetId($common + ['registration_application_id' => $appId, 'account_status' => 'active', 'business_name' => 'City QA Logistics']);
        $riderFields = array_replace($common, ['email' => $hub['first_rider_email'], 'first_name' => 'Rider']);
        $riderAppId = DB::table('registration_applications')->insertGetId($riderFields + ['role' => 'rider', 'status' => 'approved', 'logistics_account_id' => $providerId, 'admin_note' => 'SARI_CITY149_DEMO_V1:'.$hub['city_code']]);
        DB::table('courier_accounts')->insert($riderFields + ['registration_application_id' => $riderAppId, 'logistics_account_id' => $providerId, 'account_status' => 'active', 'availability_status' => 'offline', 'activated_at' => now(), 'vehicle_type' => 'Motorcycle', 'plate_number' => 'QA-'.($index + 1), 'duty_province_name' => $hub['province_name'], 'duty_municipality_name' => $hub['municipality_name'], 'duty_barangay_name' => $hub['first_barangay']]);
    }
});
afterEach(function () {
    $this->app->useStoragePath($this->originalStoragePath);
    File::deleteDirectory($this->hubTestStorage);
});

function sariCityHubExisting(array $hub, string $code = 'CUSTOM-CITY', bool $main = false, ?int $providerId = null): LogisticsFacility
{
    $providerId ??= LogisticsAccount::where('email', $hub['provider_email'])->value('id');

    return LogisticsFacility::create(['logistics_account_id' => $providerId, 'code' => $code, 'name' => 'Preserved physical city hub', 'address' => 'Preserved custom address', 'province_name' => $hub['province_name'], 'municipality_name' => $hub['municipality_name'], 'latitude' => $hub['latitude'], 'longitude' => $hub['longitude'], 'is_active' => true, 'is_main_branch' => $main]);
}

it('validates 149 provider and rider links without creating facilities or reports', function () {
    $this->artisan('sari:seed-city-logistics-hubs', ['--dry-run' => true])->assertExitCode(0);
    expect(LogisticsFacility::count())->toBe(0)->and(is_dir(storage_path('app/private/sari-city-hubs-demo')))->toBeFalse();
    Http::assertNothingSent();
});

it('creates exactly one active city Main Branch per provider while preserving rider identity and offline state', function () {
    $providerHash = LogisticsAccount::first()->password;
    $riderHash = CourierAccount::first()->password;
    $this->artisan('sari:seed-city-logistics-hubs')->assertExitCode(0);
    expect(LogisticsFacility::count())->toBe(149)->and(LogisticsFacility::where('is_main_branch', true)->count())->toBe(149)
        ->and(LogisticsFacility::where('is_active', true)->count())->toBe(149)
        ->and(LogisticsAccount::count())->toBe(149)->and(CourierAccount::count())->toBe(149)
        ->and(CourierAccount::where('availability_status', 'offline')->count())->toBe(149)
        ->and(LogisticsAccount::first()->password)->toBe($providerHash)->and(CourierAccount::first()->password)->toBe($riderHash)
        ->and(DB::table('marketplace_orders')->count())->toBe(0)->and(DB::table('finance_wallet_entries')->count())->toBe(0);
    $directory = storage_path('app/private/sari-city-hubs-demo');
    expect(file($directory.'/hub-provider-rider-mapping.csv'))->toHaveCount(150)
        ->and(file($directory.'/first-10-flow-readiness.csv'))->toHaveCount(11);
    $facility = LogisticsFacility::first();
    $facility->update(['name' => 'Custom edited hub', 'latitude' => $facility->latitude + 0.001]);
    $latitude = $facility->fresh()->latitude;
    $this->artisan('sari:seed-city-logistics-hubs')->assertExitCode(0);
    expect(LogisticsFacility::count())->toBe(149)->and($facility->fresh()->name)->toBe('Custom edited hub')->and($facility->fresh()->latitude)->toBe($latitude);
});

it('reuses a valid existing city hub and only adds the missing Main Branch flag with a backup', function () {
    $facility = sariCityHubExisting($this->hubs[0]);
    $facility->update(['name' => '=2+2']);
    $before = $facility->only(['code', 'name', 'address', 'latitude', 'longitude']);
    $this->artisan('sari:seed-city-logistics-hubs')->assertExitCode(0);
    expect(LogisticsFacility::count())->toBe(149)->and($facility->fresh()->only(array_keys($before)))->toBe($before)
        ->and($facility->fresh()->is_main_branch)->toBeTrue();
    expect(glob(storage_path('app/private/sari-city-hubs-demo/main-flags-before-*.json')))->toHaveCount(1);
    expect(file_get_contents(storage_path('app/private/sari-city-hubs-demo/hub-provider-rider-mapping.csv')))->toContain("'=2+2");
});

it('preserves an existing Main Branch in another city and creates the registered-city hub as a regular branch', function () {
    $hub = $this->hubs[0];
    $hub['municipality_name'] = 'Other Test Municipality';
    $main = sariCityHubExisting($hub, 'EXISTING-MAIN', true);
    $this->artisan('sari:seed-city-logistics-hubs')->assertExitCode(0);
    $branch = LogisticsFacility::where('code', $this->hubs[0]['code'])->firstOrFail();
    expect(LogisticsFacility::count())->toBe(150)->and($main->fresh()->is_main_branch)->toBeTrue()
        ->and($branch->is_main_branch)->toBeFalse()->and($branch->logistics_account_id)->toBe($main->logistics_account_id);
});

it('refuses a foreign reserved hub code instead of adopting another providers facility', function () {
    $otherId = LogisticsAccount::where('email', $this->hubs[1]['provider_email'])->value('id');
    $foreign = sariCityHubExisting($this->hubs[0], $this->hubs[0]['code'], false, $otherId);
    $this->artisan('sari:seed-city-logistics-hubs')->assertExitCode(1);
    expect(LogisticsFacility::count())->toBe(1)->and($foreign->fresh()->logistics_account_id)->toBe($otherId)->and($foreign->fresh()->is_main_branch)->toBeFalse();
});

it('does not reactivate existing inactive hubs or reassign an unapproved rider', function () {
    $facility = sariCityHubExisting($this->hubs[0]);
    $facility->update(['is_active' => false]);
    $this->artisan('sari:seed-city-logistics-hubs')->assertExitCode(1);
    expect($facility->fresh()->is_active)->toBeFalse()->and(LogisticsFacility::count())->toBe(1);
    $facility->delete();
    $rider = CourierAccount::where('email', $this->hubs[0]['first_rider_email'])->firstOrFail();
    DB::table('registration_applications')->where('id', $rider->registration_application_id)->update(['status' => 'rejected']);
    $this->artisan('sari:seed-city-logistics-hubs')->assertExitCode(1);
    expect(LogisticsFacility::count())->toBe(0)->and($rider->fresh()->logistics_account_id)->toBe($rider->logistics_account_id);
});

it('rolls back all new facilities when a later insert fails without exposing SQL details', function () {
    $code = $this->hubs[10]['code'];
    DB::unprepared("CREATE TRIGGER stop_city_hub BEFORE INSERT ON logistics_facilities WHEN NEW.code = '".$code."' BEGIN SELECT RAISE(ABORT, 'PRIVATE_HUB_FAILURE'); END;");
    $exit = Artisan::call('sari:seed-city-logistics-hubs');
    expect($exit)->toBe(1)->and(Artisan::output())->not->toContain('PRIVATE_HUB_FAILURE')->and(LogisticsFacility::count())->toBe(0);
});

it('connects the first ten buyer seller routes to the correct same-provider city hub and rider duty area', function () {
    $this->artisan('sari:seed-city-logistics-hubs')->assertExitCode(0);
    $geocoder = Mockery::mock(PhilippineAddressGeocoder::class)->makePartial();
    $map = [];
    foreach (array_slice($this->hubs, 0, 10) as $i => $hub) {
        $map[sprintf('%03d', $i + 1)] = $hub;
    }
    $point = function ($order, bool $buyer) use ($map) {
        $email = $buyer ? $order->buyer->email : $order->seller->email;
        preg_match('/\.(\d{3})@/', $email, $m);
        $hub = $map[$m[1]];

        return ['latitude' => $hub['latitude'] + ($buyer ? 0.001 : -0.001), 'longitude' => $hub['longitude'], 'resolved_query' => 'Synthetic test endpoint'];
    };
    $geocoder->shouldReceive('seller')->andReturnUsing(fn ($o) => $point($o, false));
    $geocoder->shouldReceive('buyer')->andReturnUsing(fn ($o) => $point($o, true));
    $service = new DeliveryRouteService($geocoder);
    foreach (array_slice($this->hubs, 0, 10) as $index => $hub) {
        $slot = sprintf('%03d', $index + 1);
        $address = 'QA Street, '.$hub['first_barangay'].', '.$hub['municipality_name'].', '.$hub['province_name'].', Philippines';
        $seller = SellerAccount::create(['email' => 'seller.flow.'.$slot.'@example.test', 'store_name' => 'City Flow Shop', 'store_status' => 'open', 'account_status' => 'active', 'municipality_name' => $hub['municipality_name'], 'province_name' => $hub['province_name'], 'barangay_name' => $hub['first_barangay'], 'pickup_address' => $address]);
        $buyer = BuyerAccount::create(['first_name' => 'QA', 'last_name' => 'Buyer', 'email' => 'buyer.demo.'.$slot.'@example.test', 'sex' => 'Other', 'contact_no' => '00000000000', 'birthday' => '2000-01-01', 'age' => 26, 'province_code' => 'TEST', 'province_name' => $hub['province_name'], 'municipality_code' => 'TEST', 'municipality_name' => $hub['municipality_name'], 'barangay_code' => 'TEST', 'barangay_name' => $hub['first_barangay'], 'street_address' => 'QA Street', 'password' => 'test-only', 'account_status' => 'active']);
        $order = MarketplaceOrder::create(['order_number' => 'QA-HUB-FLOW-'.$slot, 'seller_account_id' => $seller->id, 'buyer_account_id' => $buyer->id, 'buyer_name' => 'QA Buyer', 'buyer_address' => $address, 'pickup_address' => $address, 'payment_method' => 'COD', 'payment_status' => 'pending', 'subtotal' => 100, 'delivery_fee' => 50, 'total' => 150, 'status' => 'ready_for_pickup']);
        $stops = $service->ensureStops($order);
        $facility = LogisticsFacility::where('code', $hub['code'])->firstOrFail();
        expect($stops)->toHaveCount(4)
            ->and($order->fresh()->logistics_account_id)->toBe($facility->logistics_account_id)
            ->and($stops->firstWhere('stop_key', 'origin_facility')->logistics_facility_id)->toBe($facility->id)
            ->and($stops->firstWhere('stop_key', 'destination_facility')->logistics_facility_id)->toBe($facility->id);
        $rider = CourierAccount::where('email', $hub['first_rider_email'])->firstOrFail();
        expect(LogisticsServiceArea::matchesPickupRider($rider, $address))->toBeTrue()
            ->and(LogisticsServiceArea::matchesRider($rider, (object) ['destination_municipality' => $hub['municipality_name'], 'destination_province' => $hub['province_name'], 'barangay_name' => LogisticsServiceArea::fromAddress($address, $hub['municipality_name'])]))->toBeTrue()
            ->and($rider->logistics_account_id)->toBe($facility->logistics_account_id);
    }
    Http::assertNothingSent();
});

it('allows the existing manager to select their new city operating branch', function () {
    $this->artisan('sari:seed-city-logistics-hubs')->assertExitCode(0);
    $provider = LogisticsAccount::where('email', $this->hubs[0]['provider_email'])->firstOrFail();
    $facility = LogisticsFacility::where('code', $this->hubs[0]['code'])->firstOrFail();
    $this->withSession(['is_logistics' => true, 'logistics_role' => 'manager', 'logistics_account_id' => $provider->id, 'logistics_email' => $provider->email])
        ->get(route('logistics.branch.select'))->assertOk()->assertSee($facility->name);
    $this->post(route('logistics.branch.store'), ['facility_id' => $facility->id])->assertRedirect(route('logistics.dashboard'))->assertSessionHas('logistics_operating_facility_id', $facility->id);
});

it('requires an explicit demo override outside local or testing', function () {
    $this->app['env'] = 'production';
    $this->artisan('sari:seed-city-logistics-hubs')->assertExitCode(1);
    $this->artisan('sari:seed-city-logistics-hubs', ['--dry-run' => true, '--allow-nonlocal' => true])->assertExitCode(0);
    expect(LogisticsFacility::count())->toBe(0);
});

it('preserves a comma inside a numbered barangay without allowing partial or wrong-city rider matches', function () {
    $hub = $this->hubs[1];
    $rider = CourierAccount::where('email', $hub['first_rider_email'])->firstOrFail();
    $full = 'Test Street, Bgy. No. 42, Apaya, City of Laoag, Ilocos Norte, Philippines';
    expect(LogisticsServiceArea::fromAddress($full, 'City of Laoag'))->toBe('No. 42, Apaya')
        ->and(LogisticsServiceArea::matchesPickupRider($rider, $full))->toBeTrue()
        ->and(LogisticsServiceArea::matchesPickupRider($rider, 'Test Street, Apaya, City of Laoag, Ilocos Norte, Philippines'))->toBeFalse()
        ->and(LogisticsServiceArea::matchesPickupRider($rider, 'Test Street, Bgy. No. 42, Apaya, City of Batac, Ilocos Norte, Philippines'))->toBeFalse()
        ->and(LogisticsServiceArea::fromAddress('Street, Brgy. Aglipay, City of Batac, Ilocos Norte', 'City of Batac'))->toBe('Aglipay');
});
