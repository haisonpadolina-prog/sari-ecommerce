<?php

namespace Database\Seeders;

use App\Services\Delivery\DeliveryRouteService;
use Illuminate\Database\Seeder;

class RouteUnownedLogisticsOrdersSeeder extends Seeder
{
    public function run(): void
    {
        $limit = 200;

        $assigned = app(DeliveryRouteService::class)
            ->assignUnownedOrders($limit);

        $this->command?->info(
            "SARI assigned {$assigned} legacy/unowned order(s) to eligible Logistics networks."
        );
    }
}
