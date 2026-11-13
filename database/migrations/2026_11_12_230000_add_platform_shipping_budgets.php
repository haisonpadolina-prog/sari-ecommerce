<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platform_campaigns', function (Blueprint $table): void {
            $table->boolean('shipping_enabled')->default(false);
            $table->string('shipping_code', 64)->nullable()->unique();
            $table->unsignedBigInteger('shipping_budget_cents')->default(0);
            $table->unsignedBigInteger('shipping_reserved_cents')->default(0);
            $table->unsignedBigInteger('shipping_used_cents')->default(0);
            $table->unsignedBigInteger('shipping_cap_cents')->default(0);
            $table->unsignedBigInteger('shipping_minimum_cents')->default(0);
            $table->unsignedSmallInteger('shipping_per_buyer_limit')->default(1);
            $table->json('shipping_areas')->nullable();
        });
        Schema::table('marketplace_orders', function (Blueprint $table): void {
            $table->decimal('platform_shipping_discount_amount', 12, 2)->default(0);
        });
        Schema::create('platform_shipping_redemptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('platform_campaign_id')->constrained()->restrictOnDelete();
            $table->foreignId('marketplace_order_id')->unique()->constrained()->restrictOnDelete();
            $table->string('checkout_reference', 80);
            $table->unsignedBigInteger('buyer_account_id')->nullable();
            $table->unsignedBigInteger('buyer_social_account_id')->nullable();
            $table->unsignedBigInteger('amount_cents');
            $table->string('state', 16)->default('reserved');
            $table->json('policy_snapshot');
            $table->timestamp('used_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
            $table->index(['platform_campaign_id', 'state'], 'platform_shipping_budget_idx');
            $table->index(['platform_campaign_id', 'buyer_account_id'], 'platform_shipping_buyer_idx');
            $table->index(['platform_campaign_id', 'buyer_social_account_id'], 'platform_shipping_social_idx');
        });
    }

    public function down(): void
    {
        // Remove this feature only after exported subsidy history is preserved.
        Schema::dropIfExists('platform_shipping_redemptions');
        Schema::table('marketplace_orders', fn (Blueprint $table) => $table->dropColumn('platform_shipping_discount_amount'));
        Schema::table('platform_campaigns', fn (Blueprint $table) => $table->dropColumn([
            'shipping_enabled', 'shipping_code', 'shipping_budget_cents', 'shipping_cap_cents',
            'shipping_reserved_cents', 'shipping_used_cents',
            'shipping_minimum_cents', 'shipping_per_buyer_limit', 'shipping_areas',
        ]));
    }
};
