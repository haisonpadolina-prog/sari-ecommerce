<?php

use App\Http\Controllers\Seller\SellerShippingController;
use App\Http\Middleware\Seller\EnsureSellerAccountAccessible;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'web',
    EnsureSellerAccountAccessible::class,
])
    ->prefix('seller')
    ->name('seller.')
    ->group(function (): void {
        Route::get('/shipping', [SellerShippingController::class, 'index'])
            ->name('shipping.index');

        Route::get('/shipping/{order}', [SellerShippingController::class, 'show'])
            ->whereNumber('order')
            ->name('shipping.show');

        Route::get('/shipping/{order}/live-state', [SellerShippingController::class, 'liveState'])
            ->whereNumber('order')
            ->name('shipping.live-state');
    });
