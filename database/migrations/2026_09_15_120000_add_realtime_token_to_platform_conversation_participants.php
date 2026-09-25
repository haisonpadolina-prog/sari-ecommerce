<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platform_conversation_participants', function (Blueprint $table) {
            $table->string('realtime_token', 64)
                ->nullable()
                ->unique()
                ->after('participant_id');
        });
    }

    public function down(): void
    {
        Schema::table('platform_conversation_participants', function (Blueprint $table) {
            $table->dropUnique(['realtime_token']);
            $table->dropColumn('realtime_token');
        });
    }
};
