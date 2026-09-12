<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * REPAIR NOTE:
         * The first draft could fail on MySQL because Laravel's default
         * foreign-key name for platform_conversation_participants exceeded
         * MySQL's 64-character identifier limit.
         *
         * Because that failed migration is not recorded in the migrations
         * table, MySQL may still have left partially-created draft tables.
         * These three tables belong only to the new universal messaging
         * backend, so we rebuild them cleanly here before first successful run.
         * Existing legacy chat/report tables are NOT touched.
         */
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('platform_conversation_participants');
        Schema::dropIfExists('platform_messages');
        Schema::dropIfExists('platform_conversations');

        Schema::enableForeignKeyConstraints();

        Schema::create('platform_conversations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('conversation_type', 40)->index();
            $table->string('context_type', 60)->nullable()->index();
            $table->unsignedBigInteger('context_id')->nullable()->index();
            $table->string('dedupe_key', 190)->unique();
            $table->string('subject', 190)->nullable();
            $table->string('status', 20)->default('active')->index();
            $table->string('created_by_role', 32);
            $table->unsignedBigInteger('created_by_id');
            $table->timestamp('last_message_at')->nullable()->index();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(
                ['created_by_role', 'created_by_id'],
                'pc_creator_idx'
            );

            $table->index(
                ['context_type', 'context_id'],
                'pc_context_idx'
            );
        });

        Schema::create('platform_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('platform_conversation_id');
            $table->string('sender_role', 32)->index();
            $table->unsignedBigInteger('sender_id')->index();
            $table->string('message_type', 30)->default('text');
            $table->text('body')->nullable();

            // Reserved for the later UI/attachment phase.
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->string('attachment_mime', 120)->nullable();
            $table->unsignedBigInteger('attachment_size')->nullable();

            $table->json('metadata')->nullable();
            $table->string('legacy_source', 80)->nullable();
            $table->unsignedBigInteger('legacy_id')->nullable();
            $table->timestamp('edited_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign(
                'platform_conversation_id',
                'pm_conversation_fk'
            )
                ->references('id')
                ->on('platform_conversations')
                ->cascadeOnDelete();

            $table->index(
                ['platform_conversation_id', 'created_at'],
                'pm_conversation_created_idx'
            );

            $table->unique(
                ['legacy_source', 'legacy_id'],
                'pm_legacy_unique'
            );
        });

        Schema::create('platform_conversation_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('platform_conversation_id');
            $table->string('participant_role', 32);
            $table->unsignedBigInteger('participant_id');
            $table->timestamp('joined_at')->useCurrent();
            $table->unsignedBigInteger('last_read_message_id')->nullable();
            $table->timestamp('muted_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();

            $table->foreign(
                'platform_conversation_id',
                'pcp_conversation_fk'
            )
                ->references('id')
                ->on('platform_conversations')
                ->cascadeOnDelete();

            $table->foreign(
                'last_read_message_id',
                'pcp_last_read_fk'
            )
                ->references('id')
                ->on('platform_messages')
                ->nullOnDelete();

            $table->unique(
                [
                    'platform_conversation_id',
                    'participant_role',
                    'participant_id',
                ],
                'pcp_identity_unique'
            );

            $table->index(
                ['participant_role', 'participant_id', 'left_at'],
                'pcp_identity_active_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('platform_conversation_participants');
        Schema::dropIfExists('platform_messages');
        Schema::dropIfExists('platform_conversations');

        Schema::enableForeignKeyConstraints();
    }
};
