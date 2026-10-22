<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_applications', function (Blueprint $table): void {
            $table->string('seller_type', 30)->nullable()->after('business_name');
            $table->string('tin', 20)->nullable()->unique()->after('seller_type');
            $table->string('sec_registration_no', 20)->nullable()->unique()->after('tin');
            $table->string('postal_code', 4)->nullable()->after('street_address');
            $table->string('position', 120)->nullable()->after('line_of_business');
            $table->text('service_areas')->nullable()->after('position');
            $table->string('bir_path')->nullable()->after('business_permit_path');
            $table->string('sec_path')->nullable()->after('bir_path');
        });

        foreach (['seller_accounts', 'logistics_accounts'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->string('tin', 20)->nullable()->unique();
                $table->string('sec_registration_no', 20)->nullable()->unique();
                $table->string('postal_code', 4)->nullable();
            });
        }

        Schema::table('seller_accounts', function (Blueprint $table): void {
            $table->string('seller_type', 30)->nullable();
            $table->string('bir_path')->nullable();
            $table->string('sec_path')->nullable();
        });

        Schema::table('logistics_accounts', function (Blueprint $table): void {
            $table->string('position', 120)->nullable();
            $table->text('service_areas')->nullable();
            $table->string('bir_path')->nullable();
            $table->string('sec_path')->nullable();
        });

        Schema::table('courier_accounts', function (Blueprint $table): void {
            $table->date('license_expiry_date')->nullable()->after('license_number');
            $table->string('driver_license_path')->nullable()->after('id_path');
            $table->string('activation_token_hash', 64)->nullable()->unique();
            $table->timestamp('activation_expires_at')->nullable();
            $table->timestamp('activated_at')->nullable();
        });
    }

    public function down(): void
    {
        // Non-destructive by design. Registration/account history must be preserved.
    }
};
