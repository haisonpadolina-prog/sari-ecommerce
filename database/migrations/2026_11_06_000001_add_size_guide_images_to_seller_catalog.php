<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_products', function (Blueprint $table) {
            $table->string('size_guide_image_path')->nullable();
        });
        Schema::table('seller_product_drafts', function (Blueprint $table) {
            $table->string('size_guide_image_path')->nullable();
        });
        Schema::table('seller_product_versions', function (Blueprint $table) {
            $table->string('size_guide_image_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('seller_product_versions', fn (Blueprint $table) => $table->dropColumn('size_guide_image_path'));
        Schema::table('seller_product_drafts', fn (Blueprint $table) => $table->dropColumn('size_guide_image_path'));
        Schema::table('seller_products', fn (Blueprint $table) => $table->dropColumn('size_guide_image_path'));
    }
};
