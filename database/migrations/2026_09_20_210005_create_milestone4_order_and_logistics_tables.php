<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();

                $table->foreignId('buyer_id')
                    ->constrained('users')
                    ->restrictOnDelete();

                $table->string('reference')->unique();

                // Integer centavos.
                $table->unsignedInteger('total_minor');

                $table->enum('payment_method', [
                    'cod',
                    'wallet',
                ]);

                $table->enum('payment_status', [
                    'pending',
                    'paid',
                    'refunded',
                    'partially_refunded',
                ])->default('pending');

                // Snapshot, not an address FK.
                $table->json('shipping_address');

                $table->timestamps();

                $table->index(['buyer_id', 'payment_status']);
                $table->index('payment_status');
            });
        }

        if (!Schema::hasTable('seller_orders')) {
            Schema::create('seller_orders', function (Blueprint $table) {
                $table->id();

                $table->foreignId('order_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('seller_id')
                    ->constrained()
                    ->restrictOnDelete();

                $table->foreignId('logistics_provider_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();

                $table->unsignedInteger('subtotal_minor');
                $table->unsignedInteger('shipping_fee_minor')->default(0);
                $table->unsignedInteger('commission_minor')->default(0);

                $table->enum('status', [
                    'pending',
                    'accepted',
                    'packed',
                    'ready_to_ship',
                    'shipped',
                    'delivered',
                    'completed',
                    'cancelled',
                ])->default('pending');

                $table->timestamp('delivered_at')->nullable();
                $table->text('note')->nullable();
                $table->timestamps();

                $table->unique(['order_id', 'seller_id']);
                $table->index(['seller_id', 'status']);
                $table->index(['logistics_provider_id', 'status']);
            });
        }

        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();

                // Required split: item belongs to seller_order, NOT directly to orders.
                $table->foreignId('seller_order_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('product_id')
                    ->constrained()
                    ->restrictOnDelete();

                $table->foreignId('product_variant_id')
                    ->constrained()
                    ->restrictOnDelete();

                $table->string('product_name');
                $table->string('variant_name');
                $table->unsignedInteger('unit_price_minor');
                $table->unsignedInteger('quantity');
                $table->timestamps();

                $table->index('seller_order_id');
                $table->index('product_variant_id');
            });
        }

        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();

                $table->foreignId('order_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->enum('method', [
                    'cod',
                    'wallet',
                ]);

                $table->unsignedInteger('amount_minor');

                $table->enum('status', [
                    'pending',
                    'paid',
                    'failed',
                    'refunded',
                    'partially_refunded',
                ])->default('pending');

                $table->string('provider_ref')->nullable();
                $table->timestamps();

                $table->index(['order_id', 'status']);
                $table->index('provider_ref');
            });
        }

        if (!Schema::hasTable('shipments')) {
            Schema::create('shipments', function (Blueprint $table) {
                $table->id();

                $table->foreignId('seller_order_id')
                    ->unique()
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('logistics_provider_id')
                    ->constrained()
                    ->restrictOnDelete();

                $table->foreignId('rider_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();

                $table->string('tracking_code')->unique();

                $table->enum('status', [
                    'unassigned',
                    'assigned',
                    'picked_up',
                    'in_transit',
                    'out_for_delivery',
                    'delivered',
                    'failed',
                    'returned',
                ])->default('unassigned');

                $table->unsignedInteger('fee_minor')->default(0);
                $table->unsignedInteger('cod_amount_minor')->default(0);
                $table->boolean('cod_collected')->default(false);
                $table->unsignedInteger('attempts')->default(0);
                $table->timestamps();

                $table->index(['logistics_provider_id', 'status']);
                $table->index(['rider_id', 'status']);
                $table->index('status');
            });
        }

        if (!Schema::hasTable('delivery_events')) {
            Schema::create('delivery_events', function (Blueprint $table) {
                $table->id();

                $table->foreignId('shipment_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->enum('status', [
                    'unassigned',
                    'assigned',
                    'picked_up',
                    'in_transit',
                    'out_for_delivery',
                    'delivered',
                    'failed',
                    'returned',
                ]);

                $table->unsignedInteger('attempt')->default(1);

                $table->foreignId('user_id')
                    ->constrained()
                    ->restrictOnDelete();

                $table->text('note')->nullable();
                $table->string('photo_path')->nullable();
                $table->timestamp('occurred_at');
                $table->timestamps();

                $table->unique(['shipment_id', 'status', 'attempt']);
                $table->index(['shipment_id', 'occurred_at']);
                $table->index('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_events');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('seller_orders');
        Schema::dropIfExists('orders');
    }
};
