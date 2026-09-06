<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('seller_chat_restrictions')) {
            return;
        }

        Schema::create('seller_chat_restrictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_account_id')
                ->unique()
                ->constrained('seller_accounts')
                ->cascadeOnDelete();
            $table->boolean('is_blocked')->default(false)->index();
            $table->text('block_reason')->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->timestamp('unblocked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_chat_restrictions');
    }
};
