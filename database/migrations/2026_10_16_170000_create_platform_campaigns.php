<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_campaigns', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('admin_account_id')->nullable()->constrained('admin_accounts')->nullOnDelete();
            $table->string('name', 140);
            $table->string('slug', 170)->unique();
            $table->text('description')->nullable();
            $table->string('state', 20)->default('draft')->index(); // draft | published | paused
            $table->string('target_scope', 20)->default('all'); // all | sellers | products | categories

            $table->boolean('sale_enabled')->default(false);
            $table->string('sale_discount_type', 20)->nullable(); // percentage | fixed
            $table->decimal('sale_discount_value', 12, 2)->default(0);
            $table->decimal('sale_max_discount', 12, 2)->nullable();

            $table->boolean('voucher_enabled')->default(false);
            $table->string('voucher_code', 64)->nullable()->unique();
            $table->string('voucher_discount_type', 20)->nullable(); // percentage | fixed
            $table->decimal('voucher_discount_value', 12, 2)->default(0);
            $table->decimal('voucher_minimum_spend', 12, 2)->default(0);
            $table->decimal('voucher_maximum_discount', 12, 2)->nullable();
            $table->unsignedInteger('voucher_usage_limit')->nullable();
            $table->unsignedInteger('voucher_used_count')->default(0);
            $table->unsignedSmallInteger('voucher_per_buyer_limit')->default(1);

            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->timestamps();

            $table->index(['state', 'starts_at', 'ends_at'], 'platform_campaign_live_idx');
        });

        Schema::create('platform_campaign_products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('platform_campaign_id')->constrained('platform_campaigns')->cascadeOnDelete();
            $table->foreignId('seller_product_id')->constrained('seller_products')->cascadeOnDelete();
            $table->unique(['platform_campaign_id', 'seller_product_id'], 'platform_campaign_product_unique');
        });

        Schema::create('platform_campaign_sellers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('platform_campaign_id')->constrained('platform_campaigns')->cascadeOnDelete();
            $table->foreignId('seller_account_id')->constrained('seller_accounts')->cascadeOnDelete();
            $table->unique(['platform_campaign_id', 'seller_account_id'], 'platform_campaign_seller_unique');
        });

        Schema::create('platform_campaign_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('platform_campaign_id')->constrained('platform_campaigns')->cascadeOnDelete();
            $table->string('category', 120);
            $table->timestamps();
            $table->unique(['platform_campaign_id', 'category'], 'platform_campaign_category_unique');
        });

        Schema::create('platform_campaign_redemptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('platform_campaign_id')->constrained('platform_campaigns')->restrictOnDelete();
            $table->string('checkout_reference', 80);
            $table->unsignedBigInteger('buyer_account_id')->nullable()->index();
            $table->unsignedBigInteger('buyer_social_account_id')->nullable()->index();
            $table->string('voucher_code', 64);
            $table->decimal('discount_amount', 12, 2);
            $table->json('order_ids')->nullable();
            $table->timestamps();
            $table->unique(['platform_campaign_id', 'checkout_reference'], 'platform_campaign_checkout_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_campaign_redemptions');
        Schema::dropIfExists('platform_campaign_categories');
        Schema::dropIfExists('platform_campaign_sellers');
        Schema::dropIfExists('platform_campaign_products');
        Schema::dropIfExists('platform_campaigns');
    }
};
