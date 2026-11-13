<?php

namespace Tests\Feature;

use App\Console\Commands\DispatchMobilePush;
use App\Http\Controllers\Api\V1\MobileUpdatesController;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Services\Mobile\MobilePushAccess;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use ReflectionMethod;
use Tests\TestCase;

/** Uses only a dedicated in-memory SQLite connection, never the application's real DB. */
class MobileBackendPatchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mobile_patch_test',
            'database.connections.mobile_patch_test' => [
                'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
                'foreign_key_constraints' => true,
            ],
            'app.key' => 'base64:'.base64_encode(str_repeat('m', 32)),
            'cache.default' => 'array',
            'sanctum.expiration' => null,
            'services.sari_fcm.project_id' => 'unit-test-only',
            'services.sari_fcm.credentials_file' => __DIR__.'/missing-test-credentials.json',
        ]);
        Http::preventStrayRequests();
        $this->createSchema();
    }

    protected function tearDown(): void
    {
        DB::purge('mobile_patch_test');
        parent::tearDown();
    }

    public function test_all_device_batches_are_processed_even_when_devices_are_deleted(): void
    {
        $buyer = $this->buyer();
        // All subscriptions reference a revoked/missing token; no FCM network call is needed.
        for ($i = 0; $i < 101; $i++) {
            $this->device($buyer, 'buyer', 999999);
        }
        $this->artisan('sari:mobile-push', ['--limit' => 100])->assertExitCode(0);
        $this->assertSame(0, DB::table('sari_mobile_devices')->count());
        Http::assertNothingSent();
    }

    public function test_receipts_are_filtered_before_event_limits(): void
    {
        $buyer = $this->buyer();
        $device = $this->device($buyer, 'buyer');
        $order = DB::table('marketplace_orders')->insertGetId(['buyer_account_id' => $buyer->id]);
        for ($i = 0; $i < 16; $i++) {
            DB::table('marketplace_order_events')->insert([
                'marketplace_order_id' => $order, 'audience' => 'buyer', 'created_at' => now(),
            ]);
        }
        $first = $this->events($device);
        $this->assertCount(15, $first);
        foreach ($first as $event) {
            DB::table('sari_mobile_push_receipts')->insert([
                'mobile_device_id' => $device->id, 'event_key' => $event['key'], 'sent_at' => now(),
            ]);
        }
        $second = $this->events($device);
        $this->assertCount(1, $second);
        $this->assertSame('order:16', $second[0]['key']);
        // Another account's receipts cannot suppress this buyer's pending event.
        $other = $this->device($this->buyer(), 'buyer');
        DB::table('sari_mobile_push_receipts')->insert([
            'mobile_device_id' => $other->id, 'event_key' => 'order:16', 'sent_at' => now(),
        ]);
        $this->assertCount(1, $this->events($device));
    }

    public function test_registration_preserves_cutoff_receipts_and_disabled_preference(): void
    {
        $buyer = $this->buyer();
        $issued = $buyer->createToken('sari-flutter:test', ['mobile:buyer'], now()->addDays(30));
        $device = $this->device($buyer, 'buyer', $issued->accessToken->id, ['push_enabled' => false]);
        DB::table('sari_mobile_push_receipts')->insert([
            'mobile_device_id' => $device->id, 'event_key' => 'order:123', 'sent_at' => now(),
        ]);
        $this->withToken($issued->plainTextToken)->postJson('/api/v1/devices', [
            'device_id' => $device->device_id, 'platform' => 'android',
            'fcm_token' => str_repeat('r', 80),
        ])->assertOk();
        $fresh = DB::table('sari_mobile_devices')->find($device->id);
        $this->assertSame($device->registered_at, $fresh->registered_at);
        $this->assertFalse((bool) $fresh->push_enabled);
        $this->assertSame(1, DB::table('sari_mobile_push_receipts')->count());
        $this->assertSame(str_repeat('r', 80), Crypt::decryptString($fresh->token_ciphertext));
    }

    public function test_registration_can_explicitly_reenable_push(): void
    {
        $buyer = $this->buyer();
        $issued = $buyer->createToken('sari-flutter:test', ['mobile:buyer'], now()->addDays(30));
        $device = $this->device($buyer, 'buyer', $issued->accessToken->id, ['push_enabled' => false]);
        $this->withToken($issued->plainTextToken)->postJson('/api/v1/devices', [
            'device_id' => $device->device_id, 'platform' => 'android',
            'fcm_token' => str_repeat('e', 80), 'push_enabled' => true,
        ])->assertOk();
        $this->assertTrue((bool) DB::table('sari_mobile_devices')->find($device->id)->push_enabled);
    }

    public function test_registering_same_fcm_token_to_new_owner_removes_old_subscription(): void
    {
        $first = $this->buyer();
        $device = $this->device($first, 'buyer');
        $fcm = Crypt::decryptString($device->token_ciphertext);
        $second = $this->buyer();
        $issued = $second->createToken('sari-flutter:test', ['mobile:buyer'], now()->addDays(30));
        $this->withToken($issued->plainTextToken)->postJson('/api/v1/devices', [
            'device_id' => 'second-installation', 'platform' => 'android', 'fcm_token' => $fcm,
        ])->assertOk();
        $this->assertFalse(DB::table('sari_mobile_devices')->where('id', $device->id)->exists());
        $this->assertSame(1, DB::table('sari_mobile_devices')->count());
        $this->assertSame((int) $second->id, (int) DB::table('sari_mobile_devices')->first()->account_id);
    }

    public function test_anonymous_registration_is_rejected(): void
    {
        $this->postJson('/api/v1/devices', [])->assertUnauthorized();
    }

    public function test_original_admin_and_new_mobile_schedules_are_present(): void
    {
        $this->artisan('schedule:list')
            ->expectsOutputToContain('admin-ai:maintenance')
            ->expectsOutputToContain('sari:mobile-push')
            ->expectsOutputToContain('sari:mobile-prune')
            ->assertExitCode(0);
    }

    public function test_active_owner_with_correct_token_is_allowed(): void
    {
        $this->assertTrue(app(MobilePushAccess::class)->allows($this->device($this->buyer(), 'buyer')));
    }

    public function test_banned_inactive_and_deleted_accounts_are_rejected(): void
    {
        $buyer = $this->buyer();
        $device = $this->device($buyer, 'buyer');
        $access = app(MobilePushAccess::class);
        DB::table('account_email_bans')->insert(['email' => $buyer->email]);
        $this->assertFalse($access->allows($device));
        DB::table('account_email_bans')->delete();
        $buyer->update(['account_status' => 'suspended']);
        $this->assertFalse($access->allows($device));
        $buyer->update(['account_status' => 'active']);
        $buyer->delete();
        $this->assertFalse($access->allows($device));
    }

    public function test_revoked_expired_wrong_owner_and_wrong_scope_tokens_are_rejected(): void
    {
        $buyer = $this->buyer();
        $device = $this->device($buyer, 'buyer');
        $access = app(MobilePushAccess::class);
        $other = $this->device($this->buyer(), 'buyer');
        $device->personal_access_token_id = $other->personal_access_token_id;
        $this->assertFalse($access->allows($device));
        $device = $this->device($buyer, 'buyer');
        PersonalAccessToken::query()->findOrFail($device->personal_access_token_id)
            ->update(['abilities' => ['mobile:rider']]);
        $this->assertFalse($access->allows($device));
        PersonalAccessToken::query()->findOrFail($device->personal_access_token_id)
            ->update(['abilities' => ['mobile:buyer'], 'expires_at' => now()->subSecond()]);
        $this->assertFalse($access->allows($device));
        PersonalAccessToken::query()->findOrFail($device->personal_access_token_id)->delete();
        $this->assertFalse($access->allows($device));
    }

    public function test_global_sanctum_expiration_is_enforced(): void
    {
        $device = $this->device($this->buyer(), 'buyer');
        config(['sanctum.expiration' => 5]);
        PersonalAccessToken::query()->findOrFail($device->personal_access_token_id)
            ->update(['created_at' => now()->subMinutes(6)]);
        $this->assertFalse(app(MobilePushAccess::class)->allows($device));
    }

    public function test_rider_push_stops_when_parent_provider_is_inaccessible(): void
    {
        $provider = LogisticsAccount::query()->create(['email' => 'provider@example.test', 'account_status' => 'active']);
        $rider = CourierAccount::query()->create([
            'email' => 'rider@example.test', 'account_status' => 'active', 'logistics_account_id' => $provider->id,
        ]);
        $device = $this->device($rider, 'rider');
        $access = app(MobilePushAccess::class);
        $this->assertTrue($access->allows($device));
        $provider->update(['account_status' => 'suspended']);
        $this->assertFalse($access->allows($device));
        $provider->update(['account_status' => 'active']);
        DB::table('account_email_bans')->insert(['email' => $provider->email]);
        $this->assertFalse($access->allows($device));
    }

    public function test_rider_polling_materializes_current_assignment_notifications(): void
    {
        $rider = CourierAccount::query()->create(['email' => 'poll-rider@example.test', 'account_status' => 'active']);
        DB::table('marketplace_orders')->insert([
            'courier_email' => $rider->email, 'order_number' => 'SARI-POLL',
            'status' => 'courier_accepted', 'updated_at' => now(),
        ]);
        $request = Request::create('/api/v1/updates');
        $request->setUserResolver(fn () => $rider);
        $response = app(MobileUpdatesController::class)->index($request)->getData(true);
        $this->assertSame(1, $response['data']['unread_notifications']);
        $this->assertGreaterThan(0, $response['data']['latest_notification_id']);
    }

    public function test_logistics_message_counts_and_pushes_are_provider_scoped(): void
    {
        $provider = LogisticsAccount::query()->create(['email' => 'manager@example.test', 'account_status' => 'active']);
        $device = $this->device($provider, 'logistics');
        foreach ([[$provider->id, 'rider', null], [$provider->id, 'rider', now()],
            [$provider->id + 100, 'rider', null], [$provider->id, 'logistics', null]] as [$id, $sender, $read]) {
            DB::table('logistics_messages')->insert([
                'logistics_account_id' => $id, 'courier_account_id' => 1,
                'sender_role' => $sender, 'body' => 'Test', 'read_at' => $read, 'created_at' => now(),
            ]);
        }
        $request = Request::create('/api/v1/updates');
        $request->setUserResolver(fn () => $provider);
        $data = app(MobileUpdatesController::class)->index($request)->getData(true)['data'];
        $this->assertSame(1, $data['unread_messages']);
        $this->assertSame(2, $data['latest_message_id']);
        $this->assertSame(['logistics-message:1', 'logistics-message:2'], array_column($this->events($device), 'key'));
    }

    public function test_expired_receipts_cannot_replay_events_outside_retention(): void
    {
        $device = $this->device($this->buyer(), 'buyer', null, ['registered_at' => now()->subDays(60)]);
        $order = DB::table('marketplace_orders')->insertGetId(['buyer_account_id' => $device->account_id]);
        DB::table('marketplace_order_events')->insert([
            'marketplace_order_id' => $order, 'audience' => 'buyer', 'created_at' => now()->subDays(31),
        ]);
        $this->assertSame([], $this->events($device));
    }

    public function test_failed_delivery_is_retained_for_retry(): void
    {
        $device = $this->device($this->buyer(), 'buyer');
        $order = DB::table('marketplace_orders')->insertGetId(['buyer_account_id' => $device->account_id]);
        DB::table('marketplace_order_events')->insert([
            'marketplace_order_id' => $order, 'audience' => 'buyer', 'created_at' => now(),
        ]);
        // Intentionally nonexistent credential file makes sender return false without network.
        $this->artisan('sari:mobile-push')->assertExitCode(1);
        $this->assertSame(0, DB::table('sari_mobile_push_receipts')->count());
        $this->assertCount(1, $this->events($device));
        Http::assertNothingSent();
    }

    private function buyer(): BuyerAccount
    {
        return BuyerAccount::query()->create(['email' => Str::uuid().'@example.test', 'account_status' => 'active']);
    }

    private function device($account, string $role, ?int $tokenId = null, array $extra = []): object
    {
        $tokenId ??= $account->createToken('sari-flutter:test', ['mobile:'.$role], now()->addDays(30))->accessToken->id;
        $fcm = (string) Str::uuid().(string) Str::uuid();
        $id = DB::table('sari_mobile_devices')->insertGetId(array_replace([
            'account_role' => $role, 'account_id' => $account->id, 'personal_access_token_id' => $tokenId,
            'device_id' => (string) Str::uuid(), 'platform' => 'android',
            'token_hash' => hash('sha256', $fcm), 'token_ciphertext' => Crypt::encryptString($fcm),
            'push_enabled' => true, 'registered_at' => now()->subHour(), 'last_seen_at' => now(),
            'created_at' => now(), 'updated_at' => now(),
        ], $extra));
        return DB::table('sari_mobile_devices')->find($id);
    }

    private function events(object $device): array
    {
        $method = new ReflectionMethod(DispatchMobilePush::class, 'eventsFor');
        return $method->invoke(app(DispatchMobilePush::class), $device);
    }

    private function createSchema(): void
    {
        foreach (['buyer_accounts', 'courier_accounts', 'logistics_accounts'] as $name) {
            Schema::create($name, function (Blueprint $table): void {
                $table->id(); $table->string('email')->unique(); $table->string('account_status');
                $table->unsignedBigInteger('logistics_account_id')->nullable();
                $table->timestamps(); $table->softDeletes();
            });
        }
        Schema::create('account_email_bans', function (Blueprint $table): void {
            $table->id(); $table->string('email'); $table->timestamp('unbanned_at')->nullable();
        });
        Schema::create('personal_access_tokens', function (Blueprint $table): void {
            $table->id(); $table->morphs('tokenable'); $table->string('name'); $table->string('token', 64)->unique();
            $table->text('abilities')->nullable(); $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable(); $table->timestamps();
        });
        Schema::create('marketplace_orders', function (Blueprint $table): void {
            $table->id(); $table->unsignedBigInteger('buyer_account_id')->nullable();
            $table->string('courier_email')->nullable(); $table->string('order_number')->nullable();
            $table->string('status')->nullable(); $table->timestamps();
        });
        Schema::create('marketplace_order_events', function (Blueprint $table): void {
            $table->id(); $table->unsignedBigInteger('marketplace_order_id');
            $table->string('audience'); $table->timestamps();
        });
        Schema::create('buyer_seller_messages', function (Blueprint $table): void {
            $table->id(); $table->unsignedBigInteger('buyer_account_id');
            $table->string('sender_role'); $table->timestamp('read_at')->nullable(); $table->timestamps();
        });
        Schema::create('logistics_messages', function (Blueprint $table): void {
            $table->id(); $table->unsignedBigInteger('logistics_account_id');
            $table->unsignedBigInteger('courier_account_id'); $table->string('sender_role');
            $table->text('body'); $table->timestamp('read_at')->nullable(); $table->timestamps();
        });
        Schema::create('courier_notifications', function (Blueprint $table): void {
            $table->id(); $table->unsignedBigInteger('courier_account_id'); $table->string('event_key')->unique();
            $table->string('category'); $table->string('title'); $table->text('message');
            $table->string('target_url'); $table->timestamp('occurred_at');
            $table->timestamp('read_at')->nullable(); $table->timestamp('archived_at')->nullable();
            $table->timestamp('deleted_at')->nullable(); $table->timestamps();
        });
        Schema::create('logistics_notifications', function (Blueprint $table): void {
            $table->id(); $table->unsignedBigInteger('logistics_account_id');
            $table->unsignedBigInteger('logistics_hub_account_id')->nullable();
            $table->string('recipient_type'); $table->string('recipient_key');
            $table->timestamp('read_at')->nullable(); $table->timestamps();
        });
        $migration = require database_path('migrations/2026_11_12_170000_create_mobile_sync_infrastructure.php');
        $migration->up();
    }
}
