<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sari_mobile_devices', function (Blueprint $table): void {
            $table->id();
            $table->string('account_role', 20);
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('personal_access_token_id')->index();
            $table->string('device_id', 120);
            $table->string('platform', 15);
            $table->string('token_hash', 64)->unique();
            $table->text('token_ciphertext');
            $table->boolean('push_enabled')->default(true);
            $table->timestamp('registered_at');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
            $table->unique(['account_role', 'account_id', 'device_id'], 'sari_devices_owner_device_unique');
            $table->index(['account_role', 'account_id']);
        });

        Schema::create('sari_mobile_push_receipts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mobile_device_id')->constrained('sari_mobile_devices')->cascadeOnDelete();
            $table->string('event_key', 160);
            $table->timestamp('sent_at');
            $table->unique(['mobile_device_id', 'event_key'], 'sari_push_device_event_unique');
        });

        // Only latest reported position, not a hidden GPS history.
        Schema::create('sari_mobile_rider_locations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('courier_account_id');
            $table->string('assignment_type', 12);
            $table->unsignedBigInteger('assignment_id');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedSmallInteger('accuracy_m')->nullable();
            $table->boolean('sharing_enabled')->default(true);
            $table->timestamp('recorded_at');
            $table->timestamps();
            $table->unique(['courier_account_id', 'assignment_type', 'assignment_id'], 'sari_rider_one_position_per_job');
            $table->index(['assignment_type', 'assignment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sari_mobile_rider_locations');
        Schema::dropIfExists('sari_mobile_push_receipts');
        Schema::dropIfExists('sari_mobile_devices');
    }
};
