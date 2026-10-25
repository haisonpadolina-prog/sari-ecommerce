
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_activity_logs', function (Blueprint $table): void {

            /*
            |--------------------------------------------------------------------------
            | Primary Key
            |--------------------------------------------------------------------------
            */

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Parent Logistics Provider
            |--------------------------------------------------------------------------
            */

            $table->foreignId('logistics_account_id')
                ->constrained('logistics_accounts')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Actor Information
            |--------------------------------------------------------------------------
            |
            | Store the operator's identity as a snapshot.
            | Historical records remain readable even when
            | the operator's name changes.
            |
            */

            $table->string('actor_type', 20);

            $table->unsignedBigInteger('actor_id');

            $table->string('actor_name', 160);

            /*
            |--------------------------------------------------------------------------
            | Hub Information
            |--------------------------------------------------------------------------
            |
            | Nullable because some Main Logistics actions
            | are network-wide and not tied to a single hub.
            |
            */

            $table->unsignedBigInteger('logistics_facility_id')
                ->nullable();

            $table->string('facility_name', 160)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Activity Details
            |--------------------------------------------------------------------------
            */

            $table->string('module', 45);

            $table->string('action', 55);

            $table->string('description', 240);

            /*
            |--------------------------------------------------------------------------
            | Related Record
            |--------------------------------------------------------------------------
            |
            | Example:
            | subject_type = order
            | subject_id   = 15
            |
            */

            $table->string('subject_type', 45)
                ->nullable();

            $table->unsignedBigInteger('subject_id')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Timestamp
            |--------------------------------------------------------------------------
            |
            | Activity history only needs created_at.
            | No updated_at because records are read-only.
            |
            */

            $table->timestamp('created_at')
                ->useCurrent();

            /*
            |--------------------------------------------------------------------------
            | Database Indexes
            |--------------------------------------------------------------------------
            */

            $table->index(
                ['logistics_account_id', 'created_at'],
                'lg_activity_provider_time_idx'
            );

            $table->index(
                [
                    'logistics_account_id',
                    'logistics_facility_id',
                    'created_at',
                ],
                'lg_activity_hub_time_idx'
            );

            $table->index(
                [
                    'logistics_account_id',
                    'module',
                    'created_at',
                ],
                'lg_activity_module_time_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_activity_logs');
    }
};
