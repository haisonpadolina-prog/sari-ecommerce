<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_ai_threads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('admin_account_id')->constrained('admin_accounts')->cascadeOnDelete();
            $table->text('messages'); // Encrypted JSON; never returned as provider history to the browser.
            $table->timestamps();
        });
        Schema::create('admin_ai_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('admin_account_id')->constrained('admin_accounts')->cascadeOnDelete();
            $table->string('type', 40);
            $table->text('payload'); // Encrypted validated proposal and record fingerprints.
            $table->string('status', 20)->default('pending');
            $table->text('result')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
            $table->index(['admin_account_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_ai_actions');
        Schema::dropIfExists('admin_ai_threads');
    }
};
