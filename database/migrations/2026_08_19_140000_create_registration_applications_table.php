<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_applications', function (Blueprint $table) {
            $table->id();

            $table->string('role', 20)->index();

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

            $table->string('business_name')->nullable();
            $table->string('line_of_business')->nullable();

            $table->string('vehicle_type')->nullable();
            $table->string('plate_number')->nullable();

            // Stored on the private local disk.
            $table->string('id_path')->nullable();
            $table->string('business_permit_path')->nullable();
            $table->string('orcr_path')->nullable();

            $table->string('status', 20)->default('pending')->index();
            $table->text('admin_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->timestamps();

            $table->index(['role', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_applications');
    }
};
