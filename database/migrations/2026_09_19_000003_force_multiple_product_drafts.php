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

        // Remove every UNIQUE index whose only indexed column is
        // seller_account_id. Older versions of SARI used this constraint,
        // which limited each seller to one draft.
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
            $isUnique = (int) $index->NON_UNIQUE === 0;
            $columnCount = (int) $index->column_count;

            if (
                $name !== 'PRIMARY'
                && $isUnique
                && $columnCount === 1
                && $columns === 'seller_account_id'
            ) {
                $quotedName = str_replace('`', '``', $name);
                DB::statement(
                    "ALTER TABLE `seller_product_drafts` DROP INDEX `{$quotedName}`"
                );
            }
        }

        // Ensure seller_account_id still has a normal lookup index.
        $lookupExists = collect(DB::select(
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

        if (!$lookupExists) {
            DB::statement(
                'CREATE INDEX `seller_product_drafts_seller_account_lookup_v2` '
                . 'ON `seller_product_drafts` (`seller_account_id`)'
            );
        }
    }

    public function down(): void
    {
        // Intentionally no-op: restoring the old UNIQUE constraint could fail
        // once a seller legitimately has multiple drafts.
    }
};
