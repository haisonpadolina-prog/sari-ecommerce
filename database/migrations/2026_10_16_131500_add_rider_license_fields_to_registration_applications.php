<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_applications', function (Blueprint $table): void {
            if (!Schema::hasColumn('registration_applications', 'license_number')) {
                $table->string('license_number', 20)->nullable()->after('plate_number');
            }
            if (!Schema::hasColumn('registration_applications', 'license_expiry_date')) {
                $table->date('license_expiry_date')->nullable()->after('license_number');
            }
        });
    }

    public function down(): void
    {
        // Non-destructive by design: Rider application history must be preserved.
    }
};
