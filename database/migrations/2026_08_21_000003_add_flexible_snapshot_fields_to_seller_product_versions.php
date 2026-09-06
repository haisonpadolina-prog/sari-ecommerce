<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_product_versions', function (Blueprint $table) {
            $table->string('brand', 120)->nullable()->after('category');
            $table->json('specifications')->nullable()->after('description');
            $table->boolean('has_variants')->default(false)->after('specifications');
            $table->json('variants_snapshot')->nullable()->after('has_variants');
        });
    }

    public function down(): void
    {
        Schema::table('seller_product_versions', function (Blueprint $table) {
            $table->dropColumn([
                'brand',
                'specifications',
                'has_variants',
                'variants_snapshot',
            ]);
        });
    }
};
