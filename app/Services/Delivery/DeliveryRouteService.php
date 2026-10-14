<?php

namespace App\Services\Delivery;

use App\Models\Delivery\DeliveryRouteStop;
use App\Models\Delivery\LogisticsFacility;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class DeliveryRouteService
{
    public function __construct(
        private readonly PhilippineAddressGeocoder $geocoder,
    ) {
    }

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
        'delivered',
    ];

    /**
     * Seller → nearest facility owned by the assigned Logistics provider
     * → nearest distinct facility to Buyer → Buyer.
     *
     * @return Collection<int, DeliveryRouteStop>
     */
    public function ensureStops(MarketplaceOrder $order): Collection
    {
        $order->loadMissing(['seller', 'buyer', 'logisticsAccount']);

        DB::transaction(function () use ($order): void {
            DeliveryRouteStop::query()
                ->where('marketplace_order_id', $order->id)
                ->whereIn('stop_key', ['hub_1', 'hub_2', 'hub_3'])
                ->delete();

            $definitions = [
                [
                    'sequence' => 1,
                    'stop_key' => self::STOP_SELLER,
                    'type' => 'seller',
                    'name' => $order->seller?->store_name ?: 'Seller pickup',
                    'address_snapshot' => $order->pickup_address,
                ],
                [
                    'sequence' => 2,
                    'stop_key' => self::STOP_ORIGIN_FACILITY,
                    'type' => 'sorting_facility',
                    'name' => 'Origin Sorting Facility',
                    'address_snapshot' => null,
                ],
                [
                    'sequence' => 3,
                    'stop_key' => self::STOP_DESTINATION_FACILITY,
                    'type' => 'sorting_facility',
                    'name' => 'Destination Sorting Facility',
                    'address_snapshot' => null,
                ],
                [
                    'sequence' => 4,
                    'stop_key' => self::STOP_BUYER,
                    'type' => 'buyer',
                    'name' => $order->buyer_name ?: 'Buyer',
                    'address_snapshot' => $order->buyer_address,
                ],
            ];

            foreach ($definitions as $definition) {
                $stop = DeliveryRouteStop::query()->firstOrNew([
                    'marketplace_order_id' => $order->id,
                    'stop_key' => $definition['stop_key'],
                ]);

                $stop->sequence = $definition['sequence'];
                $stop->type = $definition['type'];

                if (!$stop->exists || $definition['type'] !== 'sorting_facility') {
                    $stop->name = $definition['name'];
                    $stop->address_snapshot = $definition['address_snapshot'];
                }

                $stop->save();
            }
        });

        $this->syncAccountEndpoints($order);
        $this->clearInvalidPhilippineStops($order);
        $this->assignLogisticsProvider($order);
        $this->syncNearestFacilities($order);

        return DeliveryRouteStop::query()
            ->where('marketplace_order_id', $order->id)
            ->with('facility')
            ->orderBy('sequence')
            ->get();
    }

    /**
     * Deterministically route a bounded number of legacy/unowned orders.
     */
    public function assignUnownedOrders(int $limit = 75): int
    {
        $assigned = 0;

        MarketplaceOrder::query()
            ->whereNull('logistics_account_id')
            ->whereIn('status', self::ROUTABLE_ORDER_STATUSES)
            ->oldest('id')
            ->limit(max(1, min($limit, 200)))
            ->get()
            ->each(function (MarketplaceOrder $order) use (&$assigned): void {
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

    /**
     * Recalculate facilities inside the same Logistics provider only.
     */
    public function refreshFacilities(MarketplaceOrder $order): Collection
    {
        $this->ensureStops($order);

        $fresh = $order->fresh([
            'seller',
            'buyer',
            'logisticsAccount',
        ]);

        if ($fresh) {
            $this->syncNearestFacilities($fresh, force: true);
        }

        return DeliveryRouteStop::query()
            ->where('marketplace_order_id', $order->id)
            ->with('facility')
            ->orderBy('sequence')
            ->get();
    }

    private function clearInvalidPhilippineStops(MarketplaceOrder $order): void
    {
        DeliveryRouteStop::query()
            ->where('marketplace_order_id', $order->id)
            ->get()
            ->each(function (DeliveryRouteStop $stop): void {
                if (
                    $stop->latitude === null
                    || $stop->longitude === null
                ) {
                    return;
                }

                if (
                    !$this->geocoder->isPhilippineCoordinate(
                        $stop->latitude,
                        $stop->longitude
                    )
                ) {
                    $stop->forceFill([
                        'logistics_facility_id' => null,
                        'latitude' => null,
                        'longitude' => null,
                        'reached_at' => $stop->type === 'sorting_facility'
                            ? null
                            : $stop->reached_at,
                    ])->save();
                }
            });
    }

    private function syncAccountEndpoints(MarketplaceOrder $order): void
    {
        $sellerStop = DeliveryRouteStop::query()
            ->where('marketplace_order_id', $order->id)
            ->where('stop_key', self::STOP_SELLER)
            ->first();

        $buyerStop = DeliveryRouteStop::query()
            ->where('marketplace_order_id', $order->id)
            ->where('stop_key', self::STOP_BUYER)
            ->first();

        if ($sellerStop) {
            $this->syncEndpoint(
                $sellerStop,
                fn () => $this->geocoder->seller($order)
            );
        }

        if ($buyerStop) {
            $this->syncEndpoint(
                $buyerStop,
                fn () => $this->geocoder->buyer($order)
            );
        }
    }

    /**
     * @param callable(): (array{latitude: float, longitude: float, resolved_query: string}|null) $resolver
     */
    private function syncEndpoint(
        DeliveryRouteStop $stop,
        callable $resolver
    ): void {
        if (
            $this->geocoder->isPhilippineCoordinate(
                $stop->latitude,
                $stop->longitude
            )
        ) {
            return;
        }

        if ($stop->latitude !== null || $stop->longitude !== null) {
            $stop->forceFill([
                'latitude' => null,
                'longitude' => null,
            ])->save();
        }

        $resolved = $resolver();

        if ($resolved === null) {
            return;
        }

        $stop->forceFill([
            'latitude' => $resolved['latitude'],
            'longitude' => $resolved['longitude'],
        ])->save();
    }

    /**
     * A provider is routable when it owns at least two active Philippine
     * facilities. The provider with the smallest combined Seller access
     * distance + Buyer access distance wins.
     */
    private function assignLogisticsProvider(MarketplaceOrder $order): void
    {
        if ($order->logistics_account_id) {
            return;
        }

        $stops = DeliveryRouteStop::query()
            ->where('marketplace_order_id', $order->id)
            ->get()
            ->keyBy('stop_key');

        /** @var DeliveryRouteStop|null $seller */
        $seller = $stops->get(self::STOP_SELLER);
        /** @var DeliveryRouteStop|null $buyer */
        $buyer = $stops->get(self::STOP_BUYER);

        if (
            !$seller
            || !$buyer
            || !$this->geocoder->isPhilippineCoordinate(
                $seller->latitude,
                $seller->longitude
            )
            || !$this->geocoder->isPhilippineCoordinate(
                $buyer->latitude,
                $buyer->longitude
            )
        ) {
            return;
        }

        /*
         * Preserve a provider implied by an existing route snapshot.
         */
        $existingFacilityIds = $stops
            ->filter(
                fn (DeliveryRouteStop $stop): bool =>
                    in_array(
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

        if ($existingFacilityIds->isNotEmpty()) {
            $owners = LogisticsFacility::query()
                ->whereIn('id', $existingFacilityIds)
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

        $facilitiesByProvider = LogisticsFacility::query()
            ->with('logistics')
            ->active()
            ->whereNotNull('logistics_account_id')
            ->whereHas('logistics', function ($query): void {
                $query->where('account_status', 'active');
            })
            ->get()
            ->filter(
                fn (LogisticsFacility $facility): bool =>
                    $this->geocoder->isPhilippineCoordinate(
                        $facility->latitude,
                        $facility->longitude
                    )
            )
            ->groupBy('logistics_account_id')
            ->filter(
                fn (Collection $facilities): bool =>
                    $facilities->count() >= 2
            );

        if ($facilitiesByProvider->isEmpty()) {
            return;
        }

        $best = $facilitiesByProvider
            ->map(function (
                Collection $facilities,
                int|string $logisticsAccountId
            ) use ($seller, $buyer): ?array {
                $origin = $this->nearestFacility(
                    $facilities,
                    (float) $seller->latitude,
                    (float) $seller->longitude
                );

                if (!$origin) {
                    return null;
                }

                $destination = $this->nearestFacility(
                    $facilities
                        ->reject(
                            fn (LogisticsFacility $facility): bool =>
                                (int) $facility->id === (int) $origin->id
                        )
                        ->values(),
                    (float) $buyer->latitude,
                    (float) $buyer->longitude
                );

                if (!$destination) {
                    return null;
                }

                return [
                    'logistics_account_id' => (int) $logisticsAccountId,
                    'score' =>
                        $this->distanceKm(
                            (float) $seller->latitude,
                            (float) $seller->longitude,
                            (float) $origin->latitude,
                            (float) $origin->longitude
                        )
                        + $this->distanceKm(
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

        if (!$best) {
            return;
        }

        $this->claimOrderForProvider(
            $order,
            (int) $best['logistics_account_id']
        );
    }

    private function claimOrderForProvider(
        MarketplaceOrder $order,
        int $logisticsAccountId
    ): void {
        MarketplaceOrder::query()
            ->whereKey($order->id)
            ->whereNull('logistics_account_id')
            ->update([
                'logistics_account_id' => $logisticsAccountId,
                'updated_at' => now(),
            ]);

        $order->refresh();
    }

    private function syncNearestFacilities(
        MarketplaceOrder $order,
        bool $force = false
    ): void {
        if (!$order->logistics_account_id) {
            return;
        }

        $stops = DeliveryRouteStop::query()
            ->where('marketplace_order_id', $order->id)
            ->with('facility')
            ->get()
            ->keyBy('stop_key');

        /** @var DeliveryRouteStop|null $seller */
        $seller = $stops->get(self::STOP_SELLER);
        /** @var DeliveryRouteStop|null $buyer */
        $buyer = $stops->get(self::STOP_BUYER);
        /** @var DeliveryRouteStop|null $originStop */
        $originStop = $stops->get(self::STOP_ORIGIN_FACILITY);
        /** @var DeliveryRouteStop|null $destinationStop */
        $destinationStop = $stops->get(self::STOP_DESTINATION_FACILITY);

        if (
            !$seller
            || !$buyer
            || !$originStop
            || !$destinationStop
            || !$this->geocoder->isPhilippineCoordinate(
                $seller->latitude,
                $seller->longitude
            )
            || !$this->geocoder->isPhilippineCoordinate(
                $buyer->latitude,
                $buyer->longitude
            )
        ) {
            return;
        }

        if (
            !$force
            && $this->stopBelongsToProvider(
                $originStop,
                (int) $order->logistics_account_id
            )
            && $this->stopBelongsToProvider(
                $destinationStop,
                (int) $order->logistics_account_id
            )
            && (int) $originStop->logistics_facility_id
                !== (int) $destinationStop->logistics_facility_id
        ) {
            return;
        }

        $facilities = LogisticsFacility::query()
            ->forLogistics((int) $order->logistics_account_id)
            ->active()
            ->get()
            ->filter(
                fn (LogisticsFacility $facility): bool =>
                    $this->geocoder->isPhilippineCoordinate(
                        $facility->latitude,
                        $facility->longitude
                    )
            )
            ->values();

        if ($facilities->count() < 2) {
            return;
        }

        $origin = $this->nearestFacility(
            $facilities,
            (float) $seller->latitude,
            (float) $seller->longitude
        );

        if (!$origin) {
            return;
        }

        $destination = $this->nearestFacility(
            $facilities
                ->reject(
                    fn (LogisticsFacility $facility): bool =>
                        (int) $facility->id === (int) $origin->id
                )
                ->values(),
            (float) $buyer->latitude,
            (float) $buyer->longitude
        );

        if (!$destination) {
            return;
        }

        $this->applyFacilityToStop($originStop, $origin);
        $this->applyFacilityToStop($destinationStop, $destination);
    }

    private function stopBelongsToProvider(
        DeliveryRouteStop $stop,
        int $logisticsAccountId
    ): bool {
        if (
            !$stop->facility
            || (int) $stop->facility->logistics_account_id
                !== $logisticsAccountId
        ) {
            return false;
        }

        return $this->geocoder->isPhilippineCoordinate(
            $stop->latitude,
            $stop->longitude
        );
    }

    /**
     * @param Collection<int, LogisticsFacility> $facilities
     */
    private function nearestFacility(
        Collection $facilities,
        float $latitude,
        float $longitude
    ): ?LogisticsFacility {
        return $facilities
            ->sortBy(
                fn (LogisticsFacility $facility): float =>
                    $this->distanceKm(
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
        $stop->forceFill([
            'logistics_facility_id' => $facility->id,
            'name' => $facility->name,
            'address_snapshot' => $facility->address,
            'latitude' => $facility->latitude,
            'longitude' => $facility->longitude,
        ])->save();
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
            + cos(deg2rad($lat1))
            * cos(deg2rad($lat2))
            * sin($lngDelta / 2) ** 2;

        return $earthRadiusKm * 2 * atan2(
            sqrt($a),
            sqrt(max(0, 1 - $a))
        );
    }

    public function markReached(
        MarketplaceOrder $order,
        DeliveryRouteStop $stop
    ): void {
        if ((int) $stop->marketplace_order_id !== (int) $order->id) {
            abort(404);
        }

        if ($stop->type !== 'sorting_facility') {
            throw ValidationException::withMessages([
                'route' => 'Seller pickup and Buyer arrival are updated automatically from the delivery workflow.',
            ]);
        }

        $previous = DeliveryRouteStop::query()
            ->where('marketplace_order_id', $order->id)
            ->where('sequence', '<', $stop->sequence)
            ->orderByDesc('sequence')
            ->first();

        if ($previous && !$this->isReached($order, $previous)) {
            throw ValidationException::withMessages([
                'route' => 'Complete the previous delivery stop first.',
            ]);
        }

        $stop->forceFill(['reached_at' => now()])->save();
    }

    /**
     * @return array<string, mixed>
     */
    public function buyerPayload(MarketplaceOrder $order): array
    {
        $stops = $this->ensureStops($order)
            ->sortBy('sequence')
            ->values();

        $allCoordinatesReady = $stops->count() === 4
            && $stops->every(
                fn (DeliveryRouteStop $stop): bool =>
                    $this->geocoder->isPhilippineCoordinate(
                        $stop->latitude,
                        $stop->longitude
                    )
            );

        $stopPayload = $stops->map(function (
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
                'reached' => $this->isReached($order, $stop),
                'reached_at' =>
                    $this->reachedAtForBuyer($order, $stop)
                        ?->toIso8601String(),
            ];
        })->all();

        $lastReachedSequence = (int) (
            collect($stopPayload)
                ->filter(
                    fn (array $stop): bool =>
                        (bool) $stop['reached']
                )
                ->pluck('sequence')
                ->max()
            ?: 0
        );

        if ($order->status === 'delivered' || $order->arrived_buyer_at) {
            $lastReachedSequence = 4;
        }

        $progress = match ($lastReachedSequence) {
            0 => 0,
            1 => 25,
            2 => 50,
            3 => 75,
            default => 100,
        };

        return [
            'ready' => $allCoordinatesReady,
            'progress' => $progress,
            'last_reached_sequence' => $lastReachedSequence,
            'current_sequence' => min(
                4,
                max(1, $lastReachedSequence + 1)
            ),
            'stops' => $stopPayload,
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
                    ['arrived_pickup', 'in_transit', 'arrived_buyer'],
                    true
                )
            ),
            self::STOP_BUYER => (bool) (
                $order->arrived_buyer_at
                || in_array(
                    $order->status,
                    ['arrived_buyer', 'delivered'],
                    true
                )
            ),
            default => (bool) $stop->reached_at,
        };
    }

    private function reachedAtForBuyer(
        MarketplaceOrder $order,
        DeliveryRouteStop $stop
    ): ?\Illuminate\Support\Carbon {
        return match ($stop->stop_key) {
            self::STOP_SELLER => $order->picked_up_at
                ?? $order->arrived_pickup_at,
            self::STOP_BUYER => $order->arrived_buyer_at
                ?? $order->delivered_at,
            default => $stop->reached_at,
        };
    }
}
