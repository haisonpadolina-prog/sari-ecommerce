<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courier_account_id')->constrained('courier_accounts')->cascadeOnDelete();
            $table->foreignId('logistics_account_id')->nullable()->constrained('logistics_accounts')->nullOnDelete();
            $table->string('sender_role', 20)->index();
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['courier_account_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_messages');
    }
};
