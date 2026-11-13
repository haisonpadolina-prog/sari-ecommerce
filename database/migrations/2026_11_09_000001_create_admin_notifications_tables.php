<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_notifications', function (Blueprint $table): void {
            $table->id();
            $table->string('source_key', 191)->unique();
            $table->string('category', 40)->index();
            $table->string('title', 160);
            $table->string('message', 500);
            $table->string('route_name', 120)->nullable();
            $table->json('route_parameters')->nullable();
            $table->timestamp('created_at')->index();
        });
        Schema::create('admin_notification_reads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('admin_account_id')->constrained('admin_accounts')->cascadeOnDelete();
            $table->foreignId('admin_notification_id')->constrained('admin_notifications')->cascadeOnDelete();
            $table->timestamp('read_at');
            $table->unique(['admin_account_id', 'admin_notification_id'], 'admin_notification_read_unique');
        });
        Schema::create('admin_notification_states', function (Blueprint $table): void {
            $table->foreignId('admin_account_id')->primary()->constrained('admin_accounts')->cascadeOnDelete();
            $table->unsignedBigInteger('last_read_id')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notification_states');
        Schema::dropIfExists('admin_notification_reads');
        Schema::dropIfExists('admin_notifications');
    }
};
