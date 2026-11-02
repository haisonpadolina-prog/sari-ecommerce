<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * A deterministic, unique LOGICAL parcel staging reference.
 *
 * This does not reserve or verify a physical shelf/bin. Physical rack
 * allocation requires a separately configured facility inventory.
 *
 * AUTO-LOCAL-H0002-P000123  => same-hub last-mile staging reference
 * AUTO-XFER-H0005-P000123   => inter-hub staging reference grouped by destination
 *
 * Parcel primary keys are unique; therefore two parcels never share a
 * generated reference, and re-computation is stable.
 */
final class ParcelSortingLocator
{
    public static function forParcel(int $parcelId, int $originHubId, int $destinationHubId): string
    {
        if ($parcelId < 1 || $originHubId < 1 || $destinationHubId < 1) {
            throw new InvalidArgumentException('Valid parcel and hub IDs are required.');
        }

        $leg = $originHubId === $destinationHubId ? 'LOCAL' : 'XFER';

        return 'AUTO-'.$leg
            .'-H'.str_pad((string) $destinationHubId, 4, '0', STR_PAD_LEFT)
            .'-P'.str_pad((string) $parcelId, 6, '0', STR_PAD_LEFT);
    }

    public static function isGenerated(?string $locator): bool
    {
        return is_string($locator)
            && preg_match('/^AUTO-(?:LOCAL|XFER)-H[0-9]+-P[0-9]+$/D', $locator) === 1;
    }
}
