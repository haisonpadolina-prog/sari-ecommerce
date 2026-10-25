<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ImusLastMileTestRiderSeeder extends Seeder
{
    public function run(): void
    {
        $logistics = DB::table('logistics_accounts')
            ->where('email', 'logistics@gmail.com')
            ->first();

        if (! $logistics) {
            $this->command?->error('Demo Logistics account logistics@gmail.com was not found.');
            return;
        }

        $now = now();
        $email = 'imus.rider@test.com';

        DB::table('registration_applications')->updateOrInsert(
            ['email' => $email],
            [
                'role' => 'rider',
                'logistics_account_id' => $logistics->id,
                'last_name' => 'Imus Rider',
                'first_name' => 'Test',
                'middle_initial' => null,
                'sex' => 'Male',
                'contact_no' => '09123456789',
                'birthday' => '2000-01-01',
                'age' => 26,
                'province_code' => 'DEMO-CAVITE',
                'province_name' => 'Cavite',
                // TEMPORARY: matches the current Last-Mile page logic,
                // which is deriving municipality from the destination hub.
                'municipality_code' => 'DEMO-BACOOR',
                'municipality_name' => 'Bacoor',
                'barangay_code' => 'DEMO-NEW-IMUS-GOV',
                'barangay_name' => 'New Imus City Government Center',
                'street_address' => 'New Imus City Government Center, Imus Boulevard, Cavite',
                'password' => Hash::make('rider123'),
                'vehicle_type' => 'Motorcycle',
                'plate_number' => 'IMUS-TEST-01',
                'status' => 'approved',
                'reviewed_at' => $now,
                'approved_at' => $now,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        $application = DB::table('registration_applications')
            ->where('email', $email)
            ->first();

        DB::table('courier_accounts')->updateOrInsert(
            ['email' => $email],
            [
                'registration_application_id' => $application->id,
                'logistics_account_id' => $logistics->id,
                'last_name' => 'Imus Rider',
                'first_name' => 'Test',
                'middle_initial' => null,
                'sex' => 'Male',
                'contact_no' => '09123456789',
                'birthday' => '2000-01-01',
                'age' => 26,
                'province_code' => 'DEMO-CAVITE',
                'province_name' => 'Cavite',
                // TEMPORARY: exact current Last-Mile service-area match.
                'municipality_code' => 'DEMO-BACOOR',
                'municipality_name' => 'Bacoor',
                'barangay_code' => 'DEMO-NEW-IMUS-GOV',
                'barangay_name' => 'New Imus City Government Center',
                'street_address' => 'New Imus City Government Center, Imus Boulevard, Cavite',
                'password' => Hash::make('rider123'),
                'vehicle_type' => 'Motorcycle',
                'vehicle_model' => 'Honda Click',
                'plate_number' => 'IMUS-TEST-01',
                'license_number' => 'DEMO-IMUS-001',
                'account_status' => 'active',
                'availability_status' => 'online',
                'rating' => 5.00,
                'approved_at' => $now,
                'deleted_at' => null,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        $this->command?->info('Imus last-mile test Rider created/repaired: imus.rider@test.com / rider123');
    }
}
