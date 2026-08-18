<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_account_id')->constrained('seller_accounts')->cascadeOnDelete();
            $table->string('name');
            $table->string('category');
            $table->string('sku')->nullable();
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->decimal('discount', 5, 2)->default(0);
            $table->string('voucher_code')->nullable();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();

            // Moderation / screening
            $table->string('moderation_status')->default('pending'); // pending | flagged | approved | rejected | removed
            $table->string('screening_risk')->default('low'); // low | medium | high
            $table->text('screening_reason')->nullable();
            $table->json('matched_terms')->nullable();
            $table->text('admin_review_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['moderation_status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_products');
    }
};
