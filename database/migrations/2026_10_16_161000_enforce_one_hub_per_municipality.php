<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logistics_facilities', function (Blueprint $table) {
            /*
            |--------------------------------------------------------------------------
            | One Hub Per Municipality
            |--------------------------------------------------------------------------
            |
            | A Logistics provider may only maintain one sorting hub for each
            | municipality/city.
            |
            | Examples:
            |
            | SARI Logistics + San Pablo City = one hub
            | SARI Logistics + Sta. Cruz      = one hub
            |
            */

            $table->unique(
                [
                    'logistics_account_id',
                    'municipality_name',
                ],
                'logistics_facilities_provider_municipality_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('logistics_facilities', function (Blueprint $table) {
            $table->dropUnique(
                'logistics_facilities_provider_municipality_unique'
            );
        });
    }
};