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
            $table->foreignId('seller_product_id')
                ->constrained('seller_products')
                ->cascadeOnDelete();

            $table->string('sku', 120)->nullable();
            $table->json('option_values');
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['seller_product_id', 'is_active']);
            $table->index('sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_product_variants');
    }
};
