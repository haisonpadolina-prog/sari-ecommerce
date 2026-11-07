<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * Synthetic, non-production seller test identities. This is deliberately
 * independent of the newer users/sellers catalog system: SARI's seller login
 * authenticates against seller_accounts and uses line_of_business as its
 * product category.
 */
final class SariDemoSellerBlueprint
{
    public const COUNT = 250;

    /** Exactly the nine options in layouts/seller-registration.blade.php. */
    public const CATEGORIES = [
        'Electronics',
        'Fashion',
        'Home & Living',
        'Beauty',
        'Books',
        'Food & Beverage',
        'Jewelry & Watches',
        'Furniture & Office',
        'Others',
    ];

    private const FIRST_NAMES = [
        'Alex', 'Andrea', 'Bianca', 'Carlo', 'Celine',
        'Daniel', 'Diana', 'Emilio', 'Erika', 'Felix',
        'Gabriela', 'Hannah', 'Isaac', 'Jasmine', 'Joaquin',
        'Katrina', 'Liam', 'Mara', 'Nico', 'Olivia',
        'Paolo', 'Rhea', 'Sofia', 'Tomas', 'Yana',
    ];

    private const LAST_NAMES = [
        'Aguilar', 'Bautista', 'Castillo', 'Dela Cruz', 'Estrada',
        'Flores', 'Garcia', 'Mendoza', 'Reyes', 'Santos',
    ];

    public static function account(int $sequence): array
    {
        if ($sequence < 1 || $sequence > self::COUNT) {
            throw new InvalidArgumentException('Demo seller sequence must be between 1 and 250.');
        }

        $number = sprintf('%03d', $sequence);
        $category = self::CATEGORIES[($sequence - 1) % count(self::CATEGORIES)];
        $firstName = self::FIRST_NAMES[($sequence - 1) % count(self::FIRST_NAMES)];
        $lastName = self::LAST_NAMES[intdiv($sequence - 1, count(self::FIRST_NAMES))];

        return [
            'email' => "qa.seller.{$number}@sari-demo.test",
            'first_name' => $firstName,
            'last_name' => $lastName,
            'store_name' => "SARI DEMO {$number} | {$category}",
            'line_of_business' => $category,
            'store_description' => "[SARI_250_DEMO_SELLERS] Synthetic {$category} seller for internal testing only. Not a real or verified merchant; no government documents, inventory, or pickup service.",
        ];
    }

    /** Unique, reproducible credentials derived without saving passwords in files. */
    public static function password(string $email, string $masterSecret): string
    {
        return 'SARI-QA-' . substr(hash_hmac('sha256', strtolower(trim($email)), $masterSecret), 0, 40) . '-9!';
    }

    public static function assertStrongMasterSecret(?string $secret): string
    {
        $secret = (string) $secret;

        if (strlen($secret) < 32 || count(array_unique(str_split($secret))) < 10) {
            throw new InvalidArgumentException(
                'Set SARI_DEMO_SELLER_MASTER_SECRET to a securely generated 32+ character secret (recommend: openssl rand -hex 32). Never commit it.'
            );
        }

        return $secret;
    }
}
