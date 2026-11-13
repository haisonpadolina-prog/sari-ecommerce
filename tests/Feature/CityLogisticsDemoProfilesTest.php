<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CityLogisticsDemoProfilesTest extends TestCase
{
    use RefreshDatabase;
    private string $originalStorage;
    private string $temporaryStorage;

    protected function setUp(): void
    {
        parent::setUp(); config(['hashing.bcrypt.rounds'=>4]);
        $this->originalStorage = storage_path();
        $this->temporaryStorage = sys_get_temp_dir().'/sari-profiles-'.bin2hex(random_bytes(5));
        mkdir($this->temporaryStorage,0700,true); $this->app->useStoragePath($this->temporaryStorage);
        Storage::fake('public');
    }

    protected function tearDown(): void
    {
        $this->app->useStoragePath($this->originalStorage);
        File::deleteDirectory($this->temporaryStorage); parent::tearDown();
    }

    public function test_png_library_has_one_unique_asset_per_managed_profile(): void
    {
        $profiles=json_decode(file_get_contents(database_path('data/sari_city_demo_profiles/profiles.json')),true)['profiles'];
        $this->assertCount(7599,$profiles);
        $this->assertCount(7599,array_unique(array_column($profiles,'email')));
        $this->assertCount(149,collect($profiles)->where('role','logistics')->pluck('business_name')->unique());
        $hashes=[];
        foreach($profiles as $profile){
            $file=database_path('data/sari_city_demo_profiles/avatars/'.$profile['asset']);
            $image=getimagesize($file);
            $this->assertSame([192,192,IMAGETYPE_PNG],array_slice($image,0,3));
            $hashes[]=hash_file('sha256',$file);
        }
        $this->assertCount(7599,array_unique($hashes));
        $names=collect($profiles)->where('role','rider')->map(fn($p)=>$p['first_name'].' '.$p['last_name'])->unique();
        $this->assertCount(7450,$names);
    }

    public function test_missing_seed_accounts_stop_without_profile_writes(): void
    {
        $this->artisan('sari:enrich-city-logistics-profiles')->assertFailed();
        $this->assertEmpty(Storage::disk('public')->allFiles());
        $this->assertDirectoryDoesNotExist(storage_path('app/private/sari-city-logistics-profile-backups'));
    }

    public function test_full_enrichment_preserves_logins_links_custom_photos_and_reruns(): void
    {
        $this->artisan('sari:seed-city-logistics')->assertSuccessful();
        $provider=DB::table('logistics_accounts')->orderBy('id')->first();
        $rider=DB::table('courier_accounts')->orderBy('id')->first();
        $other=DB::table('courier_accounts')->where('id','<>',$rider->id)->orderBy('id')->first();
        // Use a known manifest asset for a previously uploaded custom portrait.
        $profiles=json_decode(file_get_contents(database_path('data/sari_city_demo_profiles/profiles.json')),true)['profiles'];
        $sample=file_get_contents(database_path('data/sari_city_demo_profiles/avatars/'.$profiles[0]['asset']));
        Storage::disk('public')->put('account-profiles/custom/portrait.png',$sample);
        DB::table('courier_accounts')->where('id',$rider->id)->update(['first_name'=>'Custom','last_name'=>'Account','profile_image_path'=>'account-profiles/custom/portrait.png','availability_status'=>'online']);
        $this->artisan('sari:enrich-city-logistics-profiles',['--dry-run'=>true])->assertSuccessful();
        $this->assertNull(DB::table('courier_accounts')->where('id',$other->id)->value('profile_image_path'));
        $this->artisan('sari:enrich-city-logistics-profiles')->assertSuccessful();
        $updatedProvider=DB::table('logistics_accounts')->where('id',$provider->id)->first();
        $updated=DB::table('courier_accounts')->where('id',$other->id)->first();
        $custom=DB::table('courier_accounts')->where('id',$rider->id)->first();
        $this->assertStringStartsWith('demo-city149-profiles-v1/',$updated->profile_image_path);
        Storage::disk('public')->assertExists($updated->profile_image_path);
        Storage::disk('public')->assertExists($updatedProvider->profile_image_path);
        $this->assertNotSame('Rider',$updated->first_name);
        $this->assertNotSame('2000-01-01',$updated->birthday);
        $this->assertNotSame('Demo motorcycle',$updated->vehicle_model);
        $this->assertSame($other->password,$updated->password);
        $this->assertSame($other->logistics_account_id,$updated->logistics_account_id);
        $this->assertSame($other->duty_barangay_name,$updated->duty_barangay_name);
        $this->assertSame('Custom',$custom->first_name);
        $this->assertSame('account-profiles/custom/portrait.png',$custom->profile_image_path);
        $this->assertSame('online',$custom->availability_status);
        $this->assertSame($provider->password,$updatedProvider->password);
        $this->assertSame(7599,DB::table('registration_applications')->whereNotNull('profile_image_path')->count()+1);
        $this->assertDatabaseCount('marketplace_orders',0);
        $backup=glob(storage_path('app/private/sari-city-logistics-profile-backups/*.json'))[0];
        $this->assertStringNotContainsString('password',file_get_contents($backup));
        $path=$updated->profile_image_path; $name=$updated->first_name;
        $this->artisan('sari:enrich-city-logistics-profiles')->assertSuccessful();
        $this->assertSame($path,DB::table('courier_accounts')->where('id',$other->id)->value('profile_image_path'));
        $this->assertSame($name,DB::table('courier_accounts')->where('id',$other->id)->value('first_name'));
        $this->assertDatabaseCount('courier_accounts',7450);
        \Laravel\Sanctum\Sanctum::actingAs(\App\Models\Accounts\CourierAccount::find($other->id),['mobile:rider']);
        $this->getJson('/api/v1/rider/profile')->assertOk()->assertJsonPath('data.first_name',$name)->assertJsonPath('data.vehicle_model',$updated->vehicle_model);
    }

    public function test_profile_command_has_nonlocal_guard(): void
    {
        $this->app->detectEnvironment(fn()=> 'production');
        $this->artisan('sari:enrich-city-logistics-profiles')->assertFailed();
        $this->assertDatabaseCount('logistics_accounts',0);
    }
}
