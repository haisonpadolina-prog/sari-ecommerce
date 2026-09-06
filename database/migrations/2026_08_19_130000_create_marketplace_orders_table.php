<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();

            $table->foreignId('seller_account_id')
                ->constrained('seller_accounts')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('buyer_name');
            $table->string('buyer_phone')->nullable();
            $table->text('buyer_address');

            $table->string('payment_method')->default('COD');
            $table->string('payment_status')->default('pending');

            $table->json('items')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->string('pickup_name')->nullable();
            $table->text('pickup_address')->nullable();

            $table->string('status')->default('new')->index();

            $table->string('courier_name')->nullable();
            $table->string('courier_email')->nullable()->index();

            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('pickup_started_at')->nullable();
            $table->timestamp('arrived_pickup_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('arrived_buyer_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();

            $table->index(['seller_account_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_orders');
    }
};
