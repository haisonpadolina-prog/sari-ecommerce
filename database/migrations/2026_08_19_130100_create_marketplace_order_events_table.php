<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_order_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('marketplace_order_id')
                ->constrained('marketplace_orders')
                ->cascadeOnDelete();

            $table->foreignId('seller_account_id')
                ->constrained('seller_accounts')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('audience', 20)->default('seller')->index();
            $table->string('type', 60);
            $table->string('title');
            $table->text('message');
            $table->string('status', 60)->nullable();
            $table->timestamps();

            $table->index(['seller_account_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_order_events');
    }
};
