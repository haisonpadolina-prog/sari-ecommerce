<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_applications', function (Blueprint $table): void {
            if (!Schema::hasColumn('registration_applications', 'region_code')) {
                $table->string('region_code', 30)->nullable()->after('age');
            }
            if (!Schema::hasColumn('registration_applications', 'region_name')) {
                $table->string('region_name')->nullable()->after('region_code');
            }
        });

        Schema::table('seller_accounts', function (Blueprint $table): void {
            if (!Schema::hasColumn('seller_accounts', 'region_code')) {
                $table->string('region_code', 30)->nullable()->after('age');
            }
            if (!Schema::hasColumn('seller_accounts', 'region_name')) {
                $table->string('region_name')->nullable()->after('region_code');
            }
        });

        Schema::table('logistics_accounts', function (Blueprint $table): void {
            if (!Schema::hasColumn('logistics_accounts', 'region_code')) {
                $table->string('region_code', 30)->nullable()->after('age');
            }
            if (!Schema::hasColumn('logistics_accounts', 'region_name')) {
                $table->string('region_name')->nullable()->after('region_code');
            }
        });

        // The workbook does not require sex/address/documents for Buyers, and
        // does not require sex/DOB for Seller or Logistics registration. These
        // columns remain available for existing profile/account workflows but
        // become nullable so the registration schema no longer contradicts
        // the role-specific black-box requirements.
        Schema::table('registration_applications', function (Blueprint $table): void {
            $table->string('sex', 20)->nullable()->change();
            $table->date('birthday')->nullable()->change();
            $table->unsignedTinyInteger('age')->nullable()->change();
            $table->string('province_code', 30)->nullable()->change();
            $table->string('province_name')->nullable()->change();
            $table->string('municipality_code', 30)->nullable()->change();
            $table->string('municipality_name')->nullable()->change();
            $table->string('barangay_code', 30)->nullable()->change();
            $table->string('barangay_name')->nullable()->change();
            $table->text('street_address')->nullable()->change();
        });

        Schema::table('buyer_accounts', function (Blueprint $table): void {
            $table->string('sex', 20)->nullable()->change();
            $table->string('province_code', 30)->nullable()->change();
            $table->string('province_name')->nullable()->change();
            $table->string('municipality_code', 30)->nullable()->change();
            $table->string('municipality_name')->nullable()->change();
            $table->string('barangay_code', 30)->nullable()->change();
            $table->string('barangay_name')->nullable()->change();
            $table->text('street_address')->nullable()->change();
        });

        Schema::table('logistics_accounts', function (Blueprint $table): void {
            $table->string('sex', 20)->nullable()->change();
            $table->date('birthday')->nullable()->change();
            $table->unsignedTinyInteger('age')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Deliberately non-destructive. Existing deployments may already contain
        // workbook-compliant records with null values, so reverting nullability
        // or dropping region data could make rollback unsafe.
    }
};
