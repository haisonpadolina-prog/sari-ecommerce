<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_application_id')
                ->nullable()
                ->unique()
                ->constrained('registration_applications')
                ->nullOnDelete();

            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_initial', 5)->nullable();
            $table->string('sex', 20);
            $table->string('email')->unique();
            $table->string('contact_no', 30);
            $table->date('birthday');
            $table->unsignedTinyInteger('age');

            $table->string('province_code', 30);
            $table->string('province_name');
            $table->string('municipality_code', 30);
            $table->string('municipality_name');
            $table->string('barangay_code', 30);
            $table->string('barangay_name');
            $table->text('street_address');

            $table->string('password');

            $table->string('vehicle_type');
            $table->string('plate_number');

            $table->string('orcr_path')->nullable();
            $table->string('id_path')->nullable();

            $table->string('account_status', 20)->default('active')->index();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_accounts');
    }
};
