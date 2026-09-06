<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_message_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_message_id')
                ->constrained('chat_messages')
                ->cascadeOnDelete();
            $table->string('reactor_role', 20);
            $table->unsignedBigInteger('reactor_id');
            $table->string('emoji', 24);
            $table->timestamps();

            // One current reaction per user/role/message.
            // Selecting another emoji updates it; selecting the same emoji removes it.
            $table->unique(
                ['chat_message_id', 'reactor_role', 'reactor_id'],
                'chat_message_reactor_unique'
            );

            $table->index(['chat_message_id', 'emoji']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_message_reactions');
    }
};
