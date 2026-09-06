<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('chat_messages') || Schema::hasColumn('chat_messages', 'is_bot')) {
            return;
        }

        Schema::table('chat_messages', function (Blueprint $table) {
            // Bot messages keep sender_role="admin" for compatibility with the
            // current chat backend. is_bot only changes the visual identity.
            $table->boolean('is_bot')->default(false)->index();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('chat_messages') || !Schema::hasColumn('chat_messages', 'is_bot')) {
            return;
        }

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn('is_bot');
        });
    }
};
