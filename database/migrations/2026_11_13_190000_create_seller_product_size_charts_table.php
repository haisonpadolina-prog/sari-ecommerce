<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('seller_product_size_charts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_product_id')->unique()->constrained('seller_products')->cascadeOnDelete();
            $table->string('unit', 4);
            $table->string('measurement_type', 16);
            $table->string('garment_type', 16);
            $table->string('fit', 16);
            $table->json('rows');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('seller_product_size_charts'); }
};
