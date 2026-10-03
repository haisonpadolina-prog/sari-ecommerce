<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

test('draft repair migrations remove legacy uniqueness and can run twice', function (string $filename): void {
    Schema::table('seller_product_drafts', function (Blueprint $table): void {
        $table->unique('seller_account_id', 'legacy_draft_seller_unique');
    });

    $migration = require database_path('migrations/'.$filename);
    $migration->up();
    $migration->up();

    expect(Schema::hasIndex('seller_product_drafts', 'legacy_draft_seller_unique'))->toBeFalse();

    $lookupFound = false;

    foreach (Schema::getIndexes('seller_product_drafts') as $index) {
        if (!$index['unique'] && ($index['columns'][0] ?? null) === 'seller_account_id') {
            $lookupFound = true;
        }

        expect($index['unique'] && $index['columns'] === ['seller_account_id'])->toBeFalse();
    }

    expect($lookupFound)->toBeTrue();
})->with([
    '2026_09_19_000003_force_multiple_product_drafts.php',
    '2026_09_19_000004_guarantee_multiple_product_drafts.php',
]);

test('named draft index migration can run twice', function (): void {
    $migration = require database_path('migrations/2026_09_19_000002_enable_multiple_product_drafts.php');
    $migration->up();
    $migration->up();

    expect(Schema::hasIndex('seller_product_drafts', 'seller_product_drafts_seller_account_id_unique'))->toBeFalse()
        ->and(Schema::hasIndex('seller_product_drafts', 'seller_product_drafts_seller_account_lookup'))->toBeTrue();
});
