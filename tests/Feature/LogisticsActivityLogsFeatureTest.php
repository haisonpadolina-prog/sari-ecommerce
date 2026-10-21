<?php

use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\LogisticsHubAccount;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Logistics\LogisticsActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Activity Logs Test Fixtures
|--------------------------------------------------------------------------
*/

function activityFixtureProvider(string $email): LogisticsAccount
{
    return LogisticsAccount::query()->create([
        'first_name' => 'Activity',
        'last_name' => 'Tester',
        'email' => $email,
        'sex' => 'Male',
        'contact_no' => '09171234567',
        'birthday' => '2000-01-01',
        'age' => 26,
        'province_code' => '043400000',
        'province_name' => 'Laguna',
        'municipality_code' => '043426000',
        'municipality_name' => 'Santa Cruz',
        'barangay_code' => '043426001',
        'barangay_name' => 'Bubukal',
        'street_address' => '123 Activity Test Street',
        'password' => Hash::make('ActivityTest123!'),
        'account_status' => 'active',
    ]);
}

function activityFixtureFacility(
    LogisticsAccount $provider,
    string $name,
    string $code
): LogisticsFacility {

    return LogisticsFacility::query()->create([
        'logistics_account_id' => $provider->id,
        'code' => $code,
        'name' => $name,
        'address' => $name.', Laguna',
        'province_name' => 'Laguna',
        'municipality_name' => $name,
        'barangay_name' => 'Bubukal',
        'latitude' => 14.15,
        'longitude' => 121.33,
        'is_active' => true,
    ]);
}

function activityFixtureOperator(
    LogisticsAccount $provider,
    LogisticsFacility $facility,
    string $email
): LogisticsHubAccount {

    return LogisticsHubAccount::query()->create([
        'logistics_account_id' => $provider->id,
        'logistics_facility_id' => $facility->id,
        'name' => 'Audit Operator '.$facility->id,
        'email' => $email,
        'password' => Hash::make('ActivityTest123!'),
        'role' => 'hub_operator',
        'account_status' => 'active',
    ]);
}

/*
|--------------------------------------------------------------------------
| Session Fixtures
|--------------------------------------------------------------------------
*/

function activityManagerSession(
    LogisticsAccount $provider
): array {

    return [
        'is_logistics' => true,
        'logistics_role' => 'manager',
        'logistics_account_id' => $provider->id,
        'logistics_email' => $provider->email,
        'logistics_name' => $provider->displayName(),
    ];
}

function activityHubSession(
    LogisticsHubAccount $operator
): array {

    return [
        'is_logistics' => true,
        'logistics_role' => 'hub_operator',
        'logistics_account_id' => $operator->logistics_account_id,
        'logistics_email' => $operator->email,
        'logistics_name' => $operator->name,
        'is_logistics_hub_operator' => true,
        'logistics_hub_account_id' => $operator->id,
        'logistics_facility_id' => $operator->logistics_facility_id,
        'logistics_hub_name' => $operator->facility->name,
        'logistics_hub_email' => $operator->email,
    ];
}

/*
|--------------------------------------------------------------------------
| Test Activity Record Factory
|--------------------------------------------------------------------------
*/

function activityCreateLog(
    LogisticsAccount $provider,
    string $description,
    ?LogisticsFacility $facility = null,
    string $module = 'Hub Transfers',
    array $extra = []
): LogisticsActivityLog {

    return LogisticsActivityLog::query()->create(
        array_merge([
            'logistics_account_id' => $provider->id,
            'actor_type' => 'manager',
            'actor_id' => $provider->id,
            'actor_name' => $provider->displayName(),
            'logistics_facility_id' => $facility?->id,
            'facility_name' => $facility?->name,
            'module' => $module,
            'action' => 'updated',
            'description' => $description,
            'created_at' => now(),
        ], $extra)
    );
}

/*
|--------------------------------------------------------------------------
| Prepare Test Accounts and Hubs
|--------------------------------------------------------------------------
*/

beforeEach(function (): void {

    $provider = activityFixtureProvider(
        'activity-manager@example.test'
    );

    $other = activityFixtureProvider(
        'activity-other@example.test'
    );

    $origin = activityFixtureFacility(
        $provider,
        'San Pablo',
        'ACT-SANPABLO'
    );

    $destination = activityFixtureFacility(
        $provider,
        'Sta Cruz',
        'ACT-STACRUZ'
    );

    $outside = activityFixtureFacility(
        $other,
        'Calamba',
        'ACT-CALAMBA'
    );

    $operator = activityFixtureOperator(
        $provider,
        $origin,
        'activity-origin@example.test'
    );

    $destinationOperator = activityFixtureOperator(
        $provider,
        $destination,
        'activity-destination@example.test'
    );

    $this->activity = (object) compact(
        'provider',
        'other',
        'origin',
        'destination',
        'outside',
        'operator',
        'destinationOperator'
    );
});

/*
|--------------------------------------------------------------------------
| TEST 1 — Guest Access
|--------------------------------------------------------------------------
*/

test('unauthenticated visitors cannot see activity history', function (): void {

    $this->get(
        route('logistics.activity-logs')
    )->assertRedirect(
        route('login')
    );
});

/*
|--------------------------------------------------------------------------
| TEST 2 — Main Logistics Network Visibility
|--------------------------------------------------------------------------
*/

test('main logistics sees its own network but not another provider', function (): void {

    $a = $this->activity;

    activityCreateLog(
        $a->provider,
        'Origin parcel transferred',
        $a->origin
    );

    activityCreateLog(
        $a->provider,
        'Destination parcel received',
        $a->destination
    );

    activityCreateLog(
        $a->provider,
        'Provider-wide rider approval'
    );

    activityCreateLog(
        $a->other,
        'PRIVATE OTHER PROVIDER LOG',
        $a->outside
    );

    $this->withSession(
        activityManagerSession($a->provider)
    )
        ->get(route('logistics.activity-logs'))
        ->assertOk()
        ->assertSee('Origin parcel transferred')
        ->assertSee('Destination parcel received')
        ->assertSee('Provider-wide rider approval')
        ->assertDontSee('PRIVATE OTHER PROVIDER LOG');
});

/*
|--------------------------------------------------------------------------
| TEST 3 — Hub Visibility
|--------------------------------------------------------------------------
*/

test('hub operator sees only assigned hub records', function (): void {

    $a = $this->activity;

    activityCreateLog(
        $a->provider,
        'LOCAL HUB RECORD',
        $a->origin
    );

    activityCreateLog(
        $a->provider,
        'REMOTE HUB RECORD',
        $a->destination
    );

    activityCreateLog(
        $a->provider,
        'NETWORK ONLY RECORD'
    );

    $this->withSession(
        activityHubSession($a->operator)
    )
        ->get(route('logistics.activity-logs'))
        ->assertOk()
        ->assertSee('LOCAL HUB RECORD')
        ->assertDontSee('REMOTE HUB RECORD')
        ->assertDontSee('NETWORK ONLY RECORD');
});

/*
|--------------------------------------------------------------------------
| TEST 4 — Hub URL Manipulation
|--------------------------------------------------------------------------
*/

test('hub operator cannot escape hub scope with a facility query parameter', function (): void {

    $a = $this->activity;

    activityCreateLog(
        $a->provider,
        'LOCAL HUB RECORD',
        $a->origin
    );

    activityCreateLog(
        $a->provider,
        'REMOTE HUB RECORD',
        $a->destination
    );

    $this->withSession(
        activityHubSession($a->operator)
    )
        ->get(route('logistics.activity-logs', [
            'facility' => $a->destination->id,
        ]))
        ->assertOk()
        ->assertSee('LOCAL HUB RECORD')
        ->assertDontSee('REMOTE HUB RECORD');
});

/*
|--------------------------------------------------------------------------
| TEST 5 — Cross-Provider Facility Filtering
|--------------------------------------------------------------------------
*/

test('main logistics cannot filter using another provider facility id', function (): void {

    $a = $this->activity;

    $this->withSession(
        activityManagerSession($a->provider)
    )
        ->get(route('logistics.activity-logs', [
            'facility' => $a->outside->id,
        ]))
        ->assertNotFound();
});

/*
|--------------------------------------------------------------------------
| TEST 6 — Search and Module Filters
|--------------------------------------------------------------------------
*/

test('search and module filters return only matching activities', function (): void {

    $a = $this->activity;

    activityCreateLog(
        $a->provider,
        'Special transfer manifest',
        $a->origin
    );

    activityCreateLog(
        $a->provider,
        'Regular transfer manifest',
        $a->origin
    );

    activityCreateLog(
        $a->provider,
        'Special pickup verified',
        $a->origin,
        'Pickup Requests'
    );

    $this->withSession(
        activityManagerSession($a->provider)
    )
        ->get(route('logistics.activity-logs', [
            'q' => 'Special',
            'module' => 'Hub Transfers',
        ]))
        ->assertOk()
        ->assertSee('Special transfer manifest')
        ->assertDontSee('Regular transfer manifest')
        ->assertDontSee('Special pickup verified');
});

/*
|--------------------------------------------------------------------------
| TEST 7 — Pagination
|--------------------------------------------------------------------------
*/

test('activity history paginates instead of loading every record', function (): void {

    $a = $this->activity;

    for ($i = 1; $i <= 18; $i++) {

        activityCreateLog(
            $a->provider,
            'Paginated activity '.$i,
            $a->origin
        );
    }

    $this->withSession(
        activityManagerSession($a->provider)
    )
        ->get(route('logistics.activity-logs'))
        ->assertOk()
        ->assertSee('18 matching activities');

    $this->get(route('logistics.activity-logs', [
        'page' => 2,
    ]))
        ->assertOk()
        ->assertSee('18 matching activities');
});

/*
|--------------------------------------------------------------------------
| TEST 8 — Failed Login
|--------------------------------------------------------------------------
*/

test('failed login never creates a successful login activity', function (): void {

    $a = $this->activity;

    $this->post(route('login'), [
        'email' => $a->operator->email,
        'password' => 'WrongPassword!',
    ]);

    expect(
        LogisticsActivityLog::query()->count()
    )->toBe(0);
});

/*
|--------------------------------------------------------------------------
| TEST 9 — Hub Login Audit
|--------------------------------------------------------------------------
*/

test('successful hub login records actual operator and assigned hub', function (): void {

    $a = $this->activity;

    $this->post(route('login'), [
        'email' => $a->operator->email,
        'password' => 'ActivityTest123!',
    ])->assertRedirect(
        route('logistics.dashboard')
    );

    $this->assertDatabaseHas('logistics_activity_logs', [
        'logistics_account_id' => $a->provider->id,
        'actor_type' => 'hub_operator',
        'actor_id' => $a->operator->id,
        'logistics_facility_id' => $a->origin->id,
        'module' => 'Authentication',
        'action' => 'login',
    ]);
});

/*
|--------------------------------------------------------------------------
| TEST 10 — Main Logistics Login Audit
|--------------------------------------------------------------------------
*/

test('successful main logistics login produces a network-wide log', function (): void {

    $a = $this->activity;

    $this->post(route('login'), [
        'email' => $a->provider->email,
        'password' => 'ActivityTest123!',
    ])->assertRedirect(
        route('logistics.dashboard')
    );

    $this->assertDatabaseHas('logistics_activity_logs', [
        'logistics_account_id' => $a->provider->id,
        'actor_type' => 'manager',
        'actor_id' => $a->provider->id,
        'logistics_facility_id' => null,
        'module' => 'Authentication',
        'action' => 'login',
    ]);
});

/*
|--------------------------------------------------------------------------
| TEST 11 — Logout Audit
|--------------------------------------------------------------------------
*/

test('logout produces an activity record before the session is invalidated', function (): void {

    $a = $this->activity;

    $this->withSession(
        activityHubSession($a->operator)
    )
        ->post(route('logistics.logout'))
        ->assertRedirect(route('login'));

    $this->assertDatabaseHas('logistics_activity_logs', [
        'logistics_account_id' => $a->provider->id,
        'actor_type' => 'hub_operator',
        'actor_id' => $a->operator->id,
        'logistics_facility_id' => $a->origin->id,
        'module' => 'Authentication',
        'action' => 'logout',
    ]);
});

/*
|--------------------------------------------------------------------------
| TEST 12 — Read-only Page
|--------------------------------------------------------------------------
*/

test('opening activity logs is read only', function (): void {

    $a = $this->activity;

    $this->withSession(
        activityManagerSession($a->provider)
    )
        ->get(route('logistics.activity-logs'))
        ->assertOk();

    expect(
        LogisticsActivityLog::query()->count()
    )->toBe(0);
});

/*
|--------------------------------------------------------------------------
| TEST 13 — Failed Pickup Verification
|--------------------------------------------------------------------------
*/

test('invalid pickup verification does not create an activity log', function (): void {

    $a = $this->activity;

    $this->withSession(
        activityManagerSession($a->provider)
    )
        ->post(
            route('logistics.pickup-requests.batch-verify'),
            [
                'order_ids' => [],
            ]
        )
        ->assertSessionHasErrors('order_ids');

    expect(
        LogisticsActivityLog::query()->count()
    )->toBe(0);
});
