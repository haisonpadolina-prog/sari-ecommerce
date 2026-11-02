<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('delivery_proofs')) {
            return;
        }

        Schema::create('delivery_proofs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('marketplace_order_id')
                ->constrained('marketplace_orders')
                ->cascadeOnDelete();
            $table->foreignId('courier_account_id')
                ->constrained('courier_accounts')
                ->restrictOnDelete();
            $table->unsignedBigInteger('last_mile_assignment_id')->nullable()->index();
            $table->string('proof_type', 30)->default('delivered')->index();
            $table->string('photo_path');
            $table->string('recipient_name', 150)->nullable();
            $table->text('notes')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('captured_at');
            $table->timestamps();
            $table->index(['marketplace_order_id', 'courier_account_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_proofs');
    }
};
