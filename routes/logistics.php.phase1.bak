<?php

use App\Http\Controllers\LogisticsAccountManagementController;
use App\Http\Controllers\LogisticsDashboardController;
use App\Http\Controllers\LogisticsDeliveryAssignmentController;
use App\Http\Controllers\LogisticsDeliveryMonitoringController;
use App\Http\Controllers\LogisticsIncomingParcelController;
use App\Http\Controllers\LogisticsMessageController;
use App\Http\Controllers\LogisticsParcelSortingController;
use App\Http\Controllers\LogisticsPickupRequestController;
use App\Http\Controllers\LogisticsReportsController;
use App\Http\Controllers\LogisticsRiderApplicationController;
use App\Http\Controllers\LogisticsRiderManagementController;
use App\Http\Controllers\RiderRegistrationController;
use App\Http\Middleware\EnsureLogisticsAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC RIDER → LOGISTICS APPLICATION FLOW
|--------------------------------------------------------------------------
| Rider registration is intentionally separate from the standard account
| wizard. A Rider first chooses an active Logistics provider, then submits
| an application directly to that provider.
*/
Route::get('/register/rider', [RiderRegistrationController::class, 'index'])
    ->name('rider.logistics.index');

Route::get('/register/rider/{logistics}', [RiderRegistrationController::class, 'create'])
    ->whereNumber('logistics')
    ->name('rider.logistics.apply');

Route::post('/register/rider/{logistics}', [RiderRegistrationController::class, 'store'])
    ->whereNumber('logistics')
    ->name('rider.logistics.submit');

Route::prefix('logistics')
    ->name('logistics.')
    ->middleware(EnsureLogisticsAuthenticated::class)
    ->group(function (): void {
        Route::get('/', [LogisticsDashboardController::class, 'index'])->name('dashboard');

        Route::get('/rider-applications', [LogisticsRiderApplicationController::class, 'index'])->name('rider-applications');
        Route::post('/rider-applications/{application}/approve', [LogisticsRiderApplicationController::class, 'approve'])->name('rider-applications.approve');
        Route::post('/rider-applications/{application}/reject', [LogisticsRiderApplicationController::class, 'reject'])->name('rider-applications.reject');
        Route::get('/rider-applications/{application}/document/{document}', [LogisticsRiderApplicationController::class, 'document'])
            ->whereIn('document', ['id', 'orcr'])->name('rider-applications.document');

        Route::get('/rider-management', [LogisticsRiderManagementController::class, 'index'])->name('rider-management');
        Route::get('/pickup-requests', [LogisticsPickupRequestController::class, 'index'])->name('pickup-requests');

        Route::get('/incoming-parcels', [LogisticsIncomingParcelController::class, 'index'])->name('incoming-parcels');
        Route::post('/incoming-parcels/{order}/receive', [LogisticsIncomingParcelController::class, 'receive'])->name('incoming-parcels.receive');

        Route::get('/parcel-sorting', [LogisticsParcelSortingController::class, 'index'])->name('parcel-sorting');
        Route::post('/parcel-sorting/{parcel}/sort', [LogisticsParcelSortingController::class, 'sort'])->name('parcel-sorting.sort');

        Route::get('/delivery-assignment', [LogisticsDeliveryAssignmentController::class, 'index'])->name('delivery-assignment');
        Route::post('/delivery-assignment/{order}/assign', [LogisticsDeliveryAssignmentController::class, 'assign'])->name('delivery-assignment.assign');

        Route::get('/delivery-monitoring', [LogisticsDeliveryMonitoringController::class, 'index'])->name('delivery-monitoring');
        Route::get('/reports', [LogisticsReportsController::class, 'index'])->name('reports');

        Route::get('/messages', [LogisticsMessageController::class, 'index'])->name('messages');
        Route::post('/messages/{rider}', [LogisticsMessageController::class, 'send'])->name('messages.send');

        Route::get('/account-management', [LogisticsAccountManagementController::class, 'index'])->name('account-management');
        Route::patch('/account-management', [LogisticsAccountManagementController::class, 'update'])->name('account-management.update');

        Route::post('/logout', function (Request $request) {
            $request->session()->forget([
                'is_logistics', 'logistics_account_id', 'logistics_email', 'logistics_name',
            ]);
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login');
        })->name('logout');
    });
