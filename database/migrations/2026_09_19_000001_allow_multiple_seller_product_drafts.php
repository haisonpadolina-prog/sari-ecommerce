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

        Schema::table('seller_product_drafts', function (Blueprint $table) {
            // Add a normal lookup index first so the foreign key remains
            // supported after removing the old one-draft-per-seller UNIQUE key.
            $table->index(
                'seller_account_id',
                'seller_product_drafts_seller_account_lookup'
            );
        });

        Schema::table('seller_product_drafts', function (Blueprint $table) {
            $table->dropUnique(
                'seller_product_drafts_seller_account_id_unique'
            );
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('seller_product_drafts')) {
            return;
        }

        Schema::table('seller_product_drafts', function (Blueprint $table) {
            $table->unique(
                'seller_account_id',
                'seller_product_drafts_seller_account_id_unique'
            );

            $table->dropIndex(
                'seller_product_drafts_seller_account_lookup'
            );
        });
    }
};
