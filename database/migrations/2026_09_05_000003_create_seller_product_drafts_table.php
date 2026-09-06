<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('seller_product_drafts')) {
            return;
        }

        Schema::create('seller_product_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_account_id')
                ->unique()
                ->constrained('seller_accounts')
                ->cascadeOnDelete();
            $table->json('payload')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->json('gallery_image_paths')->nullable();
            $table->json('variant_image_paths')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_product_drafts');
    }
};
