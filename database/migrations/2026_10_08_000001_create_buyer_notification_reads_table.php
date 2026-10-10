<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buyer_notification_reads', function (Blueprint $table): void {
            $table->id();
            $table->string('buyer_key', 80);
            $table->foreignId('marketplace_order_event_id')
                ->constrained('marketplace_order_events')
                ->cascadeOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['buyer_key', 'marketplace_order_event_id'],
                'buyer_notification_reads_identity_event_unique'
            );
            $table->index(['buyer_key', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_notification_reads');
    }
};
