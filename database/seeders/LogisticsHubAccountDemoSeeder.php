<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class LogisticsHubAccountDemoSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Parent Logistics Provider
        |--------------------------------------------------------------------------
        */

        $logistics = DB::table('logistics_accounts')
            ->where('email', 'logistics@gmail.com')
            ->first();

        if (! $logistics) {
            throw new RuntimeException(
                'Demo Logistics account logistics@gmail.com was not found.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | San Pablo Hub
        |--------------------------------------------------------------------------
        */

        $sanPabloHub = DB::table('logistics_facilities')
            ->where(
                'logistics_account_id',
                $logistics->id
            )
            ->where(function ($query): void {
                $query
                    ->whereRaw(
                        'LOWER(name) LIKE ?',
                        ['%san pablo%']
                    )
                    ->orWhereRaw(
                        'LOWER(municipality_name) LIKE ?',
                        ['%san pablo%']
                    );
            })
            ->first();

        if (! $sanPabloHub) {
            throw new RuntimeException(
                'San Pablo logistics hub was not found.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sta. Cruz Hub
        |--------------------------------------------------------------------------
        */

        $staCruzHub = DB::table('logistics_facilities')
            ->where(
                'logistics_account_id',
                $logistics->id
            )
            ->where(function ($query): void {
                $query
                    ->whereRaw(
                        'LOWER(name) LIKE ?',
                        ['%sta%cruz%']
                    )
                    ->orWhereRaw(
                        'LOWER(name) LIKE ?',
                        ['%santa cruz%']
                    )
                    ->orWhereRaw(
                        'LOWER(municipality_name) LIKE ?',
                        ['%sta%cruz%']
                    )
                    ->orWhereRaw(
                        'LOWER(municipality_name) LIKE ?',
                        ['%santa cruz%']
                    );
            })
            ->first();

        if (! $staCruzHub) {
            throw new RuntimeException(
                'Sta. Cruz logistics hub was not found.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | San Pablo Hub Operator
        |--------------------------------------------------------------------------
        */

        DB::table('logistics_hub_accounts')
            ->updateOrInsert(
                [
                    'email' =>
                        'logistics.sanpablo@sari.test',
                ],
                [
                    'logistics_account_id' =>
                        $logistics->id,

                    'logistics_facility_id' =>
                        $sanPabloHub->id,

                    'name' =>
                        'San Pablo Hub Operator',

                    'password' =>
                        Hash::make('logistics123'),

                    'role' =>
                        'hub_operator',

                    'account_status' =>
                        'active',

                    'updated_at' =>
                        now(),

                    'created_at' =>
                        now(),
                ]
            );

        /*
        |--------------------------------------------------------------------------
        | Sta. Cruz Hub Operator
        |--------------------------------------------------------------------------
        */

        DB::table('logistics_hub_accounts')
            ->updateOrInsert(
                [
                    'email' =>
                        'logistics.stacruz@sari.test',
                ],
                [
                    'logistics_account_id' =>
                        $logistics->id,

                    'logistics_facility_id' =>
                        $staCruzHub->id,

                    'name' =>
                        'Sta. Cruz Hub Operator',

                    'password' =>
                        Hash::make('logistics123'),

                    'role' =>
                        'hub_operator',

                    'account_status' =>
                        'active',

                    'updated_at' =>
                        now(),

                    'created_at' =>
                        now(),
                ]
            );

        $this->command?->info(
            'Demo Logistics hub operator accounts created.'
        );

        $this->command?->line(
            'San Pablo: logistics.sanpablo@sari.test / logistics123'
        );

        $this->command?->line(
            'Sta. Cruz: logistics.stacruz@sari.test / logistics123'
        );
    }
}