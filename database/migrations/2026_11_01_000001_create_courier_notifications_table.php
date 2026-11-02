<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('courier_notifications')) {
            Schema::create('courier_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('courier_account_id')->constrained('courier_accounts')->cascadeOnDelete();
                $table->string('event_key', 191);
                $table->string('category', 32);
                $table->string('title', 180);
                $table->text('message');
                $table->string('target_url', 1024);
                $table->timestamp('occurred_at')->index();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                $table->unique(['courier_account_id', 'event_key'], 'courier_notifications_source_unique');
                $table->index(['courier_account_id', 'read_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_notifications');
    }
};
