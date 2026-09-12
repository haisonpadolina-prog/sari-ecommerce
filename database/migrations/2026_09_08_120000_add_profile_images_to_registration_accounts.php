<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'registration_applications',
            'buyer_accounts',
            'seller_accounts',
            'courier_accounts',
            'logistics_accounts',
        ] as $tableName) {
            if (
                Schema::hasTable($tableName)
                && !Schema::hasColumn($tableName, 'profile_image_path')
            ) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->string('profile_image_path')->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ([
            'registration_applications',
            'buyer_accounts',
            'seller_accounts',
            'courier_accounts',
            'logistics_accounts',
        ] as $tableName) {
            if (
                Schema::hasTable($tableName)
                && Schema::hasColumn($tableName, 'profile_image_path')
            ) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->dropColumn('profile_image_path');
                });
            }
        }
    }
};
