<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buyer_account_activity_states', function (Blueprint $table): void {
            $table->id();
            $table->string('buyer_key', 80);
            $table->foreignId('admin_user_activity_id')->constrained('admin_user_activities')->cascadeOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->unique(['buyer_key', 'admin_user_activity_id'], 'buyer_activity_state_owner_event_unique');
            $table->index(['buyer_key', 'archived_at', 'deleted_at'], 'buyer_activity_state_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_account_activity_states');
    }
};
