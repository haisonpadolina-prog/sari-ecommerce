<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compliance_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_account_id')->constrained('seller_accounts')->cascadeOnDelete();
            $table->string('sender_role'); // seller | admin
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compliance_messages');
    }
};
