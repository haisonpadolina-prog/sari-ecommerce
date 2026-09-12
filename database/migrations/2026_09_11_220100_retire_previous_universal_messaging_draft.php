<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('platform_conversations')) {
            return;
        }

        // Retire conversation types from the first universal-messaging draft
        // that conflict with the corrected role-access policy.
        DB::table('platform_conversations')
            ->whereIn('conversation_type', [
                'support',
                'order_room',
            ])
            ->where('status', 'active')
            ->update([
                'status' => 'closed',
                'closed_at' => now(),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Intentionally no automatic reopening. Reopening old cross-role
        // conversations could re-enable access that the corrected policy
        // explicitly removed.
    }
};
