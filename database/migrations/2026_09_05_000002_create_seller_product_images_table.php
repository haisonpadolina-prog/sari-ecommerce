<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('seller_product_images')) {
            return;
        }

        Schema::create('seller_product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_product_id')
                ->constrained('seller_products')
                ->cascadeOnDelete();
            $table->string('path');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('alt_text', 180)->nullable();
            $table->timestamps();

            $table->index(['seller_product_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_product_images');
    }
};
