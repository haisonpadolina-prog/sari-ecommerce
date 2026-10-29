<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('buyer_seller_messages')) {
            return;
        }

        Schema::table('buyer_seller_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('buyer_seller_messages', 'message_type')) {
                $table->string('message_type', 40)->default('text')->after('marketplace_order_id');
            }
            if (!Schema::hasColumn('buyer_seller_messages', 'attachment_disk')) {
                $table->string('attachment_disk', 30)->nullable()->after('body');
            }
            if (!Schema::hasColumn('buyer_seller_messages', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('attachment_disk');
            }
            if (!Schema::hasColumn('buyer_seller_messages', 'attachment_original_name')) {
                $table->string('attachment_original_name')->nullable()->after('attachment_path');
            }
            if (!Schema::hasColumn('buyer_seller_messages', 'attachment_mime_type')) {
                $table->string('attachment_mime_type', 120)->nullable()->after('attachment_original_name');
            }
            if (!Schema::hasColumn('buyer_seller_messages', 'attachment_size')) {
                $table->unsignedBigInteger('attachment_size')->nullable()->after('attachment_mime_type');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('buyer_seller_messages')) {
            return;
        }

        Schema::table('buyer_seller_messages', function (Blueprint $table) {
            $drops = ['attachment_size', 'attachment_mime_type', 'attachment_original_name', 'attachment_path', 'attachment_disk'];
            foreach ($drops as $column) {
                if (Schema::hasColumn('buyer_seller_messages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
