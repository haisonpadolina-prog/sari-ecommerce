<?php
/**
 * CLI helper: inspect ONE demo seller login without storing/exporting secrets.
 * Usage: SARI_DEMO_SELLER_MASTER_SECRET='...' php scripts/sari_demo_seller_credentials.php 1
 */
require_once dirname(__DIR__).'/app/Support/SariDemoSellerBlueprint.php';

use App\Support\SariDemoSellerBlueprint;

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only\n");
}

try {
    $index = $argv[1] ?? null;
    if (!is_string($index) || !preg_match('/^[1-9]\d{0,2}$/D', $index)) {
        throw new InvalidArgumentException('Supply one seller number (1 to 250), e.g. 1 or 250.');
    }

    $secret = SariDemoSellerBlueprint::assertStrongMasterSecret(
        getenv('SARI_DEMO_SELLER_MASTER_SECRET') ?: null
    );

    $account = SariDemoSellerBlueprint::account((int) $index);
    echo 'Store: '.$account['store_name'].PHP_EOL;
    echo 'Category: '.$account['line_of_business'].PHP_EOL;
    echo 'Email: '.$account['email'].PHP_EOL;
    echo 'Password: '.SariDemoSellerBlueprint::password($account['email'], $secret).PHP_EOL;
    echo "Use only in a private QA shell. Do not share or commit this output.\n";
} catch (Throwable $error) {
    fwrite(STDERR, $error->getMessage().PHP_EOL);
    exit(1);
}
