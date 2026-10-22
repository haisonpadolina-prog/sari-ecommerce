<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_announcements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('admin_account_id')->nullable()->constrained('admin_accounts')->nullOnDelete();
            $table->string('title', 160);
            $table->text('message');
            $table->json('audiences');
            $table->string('severity', 20)->default('info');
            $table->string('status', 20)->default('draft');
            $table->string('action_label', 80)->nullable();
            $table->string('action_url', 500)->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'starts_at', 'ends_at'], 'platform_announcements_live_idx');
        });

        Schema::create('platform_announcement_reads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('platform_announcement_id')
                ->constrained('platform_announcements')
                ->cascadeOnDelete();
            $table->string('identity_type', 30);
            $table->unsignedBigInteger('identity_id');
            $table->timestamp('read_at');
            $table->timestamps();

            $table->unique(
                ['platform_announcement_id', 'identity_type', 'identity_id'],
                'platform_announcement_read_identity_unique'
            );
            $table->index(['identity_type', 'identity_id', 'read_at'], 'platform_announcement_identity_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_announcement_reads');
        Schema::dropIfExists('platform_announcements');
    }
};
