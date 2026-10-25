<?php

use App\Providers\AppServiceProvider;
use App\Providers\SellerShippingServiceProvider;
use App\Providers\SellerCenterServiceProvider;
use App\Providers\FinanceServiceProvider;

return [
    AppServiceProvider::class,
    SellerShippingServiceProvider::class,
    SellerCenterServiceProvider::class,
    FinanceServiceProvider::class,
];
