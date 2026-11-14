<?php

namespace App\Support;

use App\Models\Accounts\CourierAccount;

final class LogisticsServiceArea
{
    public static function normalize(?string $value): string
    {
        return mb_strtolower(trim(
            preg_replace('/\s+/u', ' ', (string) $value) ?? ''
        ));
    }

    public static function barangay(?string $value): string
    {
        return self::normalize(
            preg_replace(
                '/^(?:barangay|brgy\.?|bgy\.?)\s+/iu',
                '',
                trim((string) $value)
            )
        );
    }

    public static function municipality(?string $value): string
    {
        $name = preg_replace(
            '/[^\pL\pN]+/u',
            ' ',
            self::normalize($value)
        ) ?? '';

        $name = preg_replace(
            '/\b(?:municipality of|city of|city)\b/u',
            ' ',
            $name
        ) ?? $name;

        $name = preg_replace(
            '/\bsta\b/u',
            'santa',
            $name
        ) ?? $name;

        $name = preg_replace(
            '/\bsto\b/u',
            'santo',
            $name
        ) ?? $name;

        return self::normalize($name);
    }

    public static function fromAddress(
        ?string $address,
        ?string $municipality = null
    ): string {
        $parts = array_values(
            array_filter(
                array_map(
                    'trim',
                    explode(',', (string) $address)
                ),
                fn ($part) => $part !== ''
            )
        );

        foreach ($parts as $index => $part) {
            if (
                preg_match(
                    '/^(?:barangay|brgy\.?|bgy\.?)\s+(.+)$/iu',
                    $part,
                    $matches
                )
            ) {
                // Numbered barangays may contain a comma in their official name,
                // e.g. Bgy. No. 42, Apaya. Bound the continuation by the known city.
                if ($municipality && preg_match('/^(?:no\.?\s*)?\d+\b/iu', trim($matches[1]))) {
                    foreach ($parts as $cityIndex => $cityPart) {
                        if ($cityIndex > $index && self::municipality($cityPart) === self::municipality($municipality)) {
                            $nameParts = array_slice($parts, $index, $cityIndex - $index);
                            $nameParts[0] = trim($matches[1]);

                            return implode(', ', $nameParts);
                        }
                    }
                }

                return trim($matches[1]);
            }
        }

        foreach ($parts as $index => $part) {
            if (
                $index > 0
                && self::municipality($part)
                    === self::municipality($municipality)
            ) {
                return $parts[$index - 1];
            }
        }

        return $parts[0] ?? 'Unknown Area';
    }

    public static function matchesRider(
        CourierAccount $rider,
        object $destination
    ): bool {
        $province = (string) ($rider->duty_province_name ?: $rider->province_name);
        $municipality = (string) ($rider->duty_municipality_name ?: $rider->municipality_name);
        $barangay = (string) ($rider->duty_barangay_name ?: $rider->barangay_name);

        if (self::normalize($province) === '' || self::municipality($municipality) === '' || self::barangay($barangay) === '') {
            return false;
        }

        return self::municipality($municipality)
                === self::municipality($destination->destination_municipality ?? null)
            && self::normalize($province)
                === self::normalize($destination->destination_province ?? null)
            && self::barangay($barangay)
                === self::barangay($destination->barangay_name ?? null);
    }

    /**
     * Match the Seller's pickup barangay, city and province -- NOT the hub's.
     * Hubs may serve neighboring cities, so hub coordinates cannot define pickup duty.
     * Accept only parseable comma-delimited PH addresses; never invent a barangay.
     */
    public static function matchesPickupRider(
        CourierAccount $rider,
        ?string $pickupAddress
    ): bool {
        if (blank($pickupAddress)) {
            return false;
        }

        $parts = array_values(array_filter(array_map('trim', explode(',', $pickupAddress)), fn ($part) => $part !== ''));
        if ($parts && self::normalize(end($parts)) === 'philippines') {
            array_pop($parts);
        }
        // Minimum: Barangay, municipality, province.
        if (count($parts) < 3) {
            return false;
        }
        $province = array_pop($parts);
        $municipality = array_pop($parts);
        $barangay = self::fromAddress(
            implode(', ', array_merge($parts, [$municipality, $province])),
            $municipality
        );
        if (self::barangay($barangay) === '' || self::municipality($municipality) === '' || self::normalize($province) === '') {
            return false;
        }

        return self::matchesRider($rider, (object) [
            'destination_province' => $province,
            'destination_municipality' => $municipality,
            'barangay_name' => $barangay,
        ]);
    }
}
