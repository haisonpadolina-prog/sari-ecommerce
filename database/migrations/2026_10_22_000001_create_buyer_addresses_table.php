<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('buyer_addresses')) {
            return;
        }

        Schema::create('buyer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_account_id')
                ->constrained('buyer_accounts')
                ->cascadeOnDelete();

            $table->string('label', 50)->default('Home');
            $table->string('recipient_name', 150);
            $table->string('contact_no', 20);
            $table->string('street_address', 500);
            $table->string('barangay_name', 150);
            $table->string('municipality_name', 150);
            $table->string('province_name', 150);
            $table->string('postal_code', 4)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['buyer_account_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_addresses');
    }
};
