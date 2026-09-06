<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_product_id')->constrained('seller_products')->cascadeOnDelete();
            $table->string('combination_key', 512);
            $table->json('option_values');
            $table->string('sku', 120)->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['seller_product_id', 'combination_key'], 'seller_variant_combination_unique');
            $table->index(['seller_product_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_product_variants');
    }
};
