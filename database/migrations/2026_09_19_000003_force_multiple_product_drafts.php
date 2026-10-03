<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('seller_product_drafts')) {
            return;
        }

        $indexes = Schema::getIndexes('seller_product_drafts');
        $lookupExists = false;

        foreach ($indexes as $index) {
            if (!$index['unique'] && ($index['columns'][0] ?? null) === 'seller_account_id') {
                $lookupExists = true;
                break;
            }
        }

        // MySQL needs a supporting index before an index used by a foreign key can be removed.
        if (!$lookupExists) {
            Schema::table('seller_product_drafts', function (Blueprint $table): void {
                $table->index('seller_account_id', 'seller_product_drafts_seller_account_lookup_v2');
            });
        }

        foreach ($indexes as $index) {
            if (
                !$index['primary']
                && $index['unique']
                && $index['columns'] === ['seller_account_id']
            ) {
                Schema::table('seller_product_drafts', function (Blueprint $table) use ($index): void {
                    $table->dropUnique($index['name']);
                });
            }
        }
    }

    public function down(): void
    {
        // Restoring uniqueness would reject sellers who already have multiple drafts.
    }
};
