<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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

        $uniqueExists = DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', 'seller_product_drafts')
            ->where(
                'index_name',
                'seller_product_drafts_seller_account_id_unique'
            )
            ->exists();

        $lookupExists = DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', 'seller_product_drafts')
            ->where(
                'index_name',
                'seller_product_drafts_seller_account_lookup'
            )
            ->exists();

        if (!$lookupExists) {
            Schema::table('seller_product_drafts', function (Blueprint $table) {
                $table->index(
                    'seller_account_id',
                    'seller_product_drafts_seller_account_lookup'
                );
            });
        }

        if ($uniqueExists) {
            Schema::table('seller_product_drafts', function (Blueprint $table) {
                $table->dropUnique(
                    'seller_product_drafts_seller_account_id_unique'
                );
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('seller_product_drafts')) {
            return;
        }

        $database = DB::getDatabaseName();

        $uniqueExists = DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', 'seller_product_drafts')
            ->where(
                'index_name',
                'seller_product_drafts_seller_account_id_unique'
            )
            ->exists();

        $lookupExists = DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', 'seller_product_drafts')
            ->where(
                'index_name',
                'seller_product_drafts_seller_account_lookup'
            )
            ->exists();

        if (!$uniqueExists) {
            Schema::table('seller_product_drafts', function (Blueprint $table) {
                $table->unique(
                    'seller_account_id',
                    'seller_product_drafts_seller_account_id_unique'
                );
            });
        }

        if ($lookupExists) {
            Schema::table('seller_product_drafts', function (Blueprint $table) {
                $table->dropIndex(
                    'seller_product_drafts_seller_account_lookup'
                );
            });
        }
    }
};
