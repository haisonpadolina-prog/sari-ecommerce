<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [
            'registration_application_id',
            'last_name',
            'first_name',
            'middle_initial',
            'sex',
            'contact_no',
            'birthday',
            'age',
            'province_code',
            'province_name',
            'municipality_code',
            'municipality_name',
            'barangay_code',
            'barangay_name',
            'street_address',
            'password',
            'line_of_business',
            'id_path',
            'business_permit_path',
            'registration_status',
            'approved_at',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('seller_accounts', $column)) {
                continue;
            }

            Schema::table('seller_accounts', function (Blueprint $table) use ($column) {
                match ($column) {
                    'registration_application_id' =>
                        $table->unsignedBigInteger($column)->nullable()->unique(),

                    'birthday' =>
                        $table->date($column)->nullable(),

                    'age' =>
                        $table->unsignedTinyInteger($column)->nullable(),

                    'street_address' =>
                        $table->text($column)->nullable(),

                    'approved_at' =>
                        $table->timestamp($column)->nullable(),

                    'registration_status' =>
                        $table->string($column, 20)->default('approved')->index(),

                    'middle_initial' =>
                        $table->string($column, 5)->nullable(),

                    'sex' =>
                        $table->string($column, 20)->nullable(),

                    'contact_no' =>
                        $table->string($column, 30)->nullable(),

                    'province_code',
                    'municipality_code',
                    'barangay_code' =>
                        $table->string($column, 30)->nullable(),

                    default =>
                        $table->string($column)->nullable(),
                };
            });
        }

        // account_status may already exist from Seller Account Control.
        if (!Schema::hasColumn('seller_accounts', 'account_status')) {
            Schema::table('seller_accounts', function (Blueprint $table) {
                $table->string('account_status', 20)->default('active')->index();
            });
        }
    }

    public function down(): void
    {
        // Intentionally non-destructive: this migration integrates with an
        // existing Seller compliance table. We do not drop columns in down().
    }
};
