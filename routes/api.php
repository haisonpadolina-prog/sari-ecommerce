<?php

use App\Http\Controllers\Api\V1\MobileAuthController;
use App\Http\Controllers\Api\V1\MobileBuyerOrderController;
use App\Http\Controllers\Api\V1\MobileCatalogController;
use App\Http\Middleware\Api\EnsureMobileRole;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('health', fn () => response()->json([
        'success' => true,
        'data' => ['service' => 'sari-api', 'status' => 'ok', 'version' => 'v1'],
    ]))->name('api.v1.health');

    Route::get('products', [MobileCatalogController::class, 'index']);
    Route::get('products/{product}', [MobileCatalogController::class, 'show'])->whereNumber('product');

    Route::post('auth/login', [MobileAuthController::class, 'login'])
        ->middleware('throttle:5,1');

    Route::middleware(['auth:sanctum', EnsureMobileRole::class])->group(function (): void {
        Route::get('auth/me', [MobileAuthController::class, 'me']);
        Route::post('auth/logout', [MobileAuthController::class, 'logout']);
        Route::post('auth/logout-all', [MobileAuthController::class, 'logoutAll'])
            ->middleware('throttle:5,1');
    });

    Route::middleware(['auth:sanctum', EnsureMobileRole::class.':buyer'])
        ->prefix('buyer')->group(function (): void {
            Route::get('orders', [MobileBuyerOrderController::class, 'index']);
            Route::get('orders/{order}', [MobileBuyerOrderController::class, 'show'])
                ->whereNumber('order');
        });
});
