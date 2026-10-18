<?php

use App\Http\Middleware\Logistics\EnsureLogisticsManager;
use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\LogisticsHubAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Delivery\DeliveryRouteStop;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Delivery\LogisticsParcel;
use App\Models\Messaging\LogisticsMessage;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Registration\RegistrationApplication;
use App\Services\Delivery\DeliveryRouteService;
use App\Services\Delivery\PhilippineAddressGeocoder;
use App\Services\Logistics\LogisticsNotificationService;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function logisticsAuditIdentity(string $email): array
{
    return [
        'first_name' => 'Audit', 'last_name' => 'Account', 'email' => $email,
        'sex' => 'Male', 'contact_no' => '09171234567', 'birthday' => '2000-01-01', 'age' => 26,
        'province_code' => '043400000', 'province_name' => 'Laguna',
        'municipality_code' => '043426000', 'municipality_name' => 'Santa Cruz',
        'barangay_code' => '043426001', 'barangay_name' => 'Bubukal',
        'street_address' => '123 Audit Street', 'password' => Hash::make('AuditPassword123!'),
    ];
}

function logisticsAuditManager(LogisticsAccount $provider): array
{
    return ['is_logistics' => true, 'logistics_account_id' => $provider->id,
        'logistics_email' => $provider->email, 'logistics_name' => 'Audit Logistics', 'logistics_role' => 'manager'];
}

function logisticsAuditHub(LogisticsHubAccount $operator): array
{
    return ['is_logistics' => true, 'logistics_account_id' => $operator->logistics_account_id,
        'logistics_email' => $operator->email, 'logistics_name' => $operator->name,
        'logistics_role' => 'hub_operator', 'is_logistics_hub_operator' => true,
        'logistics_hub_account_id' => $operator->id, 'logistics_facility_id' => $operator->logistics_facility_id,
        'logistics_hub_email' => $operator->email, 'logistics_hub_name' => $operator->facility->name];
}

function logisticsAuditRider(CourierAccount $rider): array
{
    return ['is_courier' => true, 'courier_account_id' => $rider->id,
        'courier_email' => $rider->email, 'courier_name' => 'Audit Rider'];
}

function logisticsAuditOrder(object $fixture, string $status = 'ready_for_pickup', ?string $parcelStatus = null, bool $local = false, ?LogisticsAccount $provider = null): MarketplaceOrder
{
    $order = MarketplaceOrder::query()->create([
        'order_number' => 'AUDIT-'.fake()->unique()->numerify('########'),
        'logistics_account_id' => ($provider ?? $fixture->provider)->id,
        'seller_account_id' => $fixture->seller->id,
        'buyer_name' => 'Audit Buyer', 'buyer_phone' => '09171234568', 'buyer_email' => 'audit-buyer@example.test',
        'buyer_address' => 'Barangay Bubukal, Santa Cruz, Laguna', 'payment_method' => 'COD',
        'payment_status' => 'pending', 'items' => [['name' => 'Audit Product', 'quantity' => 1, 'price' => 200]],
        'subtotal' => 200, 'delivery_fee' => 50, 'total' => 250,
        'pickup_name' => 'Audit Seller', 'pickup_address' => '123 Seller Street, San Pablo City, Laguna',
        'status' => $status, 'ready_at' => now(),
        'courier_email' => $status === 'ready_for_pickup' ? null : $fixture->pickupRider->email,
        'courier_name' => $status === 'ready_for_pickup' ? null : 'Pickup Rider',
        'picked_up_at' => in_array($status, ['in_transit', 'arrived_buyer', 'delivered']) ? now() : null,
        'delivered_at' => $status === 'delivered' ? now() : null,
    ]);

    foreach (['seller', 'origin_facility', 'destination_facility', 'buyer'] as $index => $key) {
        $facility = match ($key) {
            'origin_facility' => $fixture->origin,
            'destination_facility' => $local ? $fixture->origin : $fixture->destination,
            default => null,
        };

        DeliveryRouteStop::query()->create([
            'marketplace_order_id' => $order->id, 'stop_key' => $key, 'sequence' => $index + 1,
            'type' => $facility ? 'sorting_facility' : $key,
            'logistics_facility_id' => $facility?->id, 'name' => $facility?->name ?? ucfirst($key),
            'address_snapshot' => $facility?->address ?? 'Audit Street, Laguna',
            'latitude' => 14.1 + $index * .01, 'longitude' => 121.3 + $index * .01,
        ]);
    }

    if ($parcelStatus) {
        LogisticsParcel::query()->create([
            'marketplace_order_id' => $order->id, 'status' => $parcelStatus,
            'received_at' => in_array($parcelStatus, ['received', 'sorted', 'destination_received']) ? now() : null,
            'sorted_at' => in_array($parcelStatus, ['sorted', 'destination_received']) ? now() : null,
        ]);
    }

    return $order;
}

beforeEach(function (): void {
    Http::preventStrayRequests();
    Http::fake(['nominatim.openstreetmap.org/*' => Http::response([], 200)]);
    Bus::fake();
    Storage::fake('local');
    Storage::fake('public');

    $provider = LogisticsAccount::query()->create(logisticsAuditIdentity('manager@example.test'));
    $other = LogisticsAccount::query()->create(logisticsAuditIdentity('other-manager@example.test'));

    $facilities = [];

    foreach (['San Pablo City', 'Santa Cruz'] as $i => $city) {
        $facilities[] = LogisticsFacility::query()->create([
            'logistics_account_id' => $provider->id, 'code' => 'AUDIT-HUB-'.$i, 'name' => $city.' Audit Hub',
            'address' => $city.', Laguna', 'province_name' => 'Laguna', 'municipality_name' => $city,
            'barangay_name' => 'Bubukal', 'latitude' => 14.1 + $i * .1, 'longitude' => 121.3 + $i * .1, 'is_active' => true,
        ]);
    }

    $operators = [];

    foreach ($facilities as $i => $facility) {
        $operators[] = LogisticsHubAccount::query()->create([
            'logistics_account_id' => $provider->id, 'logistics_facility_id' => $facility->id,
            'name' => 'Hub Operator '.$i, 'email' => 'operator'.$i.'@example.test',
            'password' => Hash::make('AuditPassword123!'), 'role' => 'hub_operator', 'account_status' => 'active',
        ]);
    }

    $riders = [];

    foreach (['San Pablo City', 'Santa Cruz'] as $i => $city) {
        $identity = array_merge(logisticsAuditIdentity('rider'.$i.'@example.test'), [
            'municipality_name' => $city, 'vehicle_type' => 'Motorcycle', 'plate_number' => 'AUDIT-'.$i,
            'logistics_account_id' => $provider->id,
        ]);

        $application = RegistrationApplication::query()->create(array_merge($identity, [
            'role' => 'rider',
            'status' => 'approved',
        ]));

        $riders[] = CourierAccount::query()->create(array_merge($identity, [
            'registration_application_id' => $application->id, 'account_status' => 'active', 'availability_status' => 'online',
        ]));
    }

    $seller = SellerAccount::query()->create([
        'email' => 'audit-seller@example.test',
        'store_name' => 'Audit Seller',
        'store_status' => 'open',
    ]);

    $this->audit = (object) [
        'provider' => $provider,
        'other' => $other,
        'origin' => $facilities[0],
        'destination' => $facilities[1],
        'originOperator' => $operators[0],
        'destinationOperator' => $operators[1],
        'pickupRider' => $riders[0],
        'lastRider' => $riders[1],
        'seller' => $seller,
    ];
});

dataset('logistics audit pages', [
    'dashboard' => ['logistics.dashboard'], 'pickup requests' => ['logistics.pickup-requests'],
    'pickup assignment' => ['logistics.delivery-assignment'], 'rider management' => ['logistics.rider-management'],
    'incoming' => ['logistics.incoming-parcels'], 'sorting' => ['logistics.parcel-sorting'],
    'hub transfers' => ['logistics.hub-transfers'], 'last mile' => ['logistics.last-mile-assignment'],
    'monitoring' => ['logistics.delivery-monitoring'], 'reports' => ['logistics.reports'],
    'notifications' => ['logistics.notifications'], 'notification summary' => ['logistics.notifications.summary'],
    'applications' => ['logistics.rider-applications'], 'facilities' => ['logistics.facilities.index'],
    'messages' => ['logistics.messages'], 'account' => ['logistics.account-management'],
]);

test('all logistics pages render for the manager with empty and populated queues', function (string $name): void {
    $this->withSession(logisticsAuditManager($this->audit->provider))->get(route($name))->assertOk();

    foreach ([['ready_for_pickup', null], ['ready_for_pickup', 'pickup_verified'], ['in_transit', 'awaiting_intake'], ['in_transit', 'received'], ['in_transit', 'sorted'], ['in_transit', 'destination_received'], ['delivered', 'delivered']] as [$status, $parcel]) {
        logisticsAuditOrder($this->audit, $status, $parcel);
    }

    $this->get(route($name))->assertOk();
})->with('logistics audit pages');

test('guest cannot open logistics pages', function (string $name): void {
    $this->get(route($name))->assertRedirect(route('login'));
})->with('logistics audit pages');

test('hub page access matches operational and manager roles', function (string $name): void {
    logisticsAuditOrder($this->audit, 'ready_for_pickup', 'pickup_verified');
    logisticsAuditOrder($this->audit, 'in_transit', 'destination_received');

    $response = $this->withSession(logisticsAuditHub($this->audit->originOperator))->get(route($name));

    if (in_array($name, ['logistics.rider-applications', 'logistics.facilities.index', 'logistics.messages', 'logistics.account-management'])) {
        $response->assertForbidden();
    } else {
        $response->assertOk();

        // Every rendered local navigation link must be accessible to this role.
        preg_match_all('/href="([^"#]+)"/', $response->getContent(), $matches);

        foreach (array_unique($matches[1]) as $href) {
            if (str_starts_with($href, url('/logistics'))) {
                $this->get(html_entity_decode($href))->assertOk();
            }
        }
    }
})->with('logistics audit pages');

test('seller batch verification and assignment are atomic and require all siblings', function (): void {
    $first = logisticsAuditOrder($this->audit);
    $second = logisticsAuditOrder($this->audit);
    $session = logisticsAuditHub($this->audit->originOperator);

    $this->withSession($session)->post(route('logistics.pickup-requests.verify', $first))->assertSessionHasNoErrors();

    $this->post(route('logistics.delivery-assignment.batch-assign'), [
        'order_ids' => [$first->id],
        'rider_id' => $this->audit->pickupRider->id,
    ])->assertSessionHasErrors('order_ids');

    $this->post(route('logistics.pickup-requests.batch-verify'), [
        'order_ids' => [$first->id, $second->id],
    ])->assertSessionHasNoErrors();

    $this->post(route('logistics.delivery-assignment.batch-assign'), [
        'order_ids' => [$first->id, $second->id],
        'rider_id' => $this->audit->pickupRider->id,
    ])->assertSessionHasNoErrors();

    foreach ([$first, $second] as $order) {
        expect($order->fresh()->status)->toBe('courier_accepted')
            ->and($order->fresh()->logisticsParcel->status)->toBe('awaiting_intake');
    }

    $this->post(route('logistics.delivery-assignment.batch-assign'), [
        'order_ids' => [$first->id, $second->id],
        'rider_id' => $this->audit->pickupRider->id,
    ])->assertSessionHasErrors();
});

test('complete inter hub journey preserves pickup rider and credits last mile rider', function (): void {
    $order = logisticsAuditOrder($this->audit);

    $this->withSession(logisticsAuditHub($this->audit->originOperator))
        ->post(route('logistics.pickup-requests.verify', $order))
        ->assertSessionHasNoErrors();

    $this->post(route('logistics.delivery-assignment.assign', $order), [
        'rider_id' => $this->audit->pickupRider->id,
    ])->assertSessionHasNoErrors();

    $this->flushSession();
    $this->withSession(logisticsAuditRider($this->audit->pickupRider));

    foreach (['proceed-pickup', 'arrived-pickup', 'confirm-pickup'] as $action) {
        $this->post(route('courier.orders.'.$action, $order))->assertSessionHasNoErrors();
    }

    $this->post(route('courier.orders.arrived-buyer', $order))->assertSessionHasErrors();

    $this->flushSession();

    $this->withSession(logisticsAuditHub($this->audit->originOperator))
        ->post(route('logistics.incoming-parcels.receive', $order))
        ->assertSessionHasNoErrors();

    $parcel = $order->fresh()->logisticsParcel;

    $this->post(route('logistics.incoming-parcels.receive', $order))->assertSessionHasErrors();

    $this->post(route('logistics.parcel-sorting.sort', $parcel), [
        'sorting_zone' => 'A1',
    ])->assertSessionHasNoErrors();

    $this->post(route('logistics.hub-transfers.store'), [
        'parcel_ids' => [$parcel->id],
        'vehicle_type' => 'Van',
        'driver_name' => 'Audit Driver',
        'vehicle_plate' => 'ABC-123',
    ])->assertSessionHasNoErrors();

    $transfer = DB::table('hub_transfers')->first();

    expect($transfer)->not->toBeNull();

    $this->post(route('logistics.hub-transfers.receive', $transfer->id))->assertForbidden();
    $this->post(route('logistics.hub-transfers.dispatch', $transfer->id))->assertSessionHasNoErrors();

    $this->flushSession();

    $this->withSession(logisticsAuditHub($this->audit->destinationOperator))
        ->post(route('logistics.hub-transfers.receive', $transfer->id))
        ->assertSessionHasNoErrors();

    expect(
        $order->deliveryRouteStops()
            ->where('stop_key', 'destination_facility')
            ->first()
            ->reached_at
    )->not->toBeNull();

    $this->post(route('logistics.last-mile-assignment.batch-assign'), [
        'order_ids' => [$order->id],
        'rider_id' => $this->audit->lastRider->id,
    ])->assertSessionHasNoErrors();

    $assignment = DB::table('last_mile_assignments')->first();

    $this->flushSession();

    $this->withSession(logisticsAuditRider($this->audit->lastRider))
        ->post(route('courier.last-mile.start', $assignment->id))
        ->assertSessionHasNoErrors();

    $this->post(route('courier.last-mile.delivered', [$assignment->id, $order->id]))->assertSessionHasErrors();
    $this->post(route('courier.last-mile.arrived', [$assignment->id, $order->id]))->assertSessionHasNoErrors();
    $this->post(route('courier.last-mile.delivered', [$assignment->id, $order->id]))->assertSessionHasNoErrors();
    $this->post(route('courier.last-mile.delivered', [$assignment->id, $order->id]))->assertSessionHasNoErrors();

    expect($order->fresh()->status)->toBe('delivered')
        ->and($order->fresh()->courier_email)->toBe($this->audit->pickupRider->email)
        ->and($order->fresh()->logisticsParcel->status)->toBe('delivered')
        ->and(DB::table('last_mile_assignments')->value('status'))->toBe('completed');

    $this->assertDatabaseHas('rider_earnings', [
        'marketplace_order_id' => $order->id,
        'courier_account_id' => $this->audit->lastRider->id,
    ]);
});

test('local sorting bypasses transfers and last mile cannot interrupt another pickup', function (): void {
    $order = logisticsAuditOrder($this->audit, 'in_transit', 'received', true);

    $this->withSession(logisticsAuditHub($this->audit->originOperator))
        ->post(route('logistics.parcel-sorting.sort', $order->logisticsParcel), [
            'sorting_zone' => 'LOCAL',
        ])->assertSessionHasNoErrors();

    expect($order->fresh()->logisticsParcel->status)->toBe('destination_received');

    logisticsAuditOrder($this->audit, 'courier_accepted', 'awaiting_intake');

    $this->post(route('logistics.last-mile-assignment.batch-assign'), [
        'order_ids' => [$order->id],
        'rider_id' => $this->audit->pickupRider->id,
    ])->assertSessionHasErrors('rider_id');

    $this->assertDatabaseCount('last_mile_assignments', 0);
});

test('manager messages remain private to their provider', function (): void {
    LogisticsMessage::query()->create([
        'courier_account_id' => $this->audit->pickupRider->id,
        'logistics_account_id' => $this->audit->other->id,
        'sender_role' => 'logistics',
        'body' => 'PRIVATE-OTHER-PROVIDER',
    ]);

    $this->withSession(logisticsAuditManager($this->audit->provider))
        ->get(route('logistics.messages'))
        ->assertOk()
        ->assertDontSee('PRIVATE-OTHER-PROVIDER');
});

test('invalid message cannot claim an unowned rider', function (): void {
    $rider = $this->audit->pickupRider;

    $rider->forceFill(['logistics_account_id' => null])->save();
    $rider->registrationApplication?->forceFill(['logistics_account_id' => null])->save();

    $this->withSession(logisticsAuditManager($this->audit->provider))
        ->post(route('logistics.messages.send', $rider), [
            'body' => '',
        ])->assertSessionHasErrors('body');

    expect($rider->fresh()->logistics_account_id)->toBeNull();
});

test('manager profile password validation and persisted update work', function (): void {
    $account = $this->audit->provider;

    $payload = [
        'first_name' => 'Updated',
        'last_name' => 'Manager',
        'contact_no' => '09171234567',
        'email' => $account->email,
        'street_address' => 'New Street',
        'current_password' => 'wrong',
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ];

    $this->withSession(logisticsAuditManager($account))
        ->patch(route('logistics.account-management.update'), $payload)
        ->assertSessionHasErrors('current_password');

    $payload['current_password'] = 'AuditPassword123!';

    $this->patch(route('logistics.account-management.update'), $payload)->assertSessionHasNoErrors();

    expect(Hash::check('NewPassword123!', $account->fresh()->password))->toBeTrue()
        ->and($account->fresh()->first_name)->toBe('Updated');
});

test('all registered controller routes resolve to public methods', function (): void {
    foreach (Route::getRoutes() as $route) {
        $action = $route->getActionName();

        if (! str_contains($action, '@')) {
            continue;
        }

        [$class, $method] = explode('@', $action, 2);

        expect(class_exists($class))->toBeTrue($action);
        expect(method_exists($class, $method))->toBeTrue($action);
        expect((new ReflectionMethod($class, $method))->isPublic())->toBeTrue($action);
    }
});

test('every logistics endpoint rejects guests including mutation requests', function (): void {
    $order = logisticsAuditOrder($this->audit, 'in_transit', 'received');

    $notification = app(LogisticsNotificationService::class)->notifyManager(
        $this->audit->provider->id,
        'audit',
        'audit',
        'Audit'
    );

    $params = [
        'order' => $order->id,
        'stop' => $order->deliveryRouteStops()->first()->id,
        'parcel' => $order->logisticsParcel->id,
        'rider' => $this->audit->pickupRider->id,
        'facility' => $this->audit->origin->id,
        'application' => $this->audit->pickupRider->registration_application_id,
        'notification' => $notification->id,
        'transfer' => 1,
        'document' => 'id',
    ];

    foreach (Route::getRoutes() as $route) {
        if (! str_starts_with($route->getName() ?? '', 'logistics.')) {
            continue;
        }

        $this->flushSession();

        $arguments = array_intersect_key(
            $params,
            array_flip($route->parameterNames())
        );

        $this->json(
            $route->methods()[0],
            route($route->getName(), $arguments)
        )->assertUnauthorized();
    }
});

test('hub operator cannot invoke any manager only endpoint', function (): void {
    $order = logisticsAuditOrder($this->audit, 'in_transit', 'received');

    $params = [
        'order' => $order->id,
        'stop' => $order->deliveryRouteStops()->first()->id,
        'rider' => $this->audit->pickupRider->id,
        'facility' => $this->audit->origin->id,
        'application' => $this->audit->pickupRider->registration_application_id,
        'document' => 'id',
    ];

    foreach (Route::getRoutes() as $route) {
        if (! in_array(EnsureLogisticsManager::class, $route->gatherMiddleware())) {
            continue;
        }

        $this->flushSession();

        $arguments = array_intersect_key(
            $params,
            array_flip($route->parameterNames())
        );

        $this->withSession(logisticsAuditHub($this->audit->originOperator))
            ->json(
                $route->methods()[0],
                route($route->getName(), $arguments)
            )->assertForbidden();
    }
});

test('forged cross provider and cross hub operations do not alter parcels', function (): void {
    $own = logisticsAuditOrder($this->audit);
    $foreign = logisticsAuditOrder($this->audit, provider: $this->audit->other);

    $this->withSession(logisticsAuditManager($this->audit->provider))
        ->post(route('logistics.pickup-requests.verify', $foreign))
        ->assertNotFound();

    $this->post(route('logistics.pickup-requests.batch-verify'), [
        'order_ids' => [$own->id, $foreign->id],
    ])->assertSessionHasErrors();

    $this->assertDatabaseCount('logistics_parcels', 0);
    $this->flushSession();

    $this->withSession(logisticsAuditHub($this->audit->destinationOperator))
        ->post(route('logistics.pickup-requests.verify', $own))
        ->assertNotFound();

    $incoming = logisticsAuditOrder($this->audit, 'in_transit', 'awaiting_intake');

    $this->post(route('logistics.incoming-parcels.receive', $incoming))->assertNotFound();

    expect($incoming->fresh()->logisticsParcel->status)->toBe('awaiting_intake');

    $sorting = logisticsAuditOrder($this->audit, 'in_transit', 'received');

    $this->post(route('logistics.parcel-sorting.sort', $sorting->logisticsParcel), [
        'sorting_zone' => 'A1',
    ])->assertNotFound();

    expect($sorting->fresh()->logisticsParcel->status)->toBe('received');
});

test('revoked inactive and incomplete logistics identities cannot continue', function (): void {
    $session = logisticsAuditHub($this->audit->originOperator);

    unset($session['logistics_hub_account_id']);

    $this->withSession($session)->getJson(route('logistics.dashboard'))->assertForbidden();

    $this->flushSession();
    $this->audit->origin->update(['is_active' => false]);

    $this->withSession(logisticsAuditHub($this->audit->originOperator))
        ->getJson(route('logistics.dashboard'))
        ->assertForbidden();

    $this->flushSession();
    $this->audit->provider->update(['account_status' => 'deactivated']);

    $this->withSession(logisticsAuditManager($this->audit->provider))
        ->getJson(route('logistics.dashboard'))
        ->assertForbidden();
});

test('notification read states and links are private idempotent and scoped', function (): void {
    $service = app(LogisticsNotificationService::class);

    $own = $service->notifyManager(
        $this->audit->provider->id,
        'manager:audit',
        'audit',
        'Manager Notification',
        actionRoute: 'logistics.pickup-requests'
    );

    $hub = $service->notifyHubOperator(
        $this->audit->originOperator->id,
        'hub:audit',
        'audit',
        'Hub Notification'
    );

    $foreign = $service->notifyManager(
        $this->audit->other->id,
        'other:audit',
        'audit',
        'Other Notification'
    );

    $this->withSession(logisticsAuditManager($this->audit->provider))
        ->getJson(route('logistics.notifications.summary'))
        ->assertOk()
        ->assertJsonPath('unread_count', 1);

    $this->post(route('logistics.notifications.read', $hub))->assertNotFound();
    $this->post(route('logistics.notifications.open', $foreign))->assertNotFound();

    $this->post(route('logistics.notifications.open', $own))
        ->assertRedirect(route('logistics.pickup-requests'));

    $readAt = $own->fresh()->read_at->toISOString();

    $service->notifyManager(
        $this->audit->provider->id,
        'manager:audit',
        'audit',
        'Manager Notification'
    );

    expect($own->fresh()->read_at->toISOString())->toBe($readAt);

    $this->post(route('logistics.notifications.read-all'))->assertSessionHasNoErrors();

    expect($hub->fresh()->read_at)->toBeNull()
        ->and($foreign->fresh()->read_at)->toBeNull();

    $this->flushSession();

    $this->withSession(logisticsAuditHub($this->audit->originOperator))
        ->post(route('logistics.notifications.read', $own))
        ->assertNotFound();

    $this->post(route('logistics.notifications.read', $hub))->assertSessionHasNoErrors();
});

test('facilities can be created edited toggled and removed with safe validation', function (): void {
    $payload = [
        'name' => 'Audit New Hub',
        'address' => 'Test Street, Calamba, Laguna',
        'province_name' => 'Laguna',
        'municipality_name' => 'Calamba',
        'barangay_name' => 'Canlubang',
        'latitude' => 14.2,
        'longitude' => 121.2,
        'is_active' => 1,
        'operator_name' => 'Audit Operator',
        'operator_email' => 'new-operator@example.test',
        'operator_password' => 'HubPassword123!',
        'operator_password_confirmation' => 'HubPassword123!',
    ];

    $this->withSession(logisticsAuditManager($this->audit->provider))
        ->post(route('logistics.facilities.store'), $payload)
        ->assertSessionHasNoErrors();

    $hub = LogisticsFacility::query()->where('name', 'Audit New Hub')->firstOrFail();

    $this->get(route('logistics.facilities.edit', $hub))->assertOk();

    $this->post(route('logistics.facilities.store'), $payload)
        ->assertSessionHasErrors(['name', 'municipality_name', 'operator_email']);

    $payload['name'] = 'Updated Audit Hub';
    $payload['operator_password'] = '';
    $payload['operator_password_confirmation'] = '';

    $this->patch(route('logistics.facilities.update', $hub), $payload)->assertSessionHasNoErrors();

    $operator = LogisticsHubAccount::query()
        ->where('logistics_facility_id', $hub->id)
        ->firstOrFail();

    expect(Hash::check('HubPassword123!', $operator->password))->toBeTrue();

    $this->patch(route('logistics.facilities.toggle', $hub))->assertSessionHasNoErrors();

    expect($hub->fresh()->is_active)->toBeFalse();

    $this->delete(route('logistics.facilities.destroy', $this->audit->origin))->assertSessionHasNoErrors();
    $this->delete(route('logistics.facilities.destroy', $hub))->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('logistics_hub_accounts', ['id' => $operator->id]);
});

test('municipal hub uniqueness distinguishes provinces and protects historical hubs', function (): void {
    $order = logisticsAuditOrder($this->audit);

    $this->withSession(logisticsAuditManager($this->audit->provider))
        ->delete(route('logistics.facilities.destroy', $this->audit->origin))
        ->assertSessionHasErrors('facility');

    $this->post(route('logistics.facilities.store'), [
        'name' => 'Other Province Santa Cruz',
        'address' => 'Audit Street',
        'province_name' => 'Marinduque',
        'municipality_name' => 'Santa Cruz',
        'latitude' => 13.48,
        'longitude' => 122.03,
        'is_active' => 1,
        'operator_name' => 'Another Operator',
        'operator_email' => 'marinduque@example.test',
        'operator_password' => 'HubPassword123!',
        'operator_password_confirmation' => 'HubPassword123!',
    ])->assertSessionHasNoErrors();

    $this->assertDatabaseHas('logistics_facilities', [
        'province_name' => 'Marinduque',
        'municipality_name' => 'Santa Cruz',
    ]);
});

test('rider applications support documents approval rejection and isolation', function (): void {
    $application = RegistrationApplication::query()->create(array_merge(
        logisticsAuditIdentity('pending@example.test'),
        [
            'role' => 'rider',
            'logistics_account_id' => $this->audit->provider->id,
            'status' => 'pending',
            'id_path' => 'audit/id.pdf',
            'orcr_path' => 'audit/orcr.pdf',
            'vehicle_type' => 'Motorcycle',
            'plate_number' => 'AUDIT-3',
        ]
    ));

    Storage::disk('local')->put('audit/id.pdf', 'audit identity document');
    Storage::disk('local')->put('audit/orcr.pdf', 'audit vehicle document');

    $this->withSession(logisticsAuditManager($this->audit->provider));

    foreach (['id', 'orcr'] as $document) {
        $this->get(route('logistics.rider-applications.document', [$application, $document]))->assertOk();
    }

    $this->post(route('logistics.rider-applications.approve', $application))->assertSessionHasNoErrors();

    expect($application->fresh()->status)->toBe('approved');

    $this->assertDatabaseHas('courier_accounts', [
        'registration_application_id' => $application->id,
        'logistics_account_id' => $this->audit->provider->id,
    ]);

    $this->post(route('logistics.rider-applications.reject', $application), [
        'review_note' => 'Should not change approved account',
    ])->assertSessionHasErrors('application');

    $pending = $application->replicate(['email']);

    $pending->forceFill([
        'email' => 'rejected@example.test',
        'status' => 'pending',
    ])->save();

    $this->post(route('logistics.rider-applications.reject', $pending), [
        'review_note' => 'Missing valid vehicle document',
    ])->assertSessionHasNoErrors();

    expect($pending->fresh()->status)->toBe('rejected');

    $this->flushSession();

    $this->withSession(logisticsAuditManager($this->audit->other))
        ->get(route('logistics.rider-applications.document', [$application, 'id']))
        ->assertNotFound();
});

test('route review works before verification and active routes cannot be changed', function (): void {
    $order = logisticsAuditOrder($this->audit);

    $this->withSession(logisticsAuditManager($this->audit->provider))
        ->get(route('logistics.delivery-routes.edit', $order))
        ->assertOk();

    $this->patch(route('logistics.delivery-routes.update', $order))->assertSessionHasNoErrors();

    $order->update([
        'status' => 'in_transit',
        'courier_email' => $this->audit->pickupRider->email,
        'picked_up_at' => now(),
    ]);

    LogisticsParcel::query()->create([
        'marketplace_order_id' => $order->id,
        'status' => 'awaiting_intake',
    ]);

    $this->patch(route('logistics.delivery-routes.update', $order))->assertSessionHasErrors('route');

    $other = logisticsAuditOrder($this->audit);

    $this->post(route('logistics.delivery-routes.stops.reached', [
        $order,
        $other->deliveryRouteStops()->where('stop_key', 'origin_facility')->first(),
    ]))->assertNotFound();
});

test('last mile matches street first addresses and rejects a rider from a namesake municipality in another province', function (): void {
    $order = logisticsAuditOrder($this->audit, 'in_transit', 'destination_received');

    $order->update([
        'buyer_address' => '123 Buyer Street, Bubukal, Santa Cruz, Laguna',
    ]);

    $this->withSession(logisticsAuditManager($this->audit->provider))
        ->get(route('logistics.last-mile-assignment'))
        ->assertOk()
        ->assertViewHas(
            'deliveryGroups',
            fn ($groups) => $groups->first()->first()->barangay_name === 'Bubukal'
        );

    $this->audit->lastRider->update(['province_name' => 'Marinduque']);

    $this->post(route('logistics.last-mile-assignment.batch-assign'), [
        'order_ids' => [$order->id],
        'rider_id' => $this->audit->lastRider->id,
    ])->assertSessionHasErrors('rider_id');

    $this->audit->lastRider->update([
        'province_name' => 'Laguna',
        'barangay_name' => 'Barangay Bubukal',
    ]);

    $this->post(route('logistics.last-mile-assignment.batch-assign'), [
        'order_ids' => [$order->id],
        'rider_id' => $this->audit->lastRider->id,
    ])->assertSessionHasNoErrors();
});

test('reports validate ranges and header search submits to an effective delivery filter', function (): void {
    $this->withSession(logisticsAuditManager($this->audit->provider))
        ->get(route('logistics.reports', [
            'from' => '2026-10-18',
            'to' => '2026-10-01',
        ]))->assertSessionHasErrors('from');

    $this->get(route('logistics.delivery-monitoring', [
        'q' => 'AUDIT-SEARCH',
    ]))->assertOk()->assertSee('value="AUDIT-SEARCH"', false);
});

test('rendered logistics forms have matching routes verbs and csrf tokens', function (): void {
    foreach ([['ready_for_pickup', null], ['ready_for_pickup', 'pickup_verified'], ['in_transit', 'awaiting_intake'], ['in_transit', 'received'], ['in_transit', 'sorted'], ['in_transit', 'destination_received'], ['delivered', 'delivered']] as [$status, $parcel]) {
        $order = logisticsAuditOrder($this->audit, $status, $parcel);

        if ($parcel === null) {
            $order->update([
                'pickup_address' => 'Alternate Seller Pickup Address',
            ]);
        }
    }

    RegistrationApplication::query()->create(array_merge(
        logisticsAuditIdentity('browser-applicant@example.test'),
        [
            'role' => 'rider',
            'logistics_account_id' => $this->audit->provider->id,
            'status' => 'pending',
            'vehicle_type' => 'Motorcycle',
            'plate_number' => 'UI-123',
        ]
    ));

    foreach (['preparing', 'in_transit'] as $state) {
        $manifestOrder = logisticsAuditOrder($this->audit, 'in_transit', 'sorted');

        $this->withSession(logisticsAuditManager($this->audit->provider))
            ->post(route('logistics.hub-transfers.store'), [
                'parcel_ids' => [$manifestOrder->logisticsParcel->id],
                'vehicle_type' => 'Van',
                'driver_name' => 'Audit Driver',
            ])->assertSessionHasNoErrors();

        if ($state === 'in_transit') {
            $transferId = DB::table('hub_transfers')->max('id');

            $this->post(route('logistics.hub-transfers.dispatch', $transferId))->assertSessionHasNoErrors();
        }

        $this->flushSession();
    }

    $exportDirectory = getenv('LOGISTICS_AUDIT_EXPORT_DIR');

    foreach ([
        'manager' => logisticsAuditManager($this->audit->provider),
        'hub' => logisticsAuditHub($this->audit->originOperator),
    ] as $role => $session) {
        foreach (Route::getRoutes() as $route) {
            $name = $route->getName() ?? '';

            if (! str_starts_with($name, 'logistics.') || ! in_array('GET', $route->methods()) || $route->parameterNames() !== [] || str_ends_with($name, '.summary')) {
                continue;
            }

            if ($role === 'hub' && in_array(EnsureLogisticsManager::class, $route->gatherMiddleware())) {
                continue;
            }

            $this->flushSession();

            $response = $this->withSession($session)->get(route($name))->assertOk();

            if ($exportDirectory) {
                if (! is_dir($exportDirectory)) {
                    mkdir($exportDirectory, 0770, true);
                }

                file_put_contents(
                    $exportDirectory.'/'.$role.'.'.$name.'.html',
                    $response->getContent()
                );
            }

            $dom = new DOMDocument;
            $previous = libxml_use_internal_errors(true);

            $dom->loadHTML($response->getContent());

            libxml_clear_errors();
            libxml_use_internal_errors($previous);

            foreach ($dom->getElementsByTagName('form') as $form) {
                $method = strtoupper($form->getAttribute('method') ?: 'GET');
                $token = false;

                foreach ($form->getElementsByTagName('input') as $input) {
                    if ($input->getAttribute('name') === '_token') {
                        $token = true;
                    }

                    if ($input->getAttribute('name') === '_method') {
                        $method = strtoupper($input->getAttribute('value'));
                    }
                }

                if ($method !== 'GET') {
                    expect($token)->toBeTrue($role.' '.$name.' missing CSRF token');
                }

                $target = $form->getAttribute('action') ?: route($name);

                $matched = Route::getRoutes()->match(
                    Request::create($target, $method)
                );

                expect($matched->getName())->not->toBeNull();
            }
        }
    }
});

test('courier endpoints revalidate deleted and mismatched rider sessions', function (): void {
    $order = logisticsAuditOrder($this->audit, 'courier_accepted', 'awaiting_intake');
    $session = logisticsAuditRider($this->audit->pickupRider);

    $this->audit->pickupRider->delete();

    $this->withSession($session)
        ->postJson(route('courier.orders.proceed-pickup', $order))
        ->assertForbidden();

    expect($order->fresh()->status)->toBe('courier_accepted');

    $this->flushSession();

    $session = logisticsAuditRider($this->audit->lastRider);
    $session['courier_email'] = 'wrong@example.test';

    $this->withSession($session)->getJson(route('courier.dashboard'))->assertForbidden();
});

test('logistics login and logout preserve exact manager and operator identity', function (): void {
    foreach ([
        'manager' => $this->audit->provider,
        'hub_operator' => $this->audit->originOperator,
    ] as $role => $account) {
        $this->flushSession();

        $this->post('/login', [
            'email' => $account->email,
            'password' => 'AuditPassword123!',
        ])->assertRedirect(route('logistics.dashboard'))
            ->assertSessionHas('logistics_role', $role);

        $this->get(route('logistics.dashboard'))->assertOk();

        $this->post(route('logistics.logout'))
            ->assertRedirect(route('login'))
            ->assertSessionMissing('is_logistics')
            ->assertSessionMissing('logistics_hub_account_id');
    }
});

test('production login cannot bootstrap demo accounts', function (string $role): void {
    $this->withoutMiddleware(ValidateCsrfToken::class);
    $this->app->instance('env', 'production');

    try {
        $this->post('/login', [
            'email' => $role.'@gmail.com',
            'password' => $role.'123',
        ])->assertRedirect()
            ->assertSessionHasErrors('email')
            ->assertSessionMissing('is_'.$role);

        $table = match ($role) {
            'rider', 'courier' => 'courier_accounts',
            default => $role.'_accounts',
        };

        $this->assertDatabaseMissing($table, [
            'email' => $role.'@gmail.com',
        ]);
    } finally {
        $this->app->instance('env', 'testing');
    }
})->with(['admin', 'buyer', 'seller', 'courier']);

test('route geocoding uses the order shipping snapshot before current account location', function (): void {
    $buyer = BuyerAccount::query()->create(
        logisticsAuditIdentity('snapshot-buyer@example.test')
    );

    $order = logisticsAuditOrder($this->audit);

    $order->update([
        'buyer_account_id' => $buyer->id,
        'buyer_address' => '456 Delivery Street, Calamba, Laguna',
    ]);

    Http::fake([
        '*' => Http::response([
            ['lat' => '14.2', 'lon' => '121.2'],
        ]),
    ]);

    app(PhilippineAddressGeocoder::class)->buyer($order);

    Http::assertSent(
        fn ($request) => $request['q'] === '456 Delivery Street, Calamba, Laguna, Philippines'
    );
});

test('public logistics and rider registration and address fallback endpoints respond', function (): void {
    $this->get(route('register.logistics'))->assertOk();
    $this->get(route('rider.logistics.index'))->assertOk();
    $this->get(route('rider.logistics.apply', $this->audit->provider))->assertOk();

    $this->post(route('register.logistics.submit'), [])->assertSessionHasErrors();

    Http::fake([
        'psgc.cloud/*' => Http::response([], 503),
        'psgc.gitlab.io/api/provinces/' => Http::response([
            ['code' => '043400000', 'name' => 'Laguna'],
        ]),
        'psgc.gitlab.io/api/provinces/*' => Http::response([
            ['code' => '043426000', 'name' => 'Santa Cruz'],
        ]),
        'psgc.gitlab.io/api/cities-municipalities/*' => Http::response([
            ['code' => '043426001', 'name' => 'Bubukal'],
        ]),
    ]);

    $this->getJson(route('address.provinces'))
        ->assertOk()
        ->assertJsonFragment(['name' => 'Laguna']);

    $this->getJson(route('address.municipalities', '043400000'))
        ->assertOk()
        ->assertJsonFragment(['name' => 'Santa Cruz']);

    $this->getJson(route('address.barangays', '043426000'))
        ->assertOk()
        ->assertJsonFragment(['name' => 'Bubukal']);
});

test('used hubs cannot move municipalities and malformed search stays a validation error', function (): void {
    logisticsAuditOrder($this->audit);
    $hub = $this->audit->origin;

    $this->withSession(logisticsAuditManager($this->audit->provider))
        ->patch(route('logistics.facilities.update', $hub), [
            'name' => $hub->name,
            'address' => $hub->address,
            'province_name' => $hub->province_name,
            'municipality_name' => 'Calamba',
            'barangay_name' => 'Bubukal',
            'latitude' => 14.1,
            'longitude' => 121.3,
            'is_active' => 1,
        ])->assertSessionHasErrors('municipality_name');

    expect($hub->fresh()->municipality_name)->toBe('San Pablo City');

    $this->getJson(route('logistics.delivery-monitoring', [
        'q' => ['invalid'],
    ]))->assertUnprocessable();

    $this->get(route('logistics.dashboard', [
        'q' => ['invalid'],
    ]))->assertOk();
});

test('route refresh chooses the order province over a different registered address', function (): void {
    $remote = LogisticsFacility::query()->create([
        'logistics_account_id' => $this->audit->provider->id,
        'code' => 'AUDIT-REMOTE',
        'name' => 'Santa Cruz Marinduque Hub',
        'address' => 'Santa Cruz, Marinduque',
        'province_name' => 'Marinduque',
        'municipality_name' => 'Santa Cruz',
        'latitude' => 13.48,
        'longitude' => 122.03,
        'is_active' => true,
    ]);

    $buyer = BuyerAccount::query()->create(
        logisticsAuditIdentity('remote-buyer@example.test')
    );

    $order = logisticsAuditOrder($this->audit);

    $order->update([
        'buyer_account_id' => $buyer->id,
        'buyer_address' => 'Barangay Bagong Silang, Santa Cruz, Marinduque',
    ]);

    Http::swap(new Factory);
    Http::preventStrayRequests();

    Http::fake([
        '*' => Http::response([
            ['lat' => '14.2', 'lon' => '121.3'],
        ]),
    ]);

    $this->withSession(logisticsAuditManager($this->audit->provider))
        ->patch(route('logistics.delivery-routes.update', $order))
        ->assertSessionHasNoErrors();

    expect(
        $order->deliveryRouteStops()
            ->where('stop_key', 'destination_facility')
            ->value('logistics_facility_id')
    )->toBe($remote->id);
});

test('admin user management renders with every referenced action route', function (): void {
    $admin = AdminAccount::query()->create([
        'name' => 'Audit Admin',
        'email' => 'audit-admin@example.test',
        'password' => Hash::make('AuditPassword123!'),
    ]);

    $this->withSession([
        'is_admin' => true,
        'admin_account_id' => $admin->id,
    ])->get(route('admin.users'))->assertOk();
});

test('unowned orders cannot be routed through unauthorized review requests', function (): void {
    $order = logisticsAuditOrder($this->audit);

    $order->deliveryRouteStops()->delete();
    $order->forceFill(['logistics_account_id' => null])->save();

    $this->withSession(logisticsAuditManager($this->audit->provider))
        ->get(route('logistics.delivery-routes.edit', $order))
        ->assertNotFound();

    expect($order->fresh()->logistics_account_id)->toBeNull();

    $this->assertDatabaseCount('delivery_route_stops', 0);

    Http::assertNothingSent();
});

test('every courier endpoint rejects guests', function (): void {
    $order = logisticsAuditOrder($this->audit, 'courier_accepted', 'awaiting_intake');

    foreach (Route::getRoutes() as $route) {
        if (! str_starts_with($route->getName() ?? '', 'courier.')) {
            continue;
        }

        $this->flushSession();

        $params = array_intersect_key(
            ['order' => $order->id, 'assignment' => 1],
            array_flip($route->parameterNames())
        );

        $this->json(
            $route->methods()[0],
            route($route->getName(), $params)
        )->assertUnauthorized();
    }
});

test('courier pages render for a valid active rider', function (string $name): void {
    logisticsAuditOrder($this->audit, 'courier_accepted', 'awaiting_intake');
    logisticsAuditOrder($this->audit, 'in_transit', 'awaiting_intake');

    $this->withSession(logisticsAuditRider($this->audit->pickupRider))
        ->get(route($name))
        ->assertOk();
})->with([
    'courier.dashboard',
    'courier.requests',
    'courier.pickups',
    'courier.deliveries',
    'courier.earnings',
    'courier.history',
    'courier.messages',
    'courier.profile',
    'courier.last-mile',
    'courier.earnings.statement',
    'courier.history.export',
]);

test('pickup assignment cannot double book a rider finishing a legacy direct delivery', function (): void {
    logisticsAuditOrder($this->audit, 'arrived_buyer');

    $queued = logisticsAuditOrder(
        $this->audit,
        'ready_for_pickup',
        'pickup_verified'
    );

    $this->withSession(logisticsAuditManager($this->audit->provider))
        ->post(route('logistics.delivery-assignment.batch-assign'), [
            'order_ids' => [$queued->id],
            'rider_id' => $this->audit->pickupRider->id,
        ])->assertSessionHasErrors('rider_id');

    expect($queued->fresh()->status)->toBe('ready_for_pickup');
});

test('completed delivery route snapshots are preserved and missing history is never fabricated', function (): void {
    $order = logisticsAuditOrder($this->audit, 'delivered', 'delivered');

    $before = $order->deliveryRouteStops()->get()->toArray();
    $service = app(DeliveryRouteService::class);

    $service->ensureStops($order);

    expect($order->deliveryRouteStops()->get()->toArray())->toBe($before);

    $order->deliveryRouteStops()->delete();

    expect($service->ensureStops($order))->toHaveCount(0);

    Http::assertNothingSent();
});

test('manager messages send persist and receive read receipts within their provider', function (): void {
    $rider = $this->audit->pickupRider;

    $this->withSession(logisticsAuditManager($this->audit->provider))
        ->post(route('logistics.messages.send', $rider), [
            'body' => 'Please collect the verified seller batch.',
        ])->assertSessionHasNoErrors();

    $message = LogisticsMessage::query()->firstOrFail();

    expect($message->logistics_account_id)->toBe($this->audit->provider->id)
        ->and($message->sender_role)->toBe('logistics');

    $this->flushSession();

    $this->withSession(logisticsAuditRider($rider))
        ->get(route('courier.messages'))
        ->assertOk()
        ->assertSee('Please collect the verified seller batch.');

    expect($message->fresh()->read_at)->not->toBeNull();

    $this->post(route('courier.messages.send'), [
        'body' => 'Seller pickup acknowledged.',
    ])->assertSessionHasNoErrors();

    $this->flushSession();

    $this->withSession(logisticsAuditManager($this->audit->provider))
        ->get(route('logistics.messages'))
        ->assertOk()
        ->assertSee('Seller pickup acknowledged.');

    expect(
        LogisticsMessage::query()
            ->where('sender_role', 'rider')
            ->first()
            ->read_at
    )->not->toBeNull();
});

test('manual route milestones accept the next facility and reject duplicates endpoints and completed journeys', function (): void {
    $order = logisticsAuditOrder($this->audit, 'in_transit', 'awaiting_intake');

    $origin = $order->deliveryRouteStops()
        ->where('stop_key', 'origin_facility')
        ->firstOrFail();

    $buyer = $order->deliveryRouteStops()
        ->where('stop_key', 'buyer')
        ->firstOrFail();

    $this->withSession(logisticsAuditManager($this->audit->provider))
        ->post(route('logistics.delivery-routes.stops.reached', [$order, $origin]))
        ->assertSessionHasNoErrors();

    expect($origin->fresh()->reached_at)->not->toBeNull();

    $this->post(route('logistics.delivery-routes.stops.reached', [$order, $origin]))
        ->assertSessionHasErrors('route');

    $this->post(route('logistics.delivery-routes.stops.reached', [$order, $buyer]))
        ->assertSessionHasErrors('route');

    $order->update([
        'status' => 'delivered',
        'delivered_at' => now(),
    ]);

    $this->patch(route('logistics.delivery-routes.update', $order))
        ->assertSessionHasErrors('route');
});

test('logistics mutations enforce csrf tokens outside the testing bypass', function (): void {
    $order = logisticsAuditOrder($this->audit);
    $this->app->instance('env', 'local');

    try {
        $session = array_merge(
            logisticsAuditManager($this->audit->provider),
            ['_token' => 'audit-csrf-token']
        );

        $this->withSession($session)
            ->post(route('logistics.pickup-requests.verify', $order))
            ->assertStatus(419);

        $this->assertDatabaseCount('logistics_parcels', 0);

        $this->withSession($session)
            ->post(route('logistics.pickup-requests.verify', $order), [
                '_token' => 'audit-csrf-token',
            ])->assertSessionHasNoErrors();

        expect($order->fresh()->logisticsParcel->status)->toBe('pickup_verified');
    } finally {
        $this->app->instance('env', 'testing');
    }
});