<?php

namespace Database\Seeders;

use App\Models\Accounts\AdminAccount;
use App\Models\Accounts\BuyerAccount;
use App\Models\Accounts\CourierAccount;
use App\Models\Accounts\LogisticsAccount;
use App\Models\Accounts\SellerAccount;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Registration\RegistrationApplication;
use App\Services\Delivery\DeliveryRouteService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class TestAccountPhilippineLocationsSeeder extends Seeder
{
    public function run(): void
    {
        $buyerDemo = config('sari_demo_locations.accounts.buyer');
        $sellerDemo = config('sari_demo_locations.accounts.seller');
        $logisticsDemo = config('sari_demo_locations.accounts.logistics');
        $courierDemo = config('sari_demo_locations.accounts.courier');

        $buyer = BuyerAccount::query()->updateOrCreate(
            ['email' => $buyerDemo['email']],
            [
                'registration_application_id' => null,
                'last_name' => 'Buyer',
                'first_name' => 'SARI',
                'middle_initial' => null,
                'sex' => 'Male',
                'contact_no' => '09123456789',
                'birthday' => '2000-01-01',
                'age' => 26,
                'province_code' => $buyerDemo['province_code'],
                'province_name' => $buyerDemo['province_name'],
                'municipality_code' => $buyerDemo['municipality_code'],
                'municipality_name' => $buyerDemo['municipality_name'],
                'barangay_code' => $buyerDemo['barangay_code'],
                'barangay_name' => $buyerDemo['barangay_name'],
                'street_address' => $buyerDemo['street_address'],
                'password' => Hash::make('buyer123'),
                'id_path' => null,
                'account_status' => 'active',
                'approved_at' => now(),
            ]
        );

        $seller = SellerAccount::query()->firstOrCreate(
            ['email' => $sellerDemo['email']],
            ['store_name' => 'SARI Seller Store']
        );

        // Seller has these DB columns even though older model fillable lists may
        // not include every registration field, so forceFill is intentional.
        $seller->forceFill([
            'store_name' => 'SARI Seller Store',
            'first_name' => 'SARI',
            'last_name' => 'Seller',
            'contact_no' => '09123456788',
            'province_code' => $sellerDemo['province_code'],
            'province_name' => $sellerDemo['province_name'],
            'municipality_code' => $sellerDemo['municipality_code'],
            'municipality_name' => $sellerDemo['municipality_name'],
            'barangay_code' => $sellerDemo['barangay_code'],
            'barangay_name' => $sellerDemo['barangay_name'],
            'street_address' => $sellerDemo['street_address'],
            'pickup_address' => $sellerDemo['full_address'],
            'password' => Hash::make('seller123'),
            'account_status' => 'active',
            'registration_status' => 'approved',
            'approved_at' => now(),
        ])->save();

        $logistics = LogisticsAccount::query()->updateOrCreate(
            ['email' => $logisticsDemo['email']],
            [
                'registration_application_id' => null,
                'last_name' => 'Logistics',
                'first_name' => 'SARI',
                'middle_initial' => null,
                'sex' => 'N/A',
                'contact_no' => '09000000000',
                'birthday' => '2000-01-01',
                'age' => 26,
                'province_code' => $logisticsDemo['province_code'],
                'province_name' => $logisticsDemo['province_name'],
                'municipality_code' => $logisticsDemo['municipality_code'],
                'municipality_name' => $logisticsDemo['municipality_name'],
                'barangay_code' => $logisticsDemo['barangay_code'],
                'barangay_name' => $logisticsDemo['barangay_name'],
                'street_address' => $logisticsDemo['street_address'],
                'business_name' => 'SARI Logistics',
                'password' => Hash::make('logistics123'),
                'id_path' => null,
                'business_permit_path' => null,
                'account_status' => 'active',
                'approved_at' => now(),
            ]
        );

        /*
         * Seed facilities only after the database-backed Logistics test account
         * exists, so every demo hub has a real owner.
         */
        $this->call(SariDemoLogisticsFacilitiesSeeder::class);

        $riderApplication = RegistrationApplication::query()->updateOrCreate(
            ['email' => $courierDemo['email']],
            [
                'logistics_account_id' => $logistics->id,
                'role' => 'rider',
                'last_name' => 'Rider',
                'first_name' => 'SARI',
                'middle_initial' => null,
                'sex' => 'Male',
                'contact_no' => '09123456787',
                'birthday' => '2000-01-01',
                'age' => 26,
                'province_code' => $courierDemo['province_code'],
                'province_name' => $courierDemo['province_name'],
                'municipality_code' => $courierDemo['municipality_code'],
                'municipality_name' => $courierDemo['municipality_name'],
                'barangay_code' => $courierDemo['barangay_code'],
                'barangay_name' => $courierDemo['barangay_name'],
                'street_address' => $courierDemo['street_address'],
                'password' => Hash::make('courier123'),
                'vehicle_type' => 'Motorcycle',
                'plate_number' => 'TEST-001',
                'id_path' => null,
                'orcr_path' => null,
                'status' => 'approved',
                'reviewed_at' => now(),
                'approved_at' => now(),
                'rejected_at' => null,
            ]
        );

        CourierAccount::query()->updateOrCreate(
            ['email' => $courierDemo['email']],
            [
                'registration_application_id' => $riderApplication->id,
                'logistics_account_id' => $logistics->id,
                'last_name' => 'Rider',
                'first_name' => 'SARI',
                'middle_initial' => null,
                'sex' => 'Male',
                'contact_no' => '09123456787',
                'birthday' => '2000-01-01',
                'age' => 26,
                'province_code' => $courierDemo['province_code'],
                'province_name' => $courierDemo['province_name'],
                'municipality_code' => $courierDemo['municipality_code'],
                'municipality_name' => $courierDemo['municipality_name'],
                'barangay_code' => $courierDemo['barangay_code'],
                'barangay_name' => $courierDemo['barangay_name'],
                'street_address' => $courierDemo['street_address'],
                'password' => Hash::make('courier123'),
                'vehicle_type' => 'Motorcycle',
                'plate_number' => 'TEST-001',
                'account_status' => 'active',
                'approved_at' => now(),
                'availability_status' => 'online',
                'rating' => 5.00,
            ]
        );

        // Admin has no address/location columns in the current schema.
        AdminAccount::query()->updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'SARI Administrator',
                'password' => Hash::make('admin123'),
            ]
        );

        MarketplaceOrder::query()
            ->where(function ($query) use ($buyer, $seller): void {
                $query
                    ->where('buyer_account_id', $buyer->id)
                    ->orWhere('seller_account_id', $seller->id);
            })
            ->get()
            ->each(function (MarketplaceOrder $order) use (
                $buyer,
                $seller,
                $buyerDemo,
                $sellerDemo
            ): void {
                if ((int) $order->buyer_account_id === (int) $buyer->id) {
                    $order->buyer_name = 'SARI Buyer';
                    $order->buyer_address = $buyerDemo['full_address'];
                }

                if ((int) $order->seller_account_id === (int) $seller->id) {
                    $order->pickup_name = 'SARI Seller Store';
                    $order->pickup_address = $sellerDemo['full_address'];
                }

                $order->save();

                if (Schema::hasTable('delivery_route_stops')) {
                    app(DeliveryRouteService::class)->ensureStops($order);
                }
            });
    }
}
