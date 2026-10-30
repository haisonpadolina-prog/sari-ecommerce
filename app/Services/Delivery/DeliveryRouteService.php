<?php

namespace App\Services\Delivery;

use App\Models\Delivery\DeliveryRouteStop;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class DeliveryRouteService
{
    public const STOP_SELLER = 'seller';

    public const STOP_ORIGIN_FACILITY = 'origin_facility';

    public const STOP_DESTINATION_FACILITY = 'destination_facility';

    public const STOP_BUYER = 'buyer';

    private const ROUTABLE_ORDER_STATUSES = [
        'ready_for_pickup',
        'courier_accepted',
        'heading_pickup',
        'arrived_pickup',
        'in_transit',
        'arrived_buyer',
    ];

    public function __construct(
        private readonly PhilippineAddressGeocoder $geocoder,
    ) {}

    /**
     * @return Collection<int, DeliveryRouteStop>
     */
    public function ensureStops(MarketplaceOrder $order): Collection
    {
        $order->loadMissing([
            'seller',
            'buyer',
            'logisticsAccount',
        ]);

        /*
         * Completed journeys are historical records.
         * Never recreate or reroute them.
         */
        if ($order->status === 'delivered') {
            return $this->getStops($order);
        }

        DB::transaction(function () use ($order): void {
            // One read replaces four firstOrNew() lookups and also lets us
            // avoid issuing an unnecessary DELETE on every tracking poll.
            $existingStops = DeliveryRouteStop::query()
                ->where('marketplace_order_id', $order->id)
                ->get()
                ->keyBy('stop_key');

            if ($existingStops->has('hub_1') || $existingStops->has('hub_2') || $existingStops->has('hub_3')) {
                DeliveryRouteStop::query()
                    ->where('marketplace_order_id', $order->id)
                    ->whereIn('stop_key', ['hub_1', 'hub_2', 'hub_3'])
                    ->delete();
            }

            $definitions = [
                [
                    1,
                    self::STOP_SELLER,
                    'seller',
                    $order->seller?->store_name ?: 'Seller pickup',
                    $order->pickup_address,
                ],
                [
                    2,
                    self::STOP_ORIGIN_FACILITY,
                    'sorting_facility',
                    'Origin Sorting Facility',
                    null,
                ],
                [
                    3,
                    self::STOP_DESTINATION_FACILITY,
                    'sorting_facility',
                    'Destination Sorting Facility',
                    null,
                ],
                [
                    4,
                    self::STOP_BUYER,
                    'buyer',
                    $order->buyer_name ?: 'Buyer',
                    $order->buyer_address,
                ],
            ];

            foreach (
                $definitions as [
                    $sequence,
                    $key,
                    $type,
                    $name,
                    $address
                ]
            ) {
                $stop = $existingStops->get($key) ?? new DeliveryRouteStop([
                    'marketplace_order_id' => $order->id,
                    'stop_key' => $key,
                ]);

                $stop->sequence = $sequence;
                $stop->type = $type;

                /*
                 * Preserve a reached stop's historical snapshot.
                 */
                if (
                    (
                        ! $stop->exists
                        || $type !== 'sorting_facility'
                    )
                    && ! $stop->reached_at
                ) {
                    $stop->name = $name;
                    $stop->address_snapshot = $address;
                }

                if (!$stop->exists || $stop->isDirty()) {
                    $stop->save();
                }
            }
        });

        $this->syncAccountEndpoints($order);
        $this->clearInvalidPhilippineStops($order);
        $this->assignLogisticsProvider($order);
        $this->syncNearestFacilities($order);

        return $this->getStops($order);
    }

    /**
     * @return Collection<int, DeliveryRouteStop>
     */
    private function getStops(MarketplaceOrder $order): Collection
    {
        return DeliveryRouteStop::query()
            ->where('marketplace_order_id', $order->id)
            ->with('facility')
            ->orderBy('sequence')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Assign Unowned Orders
    |--------------------------------------------------------------------------
    */

    public function assignUnownedOrders(int $limit = 75): int
    {
        $assigned = 0;

        MarketplaceOrder::query()
            ->whereNull('logistics_account_id')
            ->whereIn(
                'status',
                self::ROUTABLE_ORDER_STATUSES
            )
            ->oldest('id')
            ->limit(max(1, min($limit, 200)))
            ->get()
            ->each(function (
                MarketplaceOrder $order
            ) use (&$assigned): void {
                try {
                    $this->ensureStops($order);

                    $order->refresh();

                    if ($order->logistics_account_id) {
                        $assigned++;
                    }
                } catch (Throwable $e) {
                    Log::warning(
                        'SARI could not assign a Logistics provider to an order.',
                        [
                            'order_id' => $order->id,
                            'error' => $e->getMessage(),
                        ]
                    );
                }
            });

        return $assigned;
    }

    /*
    |--------------------------------------------------------------------------
    | Explicit Route Refresh
    |--------------------------------------------------------------------------
    |
    | Allowed only before pickup assignment.
    | Never re-geocode an active or completed journey.
    */

    public function refreshFacilities(
        MarketplaceOrder $order
    ): Collection {
        if (
            $order->status !== 'ready_for_pickup'
            || $order->deliveryRouteStops()
                ->whereNotNull('reached_at')
                ->exists()
            || $order->logisticsParcel()
                ->where('status', '!=', 'pickup_verified')
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'route' => 'This route is already in progress or completed and cannot be refreshed.',
            ]);
        }

        $this->ensureStops($order);

        $fresh = $order->fresh([
            'seller',
            'buyer',
            'logisticsAccount',
        ]);

        if ($fresh) {
            $this->syncAccountEndpoints(
                $fresh,
                force: true
            );

            $this->syncNearestFacilities(
                $fresh,
                force: true
            );
        }

        return $this->getStops($order);
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Philippine Coordinates
    |--------------------------------------------------------------------------
    */

    private function clearInvalidPhilippineStops(
        MarketplaceOrder $order
    ): void {
        DeliveryRouteStop::query()
            ->where(
                'marketplace_order_id',
                $order->id
            )
            ->get()
            ->each(function (
                DeliveryRouteStop $stop
            ): void {
                /*
                 * Never erase evidence from a reached stop.
                 */
                if (
                    $stop->reached_at
                    || $stop->latitude === null
                    || $stop->longitude === null
                ) {
                    return;
                }

                if (
                    ! $this->geocoder->isPhilippineCoordinate(
                        $stop->latitude,
                        $stop->longitude
                    )
                ) {
                    $stop->forceFill([
                        'logistics_facility_id' => null,
                        'latitude' => null,
                        'longitude' => null,
                        'reached_at' => null,
                    ]);
                    if ($stop->isDirty()) {
                        $stop->save();
                    }
                }
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Seller and Buyer Coordinates
    |--------------------------------------------------------------------------
    */

    private function syncAccountEndpoints(
        MarketplaceOrder $order,
        bool $force = false
    ): void {
        $stops = DeliveryRouteStop::query()
            ->where(
                'marketplace_order_id',
                $order->id
            )
            ->whereIn('stop_key', [
                self::STOP_SELLER,
                self::STOP_BUYER,
            ])
            ->get()
            ->keyBy('stop_key');

        if ($seller = $stops->get(self::STOP_SELLER)) {
            $this->syncEndpoint(
                $seller,
                fn () => $this->geocoder->seller($order),
                $force
            );
        }

        if ($buyer = $stops->get(self::STOP_BUYER)) {
            $this->syncEndpoint(
                $buyer,
                fn () => $this->geocoder->buyer($order),
                $force
            );
        }
    }

    /**
     * @param callable(): (array{
     *     latitude: float,
     *     longitude: float,
     *     resolved_query: string
     * }|null) $resolver
     */
    private function syncEndpoint(
        DeliveryRouteStop $stop,
        callable $resolver,
        bool $force = false
    ): void {
        /*
         * Physical milestone coordinates must remain recorded
         * exactly as they were when the journey happened.
         */
        if ($stop->reached_at) {
            return;
        }

        $valid = $this->geocoder->isPhilippineCoordinate(
            $stop->latitude,
            $stop->longitude
        );

        if ($valid && ! $force) {
            return;
        }

        /*
         * Resolve before replacing existing coordinates.
         * API failure must not destroy a valid route snapshot.
         */
        $resolved = $resolver();

        if ($resolved === null) {
            if (! $valid) {
                $stop->forceFill([
                    'latitude' => null,
                    'longitude' => null,
                ]);
                if ($stop->isDirty()) {
                    $stop->save();
                }
            }

            return;
        }

        $stop->forceFill([
            'latitude' => $resolved['latitude'],
            'longitude' => $resolved['longitude'],
        ]);
        if ($stop->isDirty()) {
            $stop->save();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Assign Logistics Provider
    |--------------------------------------------------------------------------
    |
    | A provider with one active municipal hub can serve local
    | deliveries, provided both endpoints match that municipality.
    |
    | Providers with multiple hubs can serve inter-municipality routes.
    */

    private function assignLogisticsProvider(
        MarketplaceOrder $order
    ): void {
        if ($order->logistics_account_id) {
            return;
        }

        $stops = DeliveryRouteStop::query()
            ->where(
                'marketplace_order_id',
                $order->id
            )
            ->get()
            ->keyBy('stop_key');

        $seller = $stops->get(self::STOP_SELLER);
        $buyer = $stops->get(self::STOP_BUYER);

        if (
            ! $seller
            || ! $buyer
            || ! $this->geocoder->isPhilippineCoordinate(
                $seller->latitude,
                $seller->longitude
            )
            || ! $this->geocoder->isPhilippineCoordinate(
                $buyer->latitude,
                $buyer->longitude
            )
        ) {
            return;
        }

        /*
         * Preserve a provider implied by existing hub snapshots.
         */
        $existingIds = $stops
            ->filter(
                fn (DeliveryRouteStop $stop): bool => in_array(
                    $stop->stop_key,
                    [
                        self::STOP_ORIGIN_FACILITY,
                        self::STOP_DESTINATION_FACILITY,
                    ],
                    true
                )
                    && (bool) $stop->logistics_facility_id
            )
            ->pluck('logistics_facility_id')
            ->filter()
            ->unique()
            ->values();

        if ($existingIds->isNotEmpty()) {
            $owners = LogisticsFacility::query()
                ->whereIn('id', $existingIds)
                ->whereNotNull('logistics_account_id')
                ->pluck('logistics_account_id')
                ->unique()
                ->values();

            if ($owners->count() === 1) {
                $this->claimOrderForProvider(
                    $order,
                    (int) $owners->first()
                );

                if ($order->logistics_account_id) {
                    return;
                }
            }
        }

        $providers = LogisticsFacility::query()
            ->with('logistics')
            ->active()
            ->whereNotNull('logistics_account_id')
            ->whereHas(
                'logistics',
                fn ($query) => $query->where(
                    'account_status',
                    'active'
                )
            )
            ->get()
            ->filter(
                fn (LogisticsFacility $facility): bool => $this->geocoder->isPhilippineCoordinate(
                    $facility->latitude,
                    $facility->longitude
                )
            )
            ->groupBy('logistics_account_id');

        $best = $providers
            ->map(function (
                Collection $facilities,
                int|string $providerId
            ) use (
                $seller,
                $buyer,
                $order
            ): ?array {
                $origin = $this->nearestForEndpoint(
                    $facilities,
                    (float) $seller->latitude,
                    (float) $seller->longitude,
                    $order->pickup_address,
                    $order->seller?->municipality_name,
                    $order->seller?->province_name
                );

                $destination = $this->nearestForEndpoint(
                    $facilities,
                    (float) $buyer->latitude,
                    (float) $buyer->longitude,
                    $order->buyer_address,
                    $order->buyer?->municipality_name,
                    $order->buyer?->province_name
                );

                if (! $origin || ! $destination) {
                    return null;
                }

                /*
                 * Prevent a provider with only one hub from claiming
                 * an unrelated out-of-town delivery.
                 */
                if (
                    $facilities->count() === 1
                    && (
                        ! $this->endpointMatchesFacility(
                            $order->pickup_address,
                            $order->seller?->municipality_name,
                            $origin
                        )
                        || ! $this->endpointMatchesFacility(
                            $order->buyer_address,
                            $order->buyer?->municipality_name,
                            $destination
                        )
                    )
                ) {
                    return null;
                }

                return [
                    'logistics_account_id' => (int) $providerId,

                    'score' => $this->distanceKm(
                        (float) $seller->latitude,
                        (float) $seller->longitude,
                        (float) $origin->latitude,
                        (float) $origin->longitude
                    )
                        +
                        $this->distanceKm(
                            (float) $buyer->latitude,
                            (float) $buyer->longitude,
                            (float) $destination->latitude,
                            (float) $destination->longitude
                        ),
                ];
            })
            ->filter()
            ->sortBy('score')
            ->first();

        if ($best) {
            $this->claimOrderForProvider(
                $order,
                (int) $best['logistics_account_id']
            );
        }
    }

    private function claimOrderForProvider(
        MarketplaceOrder $order,
        int $providerId
    ): void {
        MarketplaceOrder::query()
            ->whereKey($order->id)
            ->whereNull('logistics_account_id')
            ->update([
                'logistics_account_id' => $providerId,
                'updated_at' => now(),
            ]);

        $order->refresh();
    }

    /*
    |--------------------------------------------------------------------------
    | Assign Origin and Destination Hubs
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | Do not exclude the origin facility when choosing the destination.
    |
    | Same city:
    | Seller → San Pablo Hub → Buyer
    |
    | Different cities:
    | Seller → San Pablo Hub → Sta. Cruz Hub → Buyer
    */

    private function syncNearestFacilities(
        MarketplaceOrder $order,
        bool $force = false
    ): void {
        if (! $order->logistics_account_id) {
            return;
        }

        /*
         * The hub selection is locked once the pickup batch
         * has been assigned to a Rider.
         */
        if (
            $order->logisticsParcel()
                ->where(
                    'status',
                    '!=',
                    'pickup_verified'
                )
                ->exists()
        ) {
            return;
        }

        $stops = DeliveryRouteStop::query()
            ->where(
                'marketplace_order_id',
                $order->id
            )
            ->with('facility')
            ->get()
            ->keyBy('stop_key');

        $seller = $stops->get(self::STOP_SELLER);
        $buyer = $stops->get(self::STOP_BUYER);

        $originStop = $stops->get(
            self::STOP_ORIGIN_FACILITY
        );

        $destinationStop = $stops->get(
            self::STOP_DESTINATION_FACILITY
        );

        if (
            ! $seller
            || ! $buyer
            || ! $originStop
            || ! $destinationStop
            || ! $this->geocoder->isPhilippineCoordinate(
                $seller->latitude,
                $seller->longitude
            )
            || ! $this->geocoder->isPhilippineCoordinate(
                $buyer->latitude,
                $buyer->longitude
            )
        ) {
            return;
        }

        /*
         * Preserve an existing valid route, regardless of whether
         * the two stops refer to the same hub or different hubs.
         */
        if (
            ! $force
            && $this->stopBelongsToProvider(
                $originStop,
                (int) $order->logistics_account_id
            )
            && $this->stopBelongsToProvider(
                $destinationStop,
                (int) $order->logistics_account_id
            )
        ) {
            return;
        }

        $facilities = LogisticsFacility::query()
            ->forLogistics(
                (int) $order->logistics_account_id
            )
            ->active()
            ->get()
            ->filter(
                fn (LogisticsFacility $facility): bool => $this->geocoder->isPhilippineCoordinate(
                    $facility->latitude,
                    $facility->longitude
                )
            )
            ->values();

        if ($facilities->isEmpty()) {
            return;
        }

        $origin = $this->nearestForEndpoint(
            $facilities,
            (float) $seller->latitude,
            (float) $seller->longitude,
            $order->pickup_address,
            $order->seller?->municipality_name,
            $order->seller?->province_name
        );

        /*
         * Deliberately do NOT remove $origin from $facilities.
         * A local delivery can use the same hub.
         */
        $destination = $this->nearestForEndpoint(
            $facilities,
            (float) $buyer->latitude,
            (float) $buyer->longitude,
            $order->buyer_address,
            $order->buyer?->municipality_name,
            $order->buyer?->province_name
        );

        if (! $origin || ! $destination) {
            return;
        }

        if (
            $facilities->count() === 1
            && (
                ! $this->endpointMatchesFacility(
                    $order->pickup_address,
                    $order->seller?->municipality_name,
                    $origin
                )
                || ! $this->endpointMatchesFacility(
                    $order->buyer_address,
                    $order->buyer?->municipality_name,
                    $destination
                )
            )
        ) {
            return;
        }

        $this->applyFacilityToStop(
            $originStop,
            $origin
        );

        $this->applyFacilityToStop(
            $destinationStop,
            $destination
        );
    }

    private function stopBelongsToProvider(
        DeliveryRouteStop $stop,
        int $providerId
    ): bool {
        return $stop->facility
            && (int) $stop->facility->logistics_account_id
                === $providerId
            && $this->geocoder->isPhilippineCoordinate(
                $stop->latitude,
                $stop->longitude
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Municipality-aware Facility Selection
    |--------------------------------------------------------------------------
    |
    | Prefer a hub matching the saved delivery address.
    | If the address is too broad, use registered municipality.
    | Coordinates remain the fallback.
    */

    /**
     * @param  Collection<int, LogisticsFacility>  $facilities
     */
    private function nearestForEndpoint(
        Collection $facilities,
        float $latitude,
        float $longitude,
        ?string $address,
        ?string $municipality,
        ?string $province
    ): ?LogisticsFacility {
        $addressText = $this->normalizePlace($address);

        $municipalityName = $this->normalizePlace(
            $municipality
        );

        $provinceName = $this->normalizePlace(
            $province
        );

        $candidates = $facilities->filter(
            function (
                LogisticsFacility $facility
            ) use ($addressText): bool {
                $name = $this->normalizePlace(
                    $facility->municipality_name
                );

                return $name !== ''
                    && str_contains(
                        ' '.$addressText.' ',
                        ' '.$name.' '
                    );
            }
        );

        if (
            $candidates->isEmpty()
            && $municipalityName !== ''
        ) {
            $candidates = $facilities->filter(
                fn (LogisticsFacility $facility): bool => $this->normalizePlace(
                    $facility->municipality_name
                ) === $municipalityName
            );
        }

        if ($candidates->isEmpty()) {
            $candidates = $facilities;
        }

        $inAddressProvince = $candidates->filter(function (LogisticsFacility $facility) use ($addressText): bool {
            $name = $this->normalizePlace($facility->province_name);

            return $name !== '' && str_contains(' '.$addressText.' ', ' '.$name.' ');
        });

        if ($inAddressProvince->isNotEmpty()) {
            $candidates = $inAddressProvince;
        } elseif ($provinceName !== '') {
            $inProvince = $candidates->filter(
                fn (LogisticsFacility $facility): bool => $this->normalizePlace(
                    $facility->province_name
                ) === $provinceName
            );

            if ($inProvince->isNotEmpty()) {
                $candidates = $inProvince;
            }
        }

        return $this->nearestFacility(
            $candidates->values(),
            $latitude,
            $longitude
        );
    }

    private function endpointMatchesFacility(
        ?string $address,
        ?string $registeredMunicipality,
        LogisticsFacility $facility
    ): bool {
        $name = $this->normalizePlace(
            $facility->municipality_name
        );

        return $name !== ''
            && (
                str_contains(
                    ' '.$this->normalizePlace($address).' ',
                    ' '.$name.' '
                )
                || $this->normalizePlace(
                    $registeredMunicipality
                ) === $name
            );
    }

    private function normalizePlace(?string $value): string
    {
        $value = mb_strtolower(
            trim((string) $value)
        );

        $value = preg_replace(
            '/\b(?:municipality of|city of|city)\b/u',
            ' ',
            $value
        ) ?? $value;

        return trim(
            preg_replace(
                '/[^\pL\pN]+/u',
                ' ',
                $value
            ) ?? $value
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Distance Calculation
    |--------------------------------------------------------------------------
    */

    /**
     * @param  Collection<int, LogisticsFacility>  $facilities
     */
    private function nearestFacility(
        Collection $facilities,
        float $latitude,
        float $longitude
    ): ?LogisticsFacility {
        return $facilities
            ->sortBy(
                fn (LogisticsFacility $facility): float => $this->distanceKm(
                    $latitude,
                    $longitude,
                    (float) $facility->latitude,
                    (float) $facility->longitude
                )
            )
            ->first();
    }

    private function applyFacilityToStop(
        DeliveryRouteStop $stop,
        LogisticsFacility $facility
    ): void {
        if ($stop->reached_at) {
            return;
        }

        $stop->forceFill([
            'logistics_facility_id' => $facility->id,
            'name' => $facility->name,
            'address_snapshot' => $facility->address,
            'latitude' => $facility->latitude,
            'longitude' => $facility->longitude,
        ]);
        if ($stop->isDirty()) {
            $stop->save();
        }
    }

    private function distanceKm(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2
    ): float {
        $earthRadiusKm = 6371.0088;

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a =
            sin($latDelta / 2) ** 2
            +
            cos(deg2rad($lat1))
            *
            cos(deg2rad($lat2))
            *
            sin($lngDelta / 2) ** 2;

        return $earthRadiusKm
            * 2
            * atan2(
                sqrt($a),
                sqrt(max(0, 1 - $a))
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Manual Facility Milestone
    |--------------------------------------------------------------------------
    */

    public function markReached(
        MarketplaceOrder $order,
        DeliveryRouteStop $stop
    ): void {
        if (
            (int) $stop->marketplace_order_id
            !== (int) $order->id
        ) {
            abort(404);
        }

        if (
            $order->status === 'delivered'
            || $stop->reached_at
        ) {
            throw ValidationException::withMessages([
                'route' => 'This milestone is already completed and cannot be changed.',
            ]);
        }

        if ($stop->type !== 'sorting_facility') {
            throw ValidationException::withMessages([
                'route' => 'Seller pickup and Buyer arrival are updated automatically from the delivery workflow.',
            ]);
        }

        $previous = DeliveryRouteStop::query()
            ->where(
                'marketplace_order_id',
                $order->id
            )
            ->where(
                'sequence',
                '<',
                $stop->sequence
            )
            ->orderByDesc('sequence')
            ->first();

        if (
            $previous
            && ! $this->isReached(
                $order,
                $previous
            )
        ) {
            throw ValidationException::withMessages([
                'route' => 'Complete the previous delivery stop first.',
            ]);
        }

        $stop->forceFill([
            'reached_at' => now(),
        ])->save();
    }

    /*
    |--------------------------------------------------------------------------
    | Buyer Tracking Payload
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string, mixed>
     */
    public function buyerPayload(
        MarketplaceOrder $order
    ): array {
        $stops = $this->ensureStops($order)
            ->sortBy('sequence')
            ->values();

        $coordinatesReady =
            $stops->count() === 4
            && $stops->every(
                fn (DeliveryRouteStop $stop): bool => $this->geocoder->isPhilippineCoordinate(
                    $stop->latitude,
                    $stop->longitude
                )
            );

        $payload = $stops
            ->map(function (
                DeliveryRouteStop $stop
            ) use ($order): array {
                return [
                    'id' => $stop->id,
                    'sequence' => $stop->sequence,
                    'key' => $stop->stop_key,
                    'type' => $stop->type,
                    'name' => $stop->name,
                    'address' => $stop->address_snapshot,
                    'lat' => $stop->latitude,
                    'lng' => $stop->longitude,

                    'reached' => $this->isReached(
                        $order,
                        $stop
                    ),

                    'reached_at' => $this->reachedAtForBuyer(
                        $order,
                        $stop
                    )?->toIso8601String(),
                ];
            })
            ->all();

        $lastReached = (int) (
            collect($payload)
                ->filter(
                    fn (array $stop): bool => (bool) $stop['reached']
                )
                ->pluck('sequence')
                ->max()
            ?: 0
        );

        if (
            $order->status === 'delivered'
            || $order->arrived_buyer_at
        ) {
            $lastReached = 4;
        }

        $progress = match ($lastReached) {
            0 => 0,
            1 => 25,
            2 => 50,
            3 => 75,
            default => 100,
        };

        return [
            'ready' => $coordinatesReady,
            'progress' => $progress,
            'last_reached_sequence' => $lastReached,

            'current_sequence' => min(
                4,
                max(1, $lastReached + 1)
            ),

            'stops' => $payload,
        ];
    }

    private function isReached(
        MarketplaceOrder $order,
        DeliveryRouteStop $stop
    ): bool {
        if ($order->status === 'delivered') {
            return true;
        }

        return match ($stop->stop_key) {
            self::STOP_SELLER => (bool) (
                $order->arrived_pickup_at
                || $order->picked_up_at
                || in_array(
                    $order->status,
                    [
                        'arrived_pickup',
                        'in_transit',
                        'arrived_buyer',
                    ],
                    true
                )
            ),

            self::STOP_BUYER => (bool) (
                $order->arrived_buyer_at
                || in_array(
                    $order->status,
                    [
                        'arrived_buyer',
                        'delivered',
                    ],
                    true
                )
            ),

            default => (bool) $stop->reached_at,
        };
    }

    private function reachedAtForBuyer(
        MarketplaceOrder $order,
        DeliveryRouteStop $stop
    ): ?Carbon {
        return match ($stop->stop_key) {
            self::STOP_SELLER => $order->picked_up_at
                ?? $order->arrived_pickup_at,

            self::STOP_BUYER => $order->arrived_buyer_at
                ?? $order->delivered_at,

            default => $stop->reached_at,
        };
    }
}