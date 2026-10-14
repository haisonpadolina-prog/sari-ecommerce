<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Hardcoded SARI demo/test locations
    |--------------------------------------------------------------------------
    |
    | These locations apply ONLY to the built-in test accounts. They use
    | real public Philippine landmarks so map demos stay geographically valid.
    | Normal registered users continue to use their saved account addresses.
    |
    */

    'enabled' => env('SARI_DEMO_LOCATIONS', true),

    'accounts' => [
        'buyer' => [
            'email' => 'buyer@gmail.com',
            'name' => 'SARI Buyer',
            'province_code' => 'DEMO-CAVITE',
            'province_name' => 'Cavite',
            'municipality_code' => 'DEMO-IMUS',
            'municipality_name' => 'Imus',
            'barangay_code' => 'DEMO-MALAGASANG-IG',
            'barangay_name' => 'Malagasang I-G',
            'street_address' => 'New Imus City Government Center, Imus Boulevard',
            'full_address' => 'New Imus City Government Center, Imus Boulevard, Malagasang I-G, Imus, Cavite, Philippines',
            'latitude' => 14.3899840,
            'longitude' => 120.9188600,
        ],

        'seller' => [
            'email' => 'seller@gmail.com',
            'name' => 'SARI Seller Store',
            'province_code' => 'DEMO-NCR',
            'province_name' => 'Metro Manila',
            'municipality_code' => 'DEMO-TAGUIG',
            'municipality_name' => 'Taguig',
            'barangay_code' => 'DEMO-FORT-BONIFACIO',
            'barangay_name' => 'Fort Bonifacio',
            'street_address' => 'Bonifacio High Street Central, 7th Avenue',
            'full_address' => 'Bonifacio High Street Central, 7th Avenue, Fort Bonifacio, Taguig, Metro Manila, Philippines',
            'latitude' => 14.5511925,
            'longitude' => 121.0493256,
        ],

        'logistics' => [
            'email' => 'logistics@gmail.com',
            'name' => 'SARI Logistics',
            'province_code' => 'DEMO-NCR',
            'province_name' => 'Metro Manila',
            'municipality_code' => 'DEMO-PARANAQUE',
            'municipality_name' => 'Parañaque',
            'barangay_code' => 'DEMO-TAMBO',
            'barangay_name' => 'Tambo',
            'street_address' => 'Parañaque Integrated Terminal Exchange, 1 Kennedy Road',
            'full_address' => 'Parañaque Integrated Terminal Exchange, 1 Kennedy Road, Tambo, Parañaque, Metro Manila, Philippines',
            'latitude' => 14.5101200,
            'longitude' => 120.9913000,
        ],

        'courier' => [
            'email' => 'courier@gmail.com',
            'name' => 'SARI Courier',
            'province_code' => 'DEMO-CAVITE',
            'province_name' => 'Cavite',
            'municipality_code' => 'DEMO-BACOOR',
            'municipality_name' => 'Bacoor',
            'barangay_code' => 'DEMO-HABAY',
            'barangay_name' => 'Habay',
            'street_address' => 'SM City Bacoor, General Emilio Aguinaldo Highway corner Tirona Highway',
            'full_address' => 'SM City Bacoor, General Emilio Aguinaldo Highway corner Tirona Highway, Bacoor, Cavite, Philippines',
            'latitude' => 14.4451000,
            'longitude' => 120.9511000,
        ],

        /*
         * admin_accounts currently has no address/location columns and Admin
         * does not participate in delivery routing. This is kept here only as
         * a consistent demo reference if Admin location is added later.
         */
        'admin' => [
            'email' => 'admin@gmail.com',
            'name' => 'SARI Administrator',
            'full_address' => 'Quezon City Hall, Elliptical Road, Diliman, Quezon City, Metro Manila, Philippines',
            'latitude' => 14.6467780,
            'longitude' => 121.0499190,
        ],
    ],

    /*
     * Demo facility records are seeded into logistics_facilities by
     * SariDemoLogisticsFacilitiesSeeder. DeliveryRouteService chooses the
     * nearest active origin and destination facility dynamically.
     */

];
