<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('seller_product_drafts')) {
            return;
        }

        $database = DB::getDatabaseName();

        $indexes = DB::select(
            <<<'SQL'
                SELECT
                    INDEX_NAME,
                    NON_UNIQUE,
                    GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) AS columns_list,
                    COUNT(*) AS column_count
                FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = ?
                  AND TABLE_NAME = 'seller_product_drafts'
                GROUP BY INDEX_NAME, NON_UNIQUE
            SQL,
            [$database]
        );

        foreach ($indexes as $index) {
            $name = (string) $index->INDEX_NAME;
            $columns = strtolower((string) $index->columns_list);

            if (
                $name !== 'PRIMARY'
                && (int) $index->NON_UNIQUE === 0
                && (int) $index->column_count === 1
                && $columns === 'seller_account_id'
            ) {
                $safeName = str_replace('`', '``', $name);

                DB::statement(
                    "ALTER TABLE `seller_product_drafts` DROP INDEX `{$safeName}`"
                );
            }
        }

        // Keep a normal non-unique lookup index for seller draft queries.
        $normalIndexExists = collect(DB::select(
            <<<'SQL'
                SELECT INDEX_NAME
                FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = ?
                  AND TABLE_NAME = 'seller_product_drafts'
                  AND COLUMN_NAME = 'seller_account_id'
                  AND NON_UNIQUE = 1
                LIMIT 1
            SQL,
            [$database]
        ))->isNotEmpty();

        if (!$normalIndexExists) {
            DB::statement(
                'CREATE INDEX `seller_product_drafts_seller_lookup_v3` '
                . 'ON `seller_product_drafts` (`seller_account_id`)'
            );
        }
    }

    public function down(): void
    {
        // No-op. Re-adding the old UNIQUE constraint would destroy the
        // multiple-drafts feature once sellers have more than one draft.
    }
};
