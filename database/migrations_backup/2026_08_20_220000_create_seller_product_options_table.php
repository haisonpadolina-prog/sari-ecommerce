<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_product_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_product_id')->constrained('seller_products')->cascadeOnDelete();
            $table->string('name', 60);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index(['seller_product_id', 'position']);
        });

        Schema::create('seller_product_option_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_product_option_id')->constrained('seller_product_options')->cascadeOnDelete();
            $table->string('value', 100);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index(['seller_product_option_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_product_option_values');
        Schema::dropIfExists('seller_product_options');
    }
};
