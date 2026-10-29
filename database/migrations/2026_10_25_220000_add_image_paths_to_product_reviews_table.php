<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_reviews') || Schema::hasColumn('product_reviews', 'image_paths')) {
            return;
        }

        Schema::table('product_reviews', function (Blueprint $table): void {
            $table->json('image_paths')->nullable()->after('comment');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('product_reviews') || !Schema::hasColumn('product_reviews', 'image_paths')) {
            return;
        }

        Schema::table('product_reviews', function (Blueprint $table): void {
            $table->dropColumn('image_paths');
        });
    }
};
