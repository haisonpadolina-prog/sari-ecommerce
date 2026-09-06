<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('seller_chat_moderation_actions')) {
            return;
        }

        Schema::create('seller_chat_moderation_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_account_id')
                ->constrained('seller_accounts')
                ->cascadeOnDelete();
            $table->string('action_type', 40)->index();
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_chat_moderation_actions');
    }
};
