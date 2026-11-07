<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_notifications', function (Blueprint $table): void {
            $table->timestamp('archived_at')->nullable()->after('read_at');
            $table->timestamp('deleted_at')->nullable()->after('archived_at');
            $table->index(['seller_account_id', 'deleted_at', 'archived_at', 'created_at'], 'seller_notifications_folder_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::table('seller_notifications', function (Blueprint $table): void {
            $table->dropIndex('seller_notifications_folder_lookup_idx');
            $table->dropColumn(['archived_at', 'deleted_at']);
        });
    }
};
