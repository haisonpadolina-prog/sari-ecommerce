<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_warnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_account_id')->constrained('seller_accounts')->cascadeOnDelete();
            $table->foreignId('seller_product_id')->nullable()->constrained('seller_products')->nullOnDelete();
            $table->unsignedTinyInteger('warning_number');
            $table->string('reason');
            $table->text('admin_note')->nullable();
            $table->timestamp('issued_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_warnings');
    }
};
