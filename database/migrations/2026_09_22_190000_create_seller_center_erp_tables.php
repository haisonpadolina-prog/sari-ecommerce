<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_accounts', 'store_description')) {
                $table->text('store_description')->nullable();
            }
            if (!Schema::hasColumn('seller_accounts', 'store_phone')) {
                $table->string('store_phone', 40)->nullable();
            }
            if (!Schema::hasColumn('seller_accounts', 'store_public_email')) {
                $table->string('store_public_email')->nullable();
            }
            if (!Schema::hasColumn('seller_accounts', 'store_status')) {
                $table->string('store_status', 20)->default('open')->index();
            }
            if (!Schema::hasColumn('seller_accounts', 'pickup_instructions')) {
                $table->text('pickup_instructions')->nullable();
            }
        });

        Schema::table('marketplace_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('marketplace_orders', 'original_subtotal')) {
                $table->decimal('original_subtotal', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('marketplace_orders', 'discount_amount')) {
                $table->decimal('discount_amount', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('marketplace_orders', 'voucher_code')) {
                $table->string('voucher_code', 64)->nullable()->index();
            }
            if (!Schema::hasColumn('marketplace_orders', 'seller_voucher_id')) {
                $table->unsignedBigInteger('seller_voucher_id')->nullable()->index();
            }
        });

        Schema::create('seller_inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_account_id')->constrained('seller_accounts')->cascadeOnDelete();
            $table->foreignId('seller_product_id')->constrained('seller_products')->cascadeOnDelete();
            $table->unsignedBigInteger('seller_product_variant_id')->nullable()->index();
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            $table->integer('quantity_delta');
            $table->string('reason', 120);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['seller_account_id', 'created_at']);
        });

        Schema::create('seller_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_account_id')->constrained('seller_accounts')->cascadeOnDelete();
            $table->string('code', 64);
            $table->string('name', 120);
            $table->string('discount_type', 20); // percentage | fixed
            $table->decimal('discount_value', 12, 2);
            $table->decimal('minimum_spend', 12, 2)->default(0);
            $table->decimal('maximum_discount', 12, 2)->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['seller_account_id', 'code']);
            $table->index(['seller_account_id', 'is_active']);
        });

        Schema::create('seller_voucher_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_voucher_id')->constrained('seller_vouchers')->cascadeOnDelete();
            $table->foreignId('marketplace_order_id')->constrained('marketplace_orders')->cascadeOnDelete();
            $table->unsignedBigInteger('buyer_account_id')->nullable()->index();
            $table->unsignedBigInteger('buyer_social_account_id')->nullable()->index();
            $table->decimal('discount_amount', 12, 2);
            $table->timestamps();
            $table->unique('marketplace_order_id');
        });

        Schema::create('seller_return_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_order_id')->constrained('marketplace_orders')->cascadeOnDelete();
            $table->foreignId('seller_account_id')->constrained('seller_accounts')->cascadeOnDelete();
            $table->unsignedBigInteger('buyer_account_id')->nullable()->index();
            $table->unsignedBigInteger('buyer_social_account_id')->nullable()->index();
            $table->string('reason', 120);
            $table->text('details')->nullable();
            $table->decimal('requested_amount', 12, 2);
            $table->decimal('approved_amount', 12, 2)->nullable();
            $table->string('status', 30)->default('requested')->index();
            $table->text('seller_response')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
            $table->unique('marketplace_order_id');
            $table->index(['seller_account_id', 'status']);
        });

        Schema::create('seller_return_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_return_request_id')->constrained('seller_return_requests')->cascadeOnDelete();
            $table->string('actor_role', 30);
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('status', 30);
            $table->string('title', 150);
            $table->text('message')->nullable();
            $table->timestamps();
        });

        Schema::create('seller_review_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_review_id')->constrained('product_reviews')->cascadeOnDelete();
            $table->foreignId('seller_account_id')->constrained('seller_accounts')->cascadeOnDelete();
            $table->text('reply');
            $table->timestamps();
            $table->unique('product_review_id');
        });

        Schema::create('seller_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_account_id')->constrained('seller_accounts')->cascadeOnDelete();
            $table->string('type', 50);
            $table->string('title', 160);
            $table->text('message')->nullable();
            $table->string('action_url')->nullable();
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['seller_account_id', 'read_at', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_notifications');
        Schema::dropIfExists('seller_review_replies');
        Schema::dropIfExists('seller_return_events');
        Schema::dropIfExists('seller_return_requests');
        Schema::dropIfExists('seller_voucher_redemptions');
        Schema::dropIfExists('seller_vouchers');
        Schema::dropIfExists('seller_inventory_movements');

        Schema::table('marketplace_orders', function (Blueprint $table) {
            foreach (['original_subtotal','discount_amount','voucher_code','seller_voucher_id'] as $column) {
                if (Schema::hasColumn('marketplace_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('seller_accounts', function (Blueprint $table) {
            foreach (['store_description','store_phone','store_public_email','store_status','pickup_instructions'] as $column) {
                if (Schema::hasColumn('seller_accounts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
