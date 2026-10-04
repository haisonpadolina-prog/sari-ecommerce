<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('seller_accounts')) {
            return;
        }

        $needsPickupAddress =
            !Schema::hasColumn(
                'seller_accounts',
                'pickup_address'
            );

        $needsStoreLogo =
            !Schema::hasColumn(
                'seller_accounts',
                'store_logo_path'
            );

        $needsStoreBanner =
            !Schema::hasColumn(
                'seller_accounts',
                'store_banner_path'
            );

        if (
            !$needsPickupAddress
            && !$needsStoreLogo
            && !$needsStoreBanner
        ) {
            return;
        }

        Schema::table(
            'seller_accounts',
            function (Blueprint $table) use (
                $needsPickupAddress,
                $needsStoreLogo,
                $needsStoreBanner
            ): void {
                if ($needsPickupAddress) {
                    $table->text(
                        'pickup_address'
                    )->nullable();
                }

                if ($needsStoreLogo) {
                    $table->string(
                        'store_logo_path'
                    )->nullable();
                }

                if ($needsStoreBanner) {
                    $table->string(
                        'store_banner_path'
                    )->nullable();
                }
            }
        );
    }

    public function down(): void
    {
        /*
        | Intentionally non-destructive.
        | Store media/pickup information may contain production Seller data.
        */
    }
};
