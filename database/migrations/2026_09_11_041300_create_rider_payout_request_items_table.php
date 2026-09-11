<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rider_payout_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_payout_request_id')
                ->constrained('rider_payout_requests')
                ->cascadeOnDelete();
            $table->foreignId('rider_earning_id')
                ->constrained('rider_earnings')
                ->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->timestamps();

            $table->unique(
                ['rider_payout_request_id', 'rider_earning_id'],
                'rider_payout_items_request_earning_unique'
            );
            $table->index('rider_earning_id', 'rider_payout_items_earning');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rider_payout_request_items');
    }
};
