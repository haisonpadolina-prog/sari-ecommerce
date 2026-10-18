<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_hub_accounts', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Parent Logistics provider
            |--------------------------------------------------------------------------
            |
            | San Pablo and Sta. Cruz operators remain under the SAME Logistics
            | provider so existing logistics_account_id scoping is preserved.
            |
            */

            $table
                ->foreignId('logistics_account_id')
                ->constrained('logistics_accounts')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Assigned hub
            |--------------------------------------------------------------------------
            */

            $table
                ->foreignId('logistics_facility_id')
                ->constrained('logistics_facilities')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Operator identity
            |--------------------------------------------------------------------------
            */

            $table->string('name', 120);

            $table
                ->string('email')
                ->unique();

            $table->string('password');

            /*
            |--------------------------------------------------------------------------
            | Account role / status
            |--------------------------------------------------------------------------
            */

            $table
                ->string('role', 40)
                ->default('hub_operator');

            $table
                ->string('account_status', 30)
                ->default('active');

            $table->timestamp('last_login_at')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Operational indexes
            |--------------------------------------------------------------------------
            */

            $table->index([
                'logistics_account_id',
                'logistics_facility_id',
            ]);

            $table->index([
                'logistics_facility_id',
                'account_status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_hub_accounts');
    }
};