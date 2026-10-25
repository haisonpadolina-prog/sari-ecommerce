<?php

namespace App\Services\Delivery;

use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Orders\MarketplaceOrder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PhilippineAddressGeocoder
{
    private const ENDPOINT = 'https://nominatim.openstreetmap.org/search';

    /*
     * Broad Philippine bounding box. Results outside this range are rejected
     * so stale/manual coordinates can never send SARI tracking to Africa,
     * Europe, or another country.
     */
    public function isPhilippineCoordinate(
        mixed $latitude,
        mixed $longitude
    ): bool {
        if (! is_numeric($latitude) || ! is_numeric($longitude)) {
            return false;
        }

        $lat = (float) $latitude;
        $lng = (float) $longitude;

        return $lat >= 4.0
            && $lat <= 22.5
            && $lng >= 116.0
            && $lng <= 127.5;
    }

    /**
     * @return array{latitude: float, longitude: float, resolved_query: string}|null
     */
    public function buyer(MarketplaceOrder $order): ?array
    {
        $order->loadMissing('buyer');

        if ($demo = $this->demoAccountLocation(
            'buyer',
            $order->buyer?->email
        )) {
            return $demo;
        }

        $candidates = [];

        if (filled($order->buyer_address)) {
            $candidates[] = trim((string) $order->buyer_address)
                . ', Philippines';
        }

        if ($order->buyer instanceof BuyerAccount) {
            $candidates = array_merge(
                $candidates,
                $this->structuredCandidates(
                    $order->buyer->street_address,
                    $order->buyer->barangay_name,
                    $order->buyer->municipality_name,
                    $order->buyer->province_name
                )
            );
        }

        return $this->resolveCandidates($candidates);
    }

    /**
     * @return array{latitude: float, longitude: float, resolved_query: string}|null
     */
    public function seller(MarketplaceOrder $order): ?array
    {
        $order->loadMissing('seller');

        if ($demo = $this->demoAccountLocation(
            'seller',
            $order->seller?->email
        )) {
            return $demo;
        }

        $candidates = [];

        if (filled($order->pickup_address)) {
            $candidates[] = trim((string) $order->pickup_address)
                . ', Philippines';
        }

        if ($order->seller instanceof SellerAccount) {
            $pickup = trim((string) (
                $order->pickup_address
                ?: $order->seller->pickup_address
            ));

            $candidates = array_merge(
                $candidates,
                $this->structuredCandidates(
                    $pickup ?: $order->seller->street_address,
                    $order->seller->barangay_name,
                    $order->seller->municipality_name,
                    $order->seller->province_name
                )
            );
        }

        return $this->resolveCandidates($candidates);
    }

    /**
     * Resolve exact coordinates for built-in test accounts without making an
     * external geocoding request.
     *
     * @return array{latitude: float, longitude: float, resolved_query: string}|null
     */
    private function demoAccountLocation(
        string $role,
        mixed $email
    ): ?array {
        if (! config('sari_demo_locations.enabled', true)) {
            return null;
        }

        $demo = config("sari_demo_locations.accounts.{$role}");

        if (
            ! is_array($demo)
            || strtolower(trim((string) $email))
                !== strtolower((string) ($demo['email'] ?? ''))
        ) {
            return null;
        }

        $lat = $demo['latitude'] ?? null;
        $lng = $demo['longitude'] ?? null;

        if (! $this->isPhilippineCoordinate($lat, $lng)) {
            return null;
        }

        return [
            'latitude' => (float) $lat,
            'longitude' => (float) $lng,
            'resolved_query' => (string) (
                $demo['full_address'] ?? 'SARI demo location'
            ),
        ];
    }

    /**
     * Build increasingly broad candidates from the exact location saved during
     * account registration. If an artificial/test street or barangay is not
     * searchable, the resolver gracefully falls back to the municipality.
     *
     * @return array<int, string>
     */
    private function structuredCandidates(
        mixed $street,
        mixed $barangay,
        mixed $municipality,
        mixed $province
    ): array {
        $street = trim((string) $street);
        $barangay = trim((string) $barangay);
        $municipality = trim((string) $municipality);
        $province = trim((string) $province);

        $candidates = [];

        $this->pushCandidate($candidates, [
            $street,
            $barangay,
            $municipality,
            $province,
            'Philippines',
        ]);

        $this->pushCandidate($candidates, [
            $barangay,
            $municipality,
            $province,
            'Philippines',
        ]);

        $this->pushCandidate($candidates, [
            $municipality,
            $province,
            'Philippines',
        ]);

        $this->pushCandidate($candidates, [
            $province,
            'Philippines',
        ]);

        return array_values(array_unique($candidates));
    }

    /**
     * @param  array<int, string>  $candidates
     * @return array{latitude: float, longitude: float, resolved_query: string}|null
     */
    private function resolveCandidates(array $candidates): ?array
    {
        foreach ($candidates as $query) {
            if ($query === '' || $query === 'Philippines') {
                continue;
            }

            $result = $this->resolve($query);

            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    /**
     * @return array{latitude: float, longitude: float, resolved_query: string}|null
     */
    private function resolve(string $query): ?array
    {
        $cacheKey = 'sari.geocode.ph.v1.'
            . sha1(mb_strtolower($query));

        return Cache::remember(
            $cacheKey,
            now()->addDays(30),
            function () use ($query): ?array {
                try {
                    $response = Http::acceptJson()
                        ->withHeaders([
                            'User-Agent' => 'SARI-Marketplace/1.0 location-resolver',
                            'Accept-Language' => 'en',
                        ])
                        ->connectTimeout(4)
                        ->timeout(8)
                        ->retry(1, 250, throw: false)
                        ->get(self::ENDPOINT, [
                            'q' => $query,
                            'format' => 'jsonv2',
                            'limit' => 1,
                            'countrycodes' => 'ph',
                            'addressdetails' => 0,
                        ]);

                    if (! $response->successful()) {
                        return null;
                    }

                    $item = collect($response->json())->first();

                    if (! is_array($item)) {
                        return null;
                    }

                    $lat = $item['lat'] ?? null;
                    $lng = $item['lon'] ?? null;

                    if (! $this->isPhilippineCoordinate($lat, $lng)) {
                        return null;
                    }

                    return [
                        'latitude' => round((float) $lat, 7),
                        'longitude' => round((float) $lng, 7),
                        'resolved_query' => $query,
                    ];
                } catch (Throwable $e) {
                    Log::warning('SARI address geocoding failed.', [
                        'query' => $query,
                        'error' => $e->getMessage(),
                    ]);

                    return null;
                }
            }
        );
    }

    /**
     * @param  array<int, string>  $target
     * @param  array<int, mixed>  $parts
     */
    private function pushCandidate(
        array &$target,
        array $parts
    ): void {
        $value = collect($parts)
            ->map(fn ($part) => trim((string) $part))
            ->filter()
            ->implode(', ');

        if ($value !== '') {
            $target[] = $value;
        }
    }
}