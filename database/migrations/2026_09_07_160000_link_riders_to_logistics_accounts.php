<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_applications', function (Blueprint $table): void {
            $table->foreignId('logistics_account_id')
                ->nullable()
                ->after('role')
                ->constrained('logistics_accounts')
                ->nullOnDelete();

            $table->index(['logistics_account_id', 'role', 'status'], 'reg_apps_logistics_role_status_idx');
        });

        Schema::table('courier_accounts', function (Blueprint $table): void {
            $table->foreignId('logistics_account_id')
                ->nullable()
                ->after('registration_application_id')
                ->constrained('logistics_accounts')
                ->nullOnDelete();

            $table->index(['logistics_account_id', 'account_status'], 'couriers_logistics_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('courier_accounts', function (Blueprint $table): void {
            $table->dropIndex('couriers_logistics_status_idx');
            $table->dropConstrainedForeignId('logistics_account_id');
        });

        Schema::table('registration_applications', function (Blueprint $table): void {
            $table->dropIndex('reg_apps_logistics_role_status_idx');
            $table->dropConstrainedForeignId('logistics_account_id');
        });
    }
};
