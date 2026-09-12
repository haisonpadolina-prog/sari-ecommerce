<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_commission_id')
                ->nullable()
                ->constrained('order_commissions')
                ->nullOnDelete();
            $table->foreignId('commission_rate_id')
                ->nullable()
                ->constrained('commission_rates')
                ->nullOnDelete();
            $table->foreignId('admin_account_id')
                ->nullable()
                ->constrained('admin_accounts')
                ->nullOnDelete();
            $table->string('actor_type', 30)->default('system');
            $table->string('action', 60)->index();
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['order_commission_id', 'created_at'], 'commission_audit_order_time');
            $table->index(['commission_rate_id', 'created_at'], 'commission_audit_rate_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_audit_logs');
    }
};
