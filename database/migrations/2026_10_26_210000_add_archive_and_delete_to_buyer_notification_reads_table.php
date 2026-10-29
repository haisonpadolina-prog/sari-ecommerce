<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buyer_notification_reads', function (Blueprint $table): void {
            $table->timestamp('archived_at')->nullable()->after('read_at');
            $table->timestamp('deleted_at')->nullable()->after('archived_at');
            $table->index(['buyer_key', 'archived_at'], 'buyer_notification_reads_buyer_archive_idx');
            $table->index(['buyer_key', 'deleted_at'], 'buyer_notification_reads_buyer_delete_idx');
        });
    }

    public function down(): void
    {
        Schema::table('buyer_notification_reads', function (Blueprint $table): void {
            $table->dropIndex('buyer_notification_reads_buyer_archive_idx');
            $table->dropIndex('buyer_notification_reads_buyer_delete_idx');
            $table->dropColumn(['archived_at', 'deleted_at']);
        });
    }
};
