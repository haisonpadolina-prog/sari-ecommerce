<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_parcels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_order_id')->unique()->constrained('marketplace_orders')->cascadeOnDelete();
            $table->string('status', 30)->default('awaiting_intake')->index();
            $table->string('sorting_zone', 80)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('sorted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_parcels');
    }
};
