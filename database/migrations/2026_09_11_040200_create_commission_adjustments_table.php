<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_commission_id')
                ->constrained('order_commissions')
                ->cascadeOnDelete();
            $table->string('type', 40)->index();
            $table->decimal('amount', 12, 2);
            $table->text('reason');
            $table->string('reference', 120)->nullable();
            $table->foreignId('created_by_admin_id')
                ->nullable()
                ->constrained('admin_accounts')
                ->nullOnDelete();
            $table->dateTime('adjusted_at', 6)->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['order_commission_id', 'adjusted_at'], 'commission_adjustments_order_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_adjustments');
    }
};
