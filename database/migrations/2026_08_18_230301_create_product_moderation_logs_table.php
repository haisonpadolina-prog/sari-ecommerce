<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_moderation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_product_id')->constrained('seller_products')->cascadeOnDelete();
            $table->foreignId('seller_account_id')->constrained('seller_accounts')->cascadeOnDelete();
            $table->string('source', 30); // create | sensitive_edit
            $table->string('decision', 30); // pending | flagged
            $table->string('risk', 20);
            $table->unsignedTinyInteger('risk_score')->default(0);
            $table->string('engine', 100)->nullable();
            $table->json('matched_rules')->nullable();
            $table->json('matched_terms')->nullable();
            $table->boolean('ai_flagged')->default(false);
            $table->json('ai_categories')->nullable();
            $table->json('ai_scores')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['seller_account_id', 'created_at']);
            $table->index(['risk', 'decision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_moderation_logs');
    }
};
