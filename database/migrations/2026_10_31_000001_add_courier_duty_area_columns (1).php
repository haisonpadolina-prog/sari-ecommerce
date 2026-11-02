<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courier_accounts', function (Blueprint $table): void {
            $table->string('duty_province_name', 150)->nullable()->index();
            $table->string('duty_municipality_name', 150)->nullable()->index();
            $table->string('duty_barangay_name', 150)->nullable()->index();
        });

        // Compatibility for already-approved riders. Admins can later change their
        // duty area independently from their registration/home address.
        DB::table('courier_accounts')->update([
            'duty_province_name' => DB::raw('province_name'),
            'duty_municipality_name' => DB::raw('municipality_name'),
            'duty_barangay_name' => DB::raw('barangay_name'),
        ]);
    }

    public function down(): void
    {
        Schema::table('courier_accounts', function (Blueprint $table): void {
            $table->dropColumn([
                'duty_province_name',
                'duty_municipality_name',
                'duty_barangay_name',
            ]);
        });
    }
};
