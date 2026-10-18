<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LogisticsDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Existing local development logistics account
        $logistics = DB::table('logistics_accounts')
            ->where('email', 'logistics@gmail.com')
            ->first();

        if (! $logistics) {
            $this->command->error(
                'Logistics development account not found. Login once using logistics@gmail.com first.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | SORTING FACILITIES
        |--------------------------------------------------------------------------
        */

        DB::table('logistics_facilities')->updateOrInsert(
            ['code' => 'DEMO-SAN-PABLO-HUB'],
            [
                'logistics_account_id' => $logistics->id,
                'name' => 'SARI San Pablo Sorting Hub',
                'address' => 'San Pablo City, Laguna',
                'province_name' => 'Laguna',
                'municipality_name' => 'San Pablo City',
                'barangay_name' => null,
                'latitude' => 14.0683,
                'longitude' => 121.3256,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('logistics_facilities')->updateOrInsert(
            ['code' => 'DEMO-STA-CRUZ-HUB'],
            [
                'logistics_account_id' => $logistics->id,
                'name' => 'SARI Sta. Cruz Sorting Hub',
                'address' => 'Santa Cruz, Laguna',
                'province_name' => 'Laguna',
                'municipality_name' => 'Santa Cruz',
                'barangay_name' => null,
                'latitude' => 14.2814,
                'longitude' => 121.4161,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | APPROVED RIDER
        |--------------------------------------------------------------------------
        */

        DB::table('registration_applications')->updateOrInsert(
            ['email' => 'demo.rider@sari.test'],
            [
                'role' => 'rider',
                'logistics_account_id' => $logistics->id,
                'last_name' => 'Rider',
                'first_name' => 'Demo',
                'middle_initial' => null,
                'sex' => 'Male',
                'contact_no' => '09170000001',
                'birthday' => '2000-01-01',
                'age' => 26,

                'province_code' => 'LAG',
                'province_name' => 'Laguna',

                'municipality_code' => 'SAN-PABLO',
                'municipality_name' => 'San Pablo City',

                'barangay_code' => 'DEMO-BRGY',
                'barangay_name' => 'Demo Barangay',

                'street_address' => 'Demo address',
                'password' => Hash::make('rider123'),
                'vehicle_type' => 'Motorcycle',
                'plate_number' => 'DEMO-001',

                'status' => 'approved',
                'reviewed_at' => $now,
                'approved_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $approvedApplication = DB::table('registration_applications')
            ->where('email', 'demo.rider@sari.test')
            ->first();

        DB::table('courier_accounts')->updateOrInsert(
            ['email' => 'demo.rider@sari.test'],
            [
                'registration_application_id' => $approvedApplication->id,
                'logistics_account_id' => $logistics->id,

                'last_name' => 'Rider',
                'first_name' => 'Demo',
                'middle_initial' => null,
                'sex' => 'Male',
                'contact_no' => '09170000001',
                'birthday' => '2000-01-01',
                'age' => 26,

                'province_code' => 'LAG',
                'province_name' => 'Laguna',

                'municipality_code' => 'SAN-PABLO',
                'municipality_name' => 'San Pablo City',

                'barangay_code' => 'DEMO-BRGY',
                'barangay_name' => 'Demo Barangay',

                'street_address' => 'Demo rider address',
                'password' => Hash::make('rider123'),

                'vehicle_type' => 'Motorcycle',
                'vehicle_model' => 'Honda Click',
                'plate_number' => 'DEMO-001',
                'license_number' => 'DEMO-LICENSE-001',

                'account_status' => 'active',
                'availability_status' => 'online',
                'rating' => 5.00,
                'approved_at' => $now,

                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | PENDING RIDER APPLICATION
        |--------------------------------------------------------------------------
        */

        DB::table('registration_applications')->updateOrInsert(
            ['email' => 'pending.rider@sari.test'],
            [
                'role' => 'rider',
                'logistics_account_id' => $logistics->id,

                'last_name' => 'Applicant',
                'first_name' => 'Pending',
                'middle_initial' => null,
                'sex' => 'Male',
                'contact_no' => '09170000002',
                'birthday' => '2001-01-01',
                'age' => 25,

                'province_code' => 'LAG',
                'province_name' => 'Laguna',

                'municipality_code' => 'STA-CRUZ',
                'municipality_name' => 'Santa Cruz',

                'barangay_code' => 'DEMO-BRGY-2',
                'barangay_name' => 'Demo Barangay 2',

                'street_address' => 'Demo applicant address',
                'password' => Hash::make('rider123'),

                'vehicle_type' => 'Motorcycle',
                'plate_number' => 'DEMO-002',

                'status' => 'pending',

                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $this->command->info('Logistics demo data created successfully.');
    }
}