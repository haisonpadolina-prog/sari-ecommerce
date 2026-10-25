
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_notifications', function (Blueprint $table): void {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Parent Logistics Network
            |--------------------------------------------------------------------------
            */

            $table->foreignId('logistics_account_id')
                ->constrained('logistics_accounts')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Recipient Identity
            |--------------------------------------------------------------------------
            |
            | Main Manager:
            | logistics_hub_account_id = null
            | recipient_type = manager
            | recipient_key = manager
            |
            | Hub Operator:
            | logistics_hub_account_id = actual operator ID
            | recipient_type = hub_operator
            | recipient_key = hub:{operator_id}
            |
            | A notification service will validate these relationships
            | before creating or accessing notifications.
            |
            */

            $table->foreignId('logistics_hub_account_id')
                ->nullable()
                ->constrained('logistics_hub_accounts')
                ->cascadeOnDelete();

            $table->string('recipient_type', 20);

            $table->string('recipient_key', 64);

            /*
            |--------------------------------------------------------------------------
            | Notification Content
            |--------------------------------------------------------------------------
            */

            $table->string('type', 50);

            $table->string('title', 160);

            $table->text('message')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Internal Navigation
            |--------------------------------------------------------------------------
            |
            | Store an internal route name, not an arbitrary URL.
            | Allowed destinations will be validated by the service.
            |
            */

            $table->string('action_route', 120)->nullable();

            $table->json('action_params')->nullable();

            $table->json('data')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Duplicate Event Protection
            |--------------------------------------------------------------------------
            |
            | An event_key allows the same event to be delivered once
            | per recipient. Independent notifications may leave it null.
            |
            */

            $table->string('event_key', 100)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Persistent Read State
            |--------------------------------------------------------------------------
            */

            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Query Indexes
            |--------------------------------------------------------------------------
            */

            $table->index(
                [
                    'logistics_account_id',
                    'recipient_key',
                    'read_at',
                    'created_at',
                ],
                'log_notif_mailbox_unread_idx'
            );

            $table->unique(
                [
                    'logistics_account_id',
                    'recipient_key',
                    'event_key',
                ],
                'log_notif_recipient_event_uq'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_notifications');
    }
};
