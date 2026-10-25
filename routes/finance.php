<?php

use App\Http\Controllers\Finance\AdminFinanceController;
use App\Http\Controllers\Finance\BranchTreasuryController;
use App\Http\Controllers\Finance\CourierRemittanceController;
use App\Http\Controllers\Finance\FinancePayoutController;
use App\Http\Controllers\Finance\LogisticsFinanceController;
use App\Http\Controllers\Finance\LogisticsRemittanceController;
use App\Http\Middleware\Courier\CourierOnly;
use App\Http\Middleware\Logistics\EnsureLogisticsAuthenticated;
use App\Http\Middleware\Logistics\EnsureLogisticsManager;
use App\Http\Middleware\Logistics\EnsureLogisticsOperatingBranch;
use App\Http\Middleware\Seller\EnsureSellerNotRestricted;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function (): void {
    Route::middleware(CourierOnly::class)
        ->prefix('courier')
        ->name('courier.')
        ->group(function (): void {
            Route::get('/cod-remittance', [CourierRemittanceController::class, 'index'])
                ->name('remittances.index');
            Route::post('/cod-remittance', [CourierRemittanceController::class, 'store'])
                ->middleware('throttle:10,1')
                ->name('remittances.store');
        });

    Route::middleware(EnsureSellerNotRestricted::class)
        ->prefix('seller')
        ->name('seller.')
        ->group(function (): void {
            Route::post('/finance/payout', [FinancePayoutController::class, 'sellerStore'])
                ->middleware('throttle:6,1')
                ->name('finance.payout');
        });

    Route::middleware([
        EnsureLogisticsAuthenticated::class,
        EnsureLogisticsManager::class,
    ])
        ->prefix('logistics')
        ->name('logistics.')
        ->group(function (): void {
            // Company-wide finance remains independent from the selected branch.
            Route::get('/finance', [LogisticsFinanceController::class, 'index'])
                ->name('finance.index');
            Route::post('/finance/payout', [FinancePayoutController::class, 'logisticsStore'])
                ->middleware('throttle:6,1')
                ->name('finance.payout');

            Route::middleware(EnsureLogisticsOperatingBranch::class)
                ->group(function (): void {
                    // Rider physical COD cash -> currently selected branch.
                    Route::get('/cod-remittance', [LogisticsRemittanceController::class, 'index'])
                        ->name('remittances.index');
                    Route::get('/cod-remittance/{batch}', [LogisticsRemittanceController::class, 'show'])
                        ->whereNumber('batch')
                        ->middleware('signed:relative')
                        ->name('remittances.show');
                    Route::post('/cod-remittance/{batch}/verify', [LogisticsRemittanceController::class, 'verify'])
                        ->whereNumber('batch')
                        ->middleware('throttle:20,1')
                        ->name('remittances.verify');

                    // Verified branch cash -> Main Branch central treasury.
                    Route::get('/branch-treasury', [BranchTreasuryController::class, 'index'])
                        ->name('branch-treasury.index');
                    Route::post('/branch-treasury', [BranchTreasuryController::class, 'store'])
                        ->middleware('throttle:10,1')
                        ->name('branch-treasury.store');
                    Route::get('/branch-treasury/{batch}', [BranchTreasuryController::class, 'show'])
                        ->whereNumber('batch')
                        ->middleware('signed:relative')
                        ->name('branch-treasury.show');
                    Route::post('/branch-treasury/{batch}/verify', [BranchTreasuryController::class, 'verify'])
                        ->whereNumber('batch')
                        ->middleware('throttle:20,1')
                        ->name('branch-treasury.verify');
                });
        });

    Route::prefix('admin/finance')
        ->name('admin.finance.')
        ->group(function (): void {
            Route::get('/', [AdminFinanceController::class, 'index'])->name('index');
            Route::post('/shipping-policy', [AdminFinanceController::class, 'updateShippingPolicy'])
                ->middleware('throttle:6,1')
                ->name('shipping-policy');
            Route::post('/platform-payout', [AdminFinanceController::class, 'requestPlatformPayout'])
                ->middleware('throttle:6,1')
                ->name('platform-payout');
            Route::post('/payouts/{payout}/approve', [AdminFinanceController::class, 'approve'])
                ->whereNumber('payout')
                ->name('payouts.approve');
            Route::post('/payouts/{payout}/reject', [AdminFinanceController::class, 'reject'])
                ->whereNumber('payout')
                ->name('payouts.reject');
            Route::post('/payouts/{payout}/paid', [AdminFinanceController::class, 'markPaid'])
                ->whereNumber('payout')
                ->name('payouts.paid');
        });
});
