<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Create or repair the Hub Operator table
        |--------------------------------------------------------------------------
        |
        | MySQL limits identifiers to 64 characters. The original migration used
        | Laravel's generated name for a composite index, which exceeded that
        | limit. MySQL may therefore leave the table created while the migration
        | itself remains unrecorded. This migration is intentionally idempotent:
        | it creates the table when absent, or only adds the missing short-named
        | indexes when a partial table already exists.
        |
        */
        if (! Schema::hasTable('logistics_hub_accounts')) {
            Schema::create('logistics_hub_accounts', function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('logistics_account_id')
                    ->constrained('logistics_accounts')
                    ->cascadeOnDelete();

                $table
                    ->foreignId('logistics_facility_id')
                    ->constrained('logistics_facilities')
                    ->restrictOnDelete();

                $table->string('name', 120);
                $table->string('email')->unique();
                $table->string('password');

                $table
                    ->string('role', 40)
                    ->default('hub_operator');

                $table
                    ->string('account_status', 30)
                    ->default('active');

                $table->timestamp('last_login_at')->nullable();
                $table->timestamps();

                $table->index(
                    ['logistics_account_id', 'logistics_facility_id'],
                    'lg_hub_provider_facility_idx'
                );

                $table->index(
                    ['logistics_facility_id', 'account_status'],
                    'lg_hub_facility_status_idx'
                );
            });

            return;
        }

        // Repair the partial table left by the failed MySQL index creation.
        if (! Schema::hasIndex('logistics_hub_accounts', 'lg_hub_provider_facility_idx')) {
            Schema::table('logistics_hub_accounts', function (Blueprint $table): void {
                $table->index(
                    ['logistics_account_id', 'logistics_facility_id'],
                    'lg_hub_provider_facility_idx'
                );
            });
        }

        if (! Schema::hasIndex('logistics_hub_accounts', 'lg_hub_facility_status_idx')) {
            Schema::table('logistics_hub_accounts', function (Blueprint $table): void {
                $table->index(
                    ['logistics_facility_id', 'account_status'],
                    'lg_hub_facility_status_idx'
                );
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_hub_accounts');
    }
};
