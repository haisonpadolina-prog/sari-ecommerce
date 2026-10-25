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
                '/^(?:barangay|brgy\.?)\s+/iu',
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

        foreach ($parts as $part) {
            if (
                preg_match(
                    '/^(?:barangay|brgy\.?)\s+(.+)$/iu',
                    $part,
                    $matches
                )
            ) {
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
        return self::municipality($rider->municipality_name)
                === self::municipality(
                    $destination->destination_municipality
                )
            && self::normalize($rider->province_name)
                === self::normalize(
                    $destination->destination_province
                )
            && self::barangay($rider->barangay_name)
                === self::barangay(
                    $destination->barangay_name
                );
    }
}