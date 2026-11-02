<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add fields to the existing Rider notification table; preserve all stored records.
        if (! Schema::hasTable('courier_notifications')) return;
        Schema::table('courier_notifications', function (Blueprint $table): void {
            if (! Schema::hasColumn('courier_notifications', 'archived_at')) {
                $table->timestamp('archived_at')->nullable()->index();
            }
            if (! Schema::hasColumn('courier_notifications', 'deleted_at')) {
                $table->timestamp('deleted_at')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('courier_notifications')) return;
        Schema::table('courier_notifications', function (Blueprint $table): void {
            if (Schema::hasColumn('courier_notifications', 'archived_at')) $table->dropColumn('archived_at');
            if (Schema::hasColumn('courier_notifications', 'deleted_at')) $table->dropColumn('deleted_at');
        });
    }
};
