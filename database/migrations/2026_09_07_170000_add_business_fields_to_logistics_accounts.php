<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('logistics_accounts', 'business_name')) {
            Schema::table('logistics_accounts', function (Blueprint $table): void {
                $table->string('business_name')->nullable()->after('street_address');
            });
        }

        if (!Schema::hasColumn('logistics_accounts', 'business_permit_path')) {
            Schema::table('logistics_accounts', function (Blueprint $table): void {
                $table->string('business_permit_path')->nullable()->after('id_path');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('logistics_accounts', 'business_permit_path')) {
            Schema::table('logistics_accounts', function (Blueprint $table): void {
                $table->dropColumn('business_permit_path');
            });
        }

        if (Schema::hasColumn('logistics_accounts', 'business_name')) {
            Schema::table('logistics_accounts', function (Blueprint $table): void {
                $table->dropColumn('business_name');
            });
        }
    }
};
