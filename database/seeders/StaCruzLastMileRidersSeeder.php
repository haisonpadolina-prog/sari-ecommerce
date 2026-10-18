<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaCruzLastMileRidersSeeder extends Seeder
{
    public function run(): void
    {
        $logistics = DB::table('logistics_accounts')
            ->where('email', 'logistics@gmail.com')
            ->first();

        if (! $logistics) {
            $this->command->error(
                'Demo Logistics account not found.'
            );

            return;
        }

        $now = now();

        $riders = [
            [
                'first_name' => 'Ben',
                'last_name' => 'Bubukal',
                'barangay' => 'Bubukal',
                'barangay_code' => 'BUBUKAL',
                'email' => 'rider.bubukal@sari.test',
                'plate' => 'SC-001',
            ],
            [
                'first_name' => 'Paolo',
                'last_name' => 'Pagsawitan',
                'barangay' => 'Pagsawitan',
                'barangay_code' => 'PAGSAWITAN',
                'email' => 'rider.pagsawitan@sari.test',
                'plate' => 'SC-002',
            ],
            [
                'first_name' => 'Gab',
                'last_name' => 'Gatid',
                'barangay' => 'Gatid',
                'barangay_code' => 'GATID',
                'email' => 'rider.gatid@sari.test',
                'plate' => 'SC-003',
            ],
            [
                'first_name' => 'Pat',
                'last_name' => 'Patimbao',
                'barangay' => 'Patimbao',
                'barangay_code' => 'PATIMBAO',
                'email' => 'rider.patimbao@sari.test',
                'plate' => 'SC-004',
            ],
            [
                'first_name' => 'Angel',
                'last_name' => 'Central',
                'barangay' => 'Santo Angel Central',
                'barangay_code' => 'SANTO-ANGEL-CENTRAL',
                'email' => 'rider.santoangel@sari.test',
                'plate' => 'SC-005',
            ],
        ];

        foreach ($riders as $index => $demo) {
            DB::table(
                'registration_applications'
            )->updateOrInsert(
                [
                    'email' =>
                        $demo['email'],
                ],
                [
                    'role' =>
                        'rider',

                    'logistics_account_id' =>
                        $logistics->id,

                    'last_name' =>
                        $demo['last_name'],

                    'first_name' =>
                        $demo['first_name'],

                    'middle_initial' =>
                        null,

                    'sex' =>
                        'Male',

                    'contact_no' =>
                        '091800000'
                        . str_pad(
                            (string) ($index + 1),
                            2,
                            '0',
                            STR_PAD_LEFT
                        ),

                    'birthday' =>
                        '2000-01-01',

                    'age' =>
                        26,

                    'province_code' =>
                        'LAG',

                    'province_name' =>
                        'Laguna',

                    'municipality_code' =>
                        'STA-CRUZ',

                    'municipality_name' =>
                        'Santa Cruz',

                    'barangay_code' =>
                        $demo['barangay_code'],

                    'barangay_name' =>
                        $demo['barangay'],

                    'street_address' =>
                        $demo['barangay']
                        . ', Santa Cruz, Laguna',

                    'password' =>
                        Hash::make('rider123'),

                    'vehicle_type' =>
                        'Motorcycle',

                    'plate_number' =>
                        $demo['plate'],

                    'status' =>
                        'approved',

                    'reviewed_at' =>
                        $now,

                    'approved_at' =>
                        $now,

                    'updated_at' =>
                        $now,

                    'created_at' =>
                        $now,
                ]
            );

            $application =
                DB::table(
                    'registration_applications'
                )
                    ->where(
                        'email',
                        $demo['email']
                    )
                    ->first();

            DB::table(
                'courier_accounts'
            )->updateOrInsert(
                [
                    'email' =>
                        $demo['email'],
                ],
                [
                    'registration_application_id' =>
                        $application->id,

                    'logistics_account_id' =>
                        $logistics->id,

                    'last_name' =>
                        $demo['last_name'],

                    'first_name' =>
                        $demo['first_name'],

                    'middle_initial' =>
                        null,

                    'sex' =>
                        'Male',

                    'contact_no' =>
                        '091800000'
                        . str_pad(
                            (string) ($index + 1),
                            2,
                            '0',
                            STR_PAD_LEFT
                        ),

                    'birthday' =>
                        '2000-01-01',

                    'age' =>
                        26,

                    'province_code' =>
                        'LAG',

                    'province_name' =>
                        'Laguna',

                    'municipality_code' =>
                        'STA-CRUZ',

                    'municipality_name' =>
                        'Santa Cruz',

                    'barangay_code' =>
                        $demo['barangay_code'],

                    'barangay_name' =>
                        $demo['barangay'],

                    'street_address' =>
                        $demo['barangay']
                        . ', Santa Cruz, Laguna',

                    'password' =>
                        Hash::make('rider123'),

                    'vehicle_type' =>
                        'Motorcycle',

                    'vehicle_model' =>
                        'Honda Click',

                    'plate_number' =>
                        $demo['plate'],

                    'license_number' =>
                        'DEMO-SC-'
                        . str_pad(
                            (string) ($index + 1),
                            3,
                            '0',
                            STR_PAD_LEFT
                        ),

                    'account_status' =>
                        'active',

                    'availability_status' =>
                        'online',

                    'rating' =>
                        5.00,

                    'approved_at' =>
                        $now,

                    'updated_at' =>
                        $now,

                    'created_at' =>
                        $now,
                ]
            );
        }

        $this->command->info(
            '5 Sta. Cruz last-mile Riders created.'
        );
    }
}