<?php

namespace Database\Seeders;

use App\Models\Accounts\LogisticsAccount;
use App\Models\Delivery\LogisticsFacility;
use Illuminate\Database\Seeder;

class SariDemoLogisticsFacilitiesSeeder extends Seeder
{
    public function run(): void
    {
        $logisticsId = LogisticsAccount::query()
            ->where('email', 'logistics@gmail.com')
            ->value('id');

        if (!$logisticsId) {
            $this->command?->warn(
                'logistics@gmail.com does not exist yet. Run TestAccountPhilippineLocationsSeeder first.'
            );

            return;
        }

        $facilities = [
            [
                'code' => 'SARI-MAKATI-CENTRAL',
                'name' => 'SARI Makati Sorting Facility',
                'address' => 'Makati Central Post Office, Malugay, Makati, Metro Manila, Philippines',
                'province_name' => 'Metro Manila',
                'municipality_name' => 'Makati',
                'barangay_name' => 'San Antonio',
                'latitude' => 14.5614240,
                'longitude' => 121.0146940,
            ],
            [
                'code' => 'SARI-BACOOR',
                'name' => 'SARI Bacoor Sorting Facility',
                'address' => 'SM City Bacoor, General Emilio Aguinaldo Highway, Bacoor, Cavite, Philippines',
                'province_name' => 'Cavite',
                'municipality_name' => 'Bacoor',
                'barangay_name' => 'Habay',
                'latitude' => 14.4451000,
                'longitude' => 120.9511000,
            ],
        ];

        foreach ($facilities as $facility) {
            LogisticsFacility::query()->updateOrCreate(
                ['code' => $facility['code']],
                array_merge(
                    $facility,
                    [
                        'logistics_account_id' => $logisticsId,
                        'is_active' => true,
                    ]
                )
            );
        }

        /*
         * Older demo builds seeded PITX as a third hub. It is not needed for
         * the default Taguig → Imus demo. Preserve it only when historical
         * route snapshots reference it; otherwise remove the unused test row.
         */
        $legacyPitx = LogisticsFacility::query()
            ->where('code', 'SARI-PITX')
            ->first();

        if ($legacyPitx) {
            if ($legacyPitx->routeStops()->exists()) {
                $legacyPitx->forceFill(['is_active' => false])->save();
            } else {
                $legacyPitx->delete();
            }
        }
    }
}
