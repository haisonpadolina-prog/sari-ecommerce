<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'logistics_facilities',
            function (Blueprint $table): void {
                $table->dropUnique(
                    'logistics_facilities_provider_municipality_unique'
                );

                $table->unique(
                    [
                        'logistics_account_id',
                        'province_name',
                        'municipality_name',
                    ],
                    'logistics_facilities_provider_province_municipality_unique'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'logistics_facilities',
            function (Blueprint $table): void {
                $table->dropUnique(
                    'logistics_facilities_provider_province_municipality_unique'
                );

                $table->unique(
                    [
                        'logistics_account_id',
                        'municipality_name',
                    ],
                    'logistics_facilities_provider_municipality_unique'
                );
            }
        );
    }
};