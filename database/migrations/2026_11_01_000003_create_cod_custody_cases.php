<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cod_custody_cases', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('logistics_account_id')->constrained('logistics_accounts')->restrictOnDelete();
            $table->foreignId('courier_account_id')->constrained('courier_accounts')->restrictOnDelete();
            $table->string('status', 20)->default('open');
            $table->timestamp('extended_until')->nullable();
            $table->timestamp('extension_applies_through')->nullable();
            $table->timestamp('cod_restricted_at')->nullable();
            $table->timestamp('suspension_review_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->unique(['logistics_account_id','courier_account_id'], 'cod_custody_case_owner_unique');
        });
        Schema::create('cod_custody_case_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cod_custody_case_id')->constrained('cod_custody_cases')->cascadeOnDelete();
            $table->unsignedBigInteger('logistics_actor_id');
            $table->string('action', 32);
            $table->text('note');
            $table->timestamps();
            $table->index(['cod_custody_case_id','created_at'],'cod_custody_event_timeline');
        });
    }
    public function down(): void {
        Schema::dropIfExists('cod_custody_case_events');
        Schema::dropIfExists('cod_custody_cases');
    }
};
