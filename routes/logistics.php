<?php

use App\Http\Controllers\Courier\RiderRegistrationController;
use App\Http\Controllers\Logistics\LogisticsAccountManagementController;
use App\Http\Controllers\Logistics\LogisticsActivityLogController;
use App\Http\Controllers\Logistics\LogisticsDashboardController;
use App\Http\Controllers\Logistics\LogisticsDeliveryAssignmentController;
use App\Http\Controllers\Logistics\LogisticsDeliveryMonitoringController;
use App\Http\Controllers\Logistics\LogisticsDeliveryRouteController;
use App\Http\Controllers\Logistics\LogisticsFacilityController;
use App\Http\Controllers\Logistics\LogisticsHubTransferController;
use App\Http\Controllers\Logistics\LogisticsIncomingParcelController;
use App\Http\Controllers\Logistics\LogisticsLastMileAssignmentController;
use App\Http\Controllers\Logistics\LogisticsMessageController;
use App\Http\Controllers\Logistics\LogisticsNotificationController;
use App\Http\Controllers\Logistics\LogisticsParcelSortingController;
use App\Http\Controllers\Logistics\LogisticsPickupRequestController;
use App\Http\Controllers\Logistics\LogisticsReportsController;
use App\Http\Controllers\Logistics\LogisticsRiderApplicationController;
use App\Http\Controllers\Logistics\LogisticsRiderManagementController;

use App\Http\Middleware\Logistics\EnsureLogisticsAuthenticated;
use App\Http\Middleware\Logistics\EnsureLogisticsManager;
use App\Http\Middleware\Logistics\LogCompletedLogisticsActivity;

use App\Services\Logistics\LogisticsActivityLogger;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC RIDER REGISTRATION
|--------------------------------------------------------------------------
*/

Route::get(
    '/register/rider',
    [RiderRegistrationController::class, 'index']
)->name('rider.logistics.index');

Route::get(
    '/register/rider/{logistics}',
    [RiderRegistrationController::class, 'create']
)
    ->whereNumber('logistics')
    ->name('rider.logistics.apply');

Route::post(
    '/register/rider/{logistics}/email/send',
    [RiderRegistrationController::class, 'sendEmailCode']
)
    ->whereNumber('logistics')
    ->middleware('throttle:8,1')
    ->name('rider.logistics.email.send');

Route::post(
    '/register/rider/{logistics}/email/verify',
    [RiderRegistrationController::class, 'verifyEmailCode']
)
    ->whereNumber('logistics')
    ->middleware('throttle:12,1')
    ->name('rider.logistics.email.verify');

Route::post(
    '/register/rider/{logistics}',
    [RiderRegistrationController::class, 'store']
)
    ->whereNumber('logistics')
    ->name('rider.logistics.submit');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED LOGISTICS ROUTES
|--------------------------------------------------------------------------
|
| Accessible to:
| - Main Logistics Manager
| - Assigned Hub Operator
|
| Activity middleware records eligible successful write operations.
| Individual controllers retain their existing authorization rules.
|
*/

Route::prefix('logistics')
    ->name('logistics.')
    ->middleware([
        EnsureLogisticsAuthenticated::class,
        LogCompletedLogisticsActivity::class,
    ])
    ->group(function (): void {

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/',
            [LogisticsDashboardController::class, 'index']
        )->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | PICKUP REQUESTS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/pickup-requests',
            [LogisticsPickupRequestController::class, 'index']
        )->name('pickup-requests');

        Route::post(
            '/pickup-requests/batch/verify',
            [LogisticsPickupRequestController::class, 'verifyBatch']
        )->name('pickup-requests.batch-verify');

        Route::post(
            '/pickup-requests/{order}/verify',
            [LogisticsPickupRequestController::class, 'verify']
        )
            ->whereNumber('order')
            ->name('pickup-requests.verify');

        /*
        |--------------------------------------------------------------------------
        | PICKUP ASSIGNMENT
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/delivery-assignment',
            [LogisticsDeliveryAssignmentController::class, 'index']
        )->name('delivery-assignment');

        Route::post(
            '/delivery-assignment/batch/assign',
            [LogisticsDeliveryAssignmentController::class, 'assignBatch']
        )->name('delivery-assignment.batch-assign');

        Route::post(
            '/delivery-assignment/{order}/assign',
            [LogisticsDeliveryAssignmentController::class, 'assign']
        )
            ->whereNumber('order')
            ->name('delivery-assignment.assign');

        /*
        |--------------------------------------------------------------------------
        | RIDER MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/rider-management',
            [LogisticsRiderManagementController::class, 'index']
        )->name('rider-management');

        /*
        |--------------------------------------------------------------------------
        | INCOMING PARCELS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/incoming-parcels',
            [LogisticsIncomingParcelController::class, 'index']
        )->name('incoming-parcels');

        Route::post(
            '/incoming-parcels/{order}/receive',
            [LogisticsIncomingParcelController::class, 'receive']
        )
            ->whereNumber('order')
            ->name('incoming-parcels.receive');

        /*
        |--------------------------------------------------------------------------
        | PARCEL SORTING
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/parcel-sorting',
            [LogisticsParcelSortingController::class, 'index']
        )->name('parcel-sorting');

        Route::post(
            '/parcel-sorting/{parcel}/sort',
            [LogisticsParcelSortingController::class, 'sort']
        )
            ->whereNumber('parcel')
            ->name('parcel-sorting.sort');

        /*
        |--------------------------------------------------------------------------
        | HUB TRANSFERS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/hub-transfers',
            [LogisticsHubTransferController::class, 'index']
        )->name('hub-transfers');

        Route::post(
            '/hub-transfers',
            [LogisticsHubTransferController::class, 'store']
        )->name('hub-transfers.store');

        Route::post(
            '/hub-transfers/{transfer}/dispatch',
            [LogisticsHubTransferController::class, 'dispatch']
        )
            ->whereNumber('transfer')
            ->name('hub-transfers.dispatch');

        Route::post(
            '/hub-transfers/{transfer}/receive',
            [LogisticsHubTransferController::class, 'receive']
        )
            ->whereNumber('transfer')
            ->name('hub-transfers.receive');

        /*
        |--------------------------------------------------------------------------
        | LAST-MILE ASSIGNMENT
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/last-mile-assignment',
            [LogisticsLastMileAssignmentController::class, 'index']
        )->name('last-mile-assignment');

        Route::post(
            '/last-mile-assignment/batch/assign',
            [LogisticsLastMileAssignmentController::class, 'assignBatch']
        )->name('last-mile-assignment.batch-assign');

        /*
        |--------------------------------------------------------------------------
        | DELIVERY MONITORING
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/delivery-monitoring',
            [LogisticsDeliveryMonitoringController::class, 'index']
        )->name('delivery-monitoring');

        /*
        |--------------------------------------------------------------------------
        | REPORTS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/reports',
            [LogisticsReportsController::class, 'index']
        )->name('reports');

        /*
        |--------------------------------------------------------------------------
        | ACTIVITY LOGS
        |--------------------------------------------------------------------------
        |
        | Main Logistics:
        | Provider-wide activity history.
        |
        | Hub Operator:
        | Assigned-hub activity history.
        |
        | Actual visibility restrictions are applied by the controller.
        | No edit or delete routes are registered.
        |
        */

        Route::get(
            '/activity-logs',
            [LogisticsActivityLogController::class, 'index']
        )->name('activity-logs');

        /*
        |--------------------------------------------------------------------------
        | PERSISTENT NOTIFICATIONS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/notifications',
            [LogisticsNotificationController::class, 'index']
        )->name('notifications');

        Route::get(
            '/notifications/summary',
            [LogisticsNotificationController::class, 'summary']
        )->name('notifications.summary');

        Route::post(
            '/notifications/read-all',
            [LogisticsNotificationController::class, 'readAll']
        )
            ->middleware('throttle:10,1')
            ->name('notifications.read-all');

        Route::post(
            '/notifications/{notification}/read',
            [LogisticsNotificationController::class, 'read']
        )
            ->whereNumber('notification')
            ->middleware('throttle:30,1')
            ->name('notifications.read');

        Route::post(
            '/notifications/{notification}/open',
            [LogisticsNotificationController::class, 'open']
        )
            ->whereNumber('notification')
            ->middleware('throttle:30,1')
            ->name('notifications.open');

        /*
        |--------------------------------------------------------------------------
        | MAIN LOGISTICS ONLY
        |--------------------------------------------------------------------------
        |
        | Hub Operators cannot access these routes.
        |
        */

        Route::middleware(EnsureLogisticsManager::class)
            ->group(function (): void {

                /*
                |--------------------------------------------------------------------------
                | RIDER APPLICATIONS
                |--------------------------------------------------------------------------
                */

                Route::get(
                    '/rider-applications',
                    [LogisticsRiderApplicationController::class, 'index']
                )->name('rider-applications');

                Route::post(
                    '/rider-applications/{application}/approve',
                    [LogisticsRiderApplicationController::class, 'approve']
                )
                    ->whereNumber('application')
                    ->name('rider-applications.approve');

                Route::post(
                    '/rider-applications/{application}/reject',
                    [LogisticsRiderApplicationController::class, 'reject']
                )
                    ->whereNumber('application')
                    ->name('rider-applications.reject');

                Route::get(
                    '/rider-applications/{application}/document/{document}',
                    [LogisticsRiderApplicationController::class, 'document']
                )
                    ->whereNumber('application')
                    ->whereIn('document', ['id', 'orcr'])
                    ->name('rider-applications.document');

                /*
                |--------------------------------------------------------------------------
                | SORTING FACILITIES
                |--------------------------------------------------------------------------
                */

                Route::get(
                    '/sorting-facilities',
                    [LogisticsFacilityController::class, 'index']
                )->name('facilities.index');

                Route::post(
                    '/sorting-facilities',
                    [LogisticsFacilityController::class, 'store']
                )->name('facilities.store');

                Route::get(
                    '/sorting-facilities/{facility}/edit',
                    [LogisticsFacilityController::class, 'edit']
                )
                    ->whereNumber('facility')
                    ->name('facilities.edit');

                Route::patch(
                    '/sorting-facilities/{facility}',
                    [LogisticsFacilityController::class, 'update']
                )
                    ->whereNumber('facility')
                    ->name('facilities.update');

                Route::patch(
                    '/sorting-facilities/{facility}/status',
                    [LogisticsFacilityController::class, 'toggle']
                )
                    ->whereNumber('facility')
                    ->name('facilities.toggle');

                Route::delete(
                    '/sorting-facilities/{facility}',
                    [LogisticsFacilityController::class, 'destroy']
                )
                    ->whereNumber('facility')
                    ->name('facilities.destroy');

                /*
                |--------------------------------------------------------------------------
                | DELIVERY ROUTES
                |--------------------------------------------------------------------------
                */

                Route::get(
                    '/delivery-routes/{order}',
                    [LogisticsDeliveryRouteController::class, 'edit']
                )
                    ->whereNumber('order')
                    ->name('delivery-routes.edit');

                Route::patch(
                    '/delivery-routes/{order}',
                    [LogisticsDeliveryRouteController::class, 'update']
                )
                    ->whereNumber('order')
                    ->name('delivery-routes.update');

                Route::post(
                    '/delivery-routes/{order}/stops/{stop}/reached',
                    [LogisticsDeliveryRouteController::class, 'reached']
                )
                    ->whereNumber('order')
                    ->whereNumber('stop')
                    ->name('delivery-routes.stops.reached');

                /*
                |--------------------------------------------------------------------------
                | MESSAGING
                |--------------------------------------------------------------------------
                */

                Route::get(
                    '/messages',
                    [LogisticsMessageController::class, 'index']
                )->name('messages');

                Route::post(
                    '/messages/{rider}',
                    [LogisticsMessageController::class, 'send']
                )
                    ->whereNumber('rider')
                    ->name('messages.send');

                /*
                |--------------------------------------------------------------------------
                | ACCOUNT MANAGEMENT
                |--------------------------------------------------------------------------
                */

                Route::get(
                    '/account-management',
                    [LogisticsAccountManagementController::class, 'index']
                )->name('account-management');

                Route::patch(
                    '/account-management',
                    [LogisticsAccountManagementController::class, 'update']
                )->name('account-management.update');
            });

        /*
        |--------------------------------------------------------------------------
        | SHARED LOGOUT
        |--------------------------------------------------------------------------
        |
        | Record logout before removing session identity.
        | A logging failure must not prevent the user from signing out.
        |
        */

        Route::post('/logout', function (Request $request) {

            try {
                app(LogisticsActivityLogger::class)->record(
                    $request,
                    'Authentication',
                    'logout',
                    'Signed out of Logistics'
                );
            } catch (\Throwable $exception) {
                report($exception);
            }

            $request->session()->forget([
                'is_logistics',
                'logistics_account_id',
                'logistics_email',
                'logistics_name',
                'logistics_role',

                'is_logistics_hub_operator',
                'logistics_hub_account_id',
                'logistics_facility_id',
                'logistics_hub_name',
                'logistics_hub_email',
            ]);

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return redirect()->route('login');

        })->name('logout');
    });
