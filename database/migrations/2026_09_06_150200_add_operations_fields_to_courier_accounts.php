<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courier_accounts', function (Blueprint $table) {
            $table->string('availability_status', 20)->default('online')->index();
            $table->string('vehicle_model')->nullable();
            $table->string('license_number')->nullable();
            $table->decimal('rating', 3, 2)->default(5.00);
        });
    }

    public function down(): void
    {
        Schema::table('courier_accounts', function (Blueprint $table) {
            $table->dropColumn(['availability_status', 'vehicle_model', 'license_number', 'rating']);
        });
    }
};
