<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_message_reactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('platform_message_id');
            $table->string('reactor_role', 32);
            $table->unsignedBigInteger('reactor_id');
            $table->string('emoji', 16);
            $table->timestamps();

            $table->foreign(
                'platform_message_id',
                'pmr_message_fk'
            )
                ->references('id')
                ->on('platform_messages')
                ->cascadeOnDelete();

            $table->unique(
                ['platform_message_id', 'reactor_role', 'reactor_id'],
                'pmr_actor_unique'
            );

            $table->index(
                ['platform_message_id', 'emoji'],
                'pmr_message_emoji_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_message_reactions');
    }
};
