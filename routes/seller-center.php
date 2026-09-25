<?php

use App\Http\Controllers\BuyerReturnRequestController;
use App\Http\Controllers\SellerComplianceCenterController;
use App\Http\Controllers\SellerFinanceController;
use App\Http\Controllers\SellerInventoryController;
use App\Http\Controllers\SellerNotificationCenterController;
use App\Http\Controllers\SellerReturnsController;
use App\Http\Controllers\SellerReviewsCenterController;
use App\Http\Controllers\SellerStoreController;
use App\Http\Controllers\SellerVoucherController;
use App\Http\Middleware\EnsureSellerAccountAccessible;
use App\Http\Middleware\EnsureSellerNotRestricted;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function (): void {
    Route::middleware(EnsureSellerNotRestricted::class)
        ->prefix('seller')
        ->name('seller.')
        ->group(function (): void {
            Route::get('/inventory', [SellerInventoryController::class, 'index'])->name('inventory.index');
            Route::post('/inventory/{product}/adjust', [SellerInventoryController::class, 'adjust'])->name('inventory.adjust');

            Route::get('/returns', [SellerReturnsController::class, 'index'])->name('returns.index');
            Route::post('/returns/{returnRequest}/review', [SellerReturnsController::class, 'review'])->name('returns.review');
            Route::post('/returns/{returnRequest}/returned', [SellerReturnsController::class, 'markReturned'])->name('returns.returned');
            Route::post('/returns/{returnRequest}/refund', [SellerReturnsController::class, 'refund'])->name('returns.refund');

            Route::get('/vouchers', [SellerVoucherController::class, 'index'])->name('vouchers.index');
            Route::post('/vouchers', [SellerVoucherController::class, 'store'])->name('vouchers.store');
            Route::post('/vouchers/{voucher}/toggle', [SellerVoucherController::class, 'toggle'])->name('vouchers.toggle');
            Route::delete('/vouchers/{voucher}', [SellerVoucherController::class, 'destroy'])->name('vouchers.destroy');

            Route::get('/finance', [SellerFinanceController::class, 'index'])->name('finance.index');

            Route::get('/reviews-ratings', [SellerReviewsCenterController::class, 'index'])->name('reviews-center.index');
            Route::post('/reviews-ratings/{review}/reply', [SellerReviewsCenterController::class, 'reply'])->name('reviews-center.reply');

            Route::get('/notifications', [SellerNotificationCenterController::class, 'index'])->name('notifications.index');
            Route::post('/notifications/read-all', [SellerNotificationCenterController::class, 'readAll'])->name('notifications.read-all');
            Route::post('/notifications/{notification}/read', [SellerNotificationCenterController::class, 'read'])->name('notifications.read');

            Route::get('/store-management', [SellerStoreController::class, 'index'])->name('store.index');
            Route::patch('/store-management', [SellerStoreController::class, 'update'])->name('store.update');
        });

    Route::middleware(EnsureSellerAccountAccessible::class)
        ->prefix('seller')
        ->name('seller.')
        ->group(function (): void {
            Route::get('/compliance-center', [SellerComplianceCenterController::class, 'index'])
                ->name('compliance-center.index');
        });

    Route::post('/buyer/orders/{order}/return-request', [BuyerReturnRequestController::class, 'store'])
        ->name('buyer.orders.return-request');
});
