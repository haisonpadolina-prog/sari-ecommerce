<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_account_id')->index();
            $table->string('sender_role', 20); // seller | admin
            $table->text('body')->nullable();

            // Attachments stay on Laravel's local/private disk.
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->string('attachment_mime', 120)->nullable();
            $table->unsignedBigInteger('attachment_size')->nullable();

            $table->timestamp('read_by_seller_at')->nullable();
            $table->timestamp('read_by_admin_at')->nullable();
            $table->timestamps();

            $table->index(['seller_account_id', 'created_at']);
            $table->index(['seller_account_id', 'sender_role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
