<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_route_stops', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('marketplace_order_id')
                ->constrained('marketplace_orders')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('sequence');
            $table->string('stop_key', 32);
            $table->string('type', 32);
            $table->string('name', 120);
            $table->text('address_snapshot')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestamp('reached_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['marketplace_order_id', 'sequence'],
                'delivery_route_stops_order_sequence_unique'
            );

            $table->unique(
                ['marketplace_order_id', 'stop_key'],
                'delivery_route_stops_order_key_unique'
            );

            $table->index(['marketplace_order_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_route_stops');
    }
};
