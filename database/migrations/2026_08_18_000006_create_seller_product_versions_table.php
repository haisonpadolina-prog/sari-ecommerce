<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_product_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_product_id')
                ->constrained('seller_products')
                ->cascadeOnDelete();

            $table->unsignedInteger('version_number');
            $table->string('name');
            $table->string('category');
            $table->string('sku')->nullable();
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->decimal('discount', 5, 2)->default(0);
            $table->string('voucher_code')->nullable();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();

            $table->string('moderation_status')->nullable();
            $table->string('screening_risk')->nullable();
            $table->text('screening_reason')->nullable();
            $table->json('matched_terms')->nullable();
            $table->json('changed_fields')->nullable();
            $table->string('snapshot_reason')->default('seller_edit');
            $table->timestamps();

            $table->unique(['seller_product_id', 'version_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_product_versions');
    }
};
