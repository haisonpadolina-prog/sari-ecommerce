<?php

use App\Http\Controllers\Courier\RiderRegistrationController;
use App\Http\Controllers\Logistics\LogisticsAccountManagementController;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Rider Registration (unchanged)
|--------------------------------------------------------------------------
*/

Route::get('/register/rider', [RiderRegistrationController::class, 'index'])
    ->name('rider.logistics.index');

Route::get('/register/rider/{logistics}', [RiderRegistrationController::class, 'create'])
    ->whereNumber('logistics')
    ->name('rider.logistics.apply');

Route::post('/register/rider/{logistics}/email/send', [RiderRegistrationController::class, 'sendEmailCode'])
    ->whereNumber('logistics')
    ->middleware('throttle:8,1')
    ->name('rider.logistics.email.send');

Route::post('/register/rider/{logistics}/email/verify', [RiderRegistrationController::class, 'verifyEmailCode'])
    ->whereNumber('logistics')
    ->middleware('throttle:12,1')
    ->name('rider.logistics.email.verify');

Route::post('/register/rider/{logistics}', [RiderRegistrationController::class, 'store'])
    ->whereNumber('logistics')
    ->name('rider.logistics.submit');

/*
|--------------------------------------------------------------------------
| Authenticated Logistics — Manager and assigned Hub Operator
|--------------------------------------------------------------------------
*/

Route::prefix('logistics')
    ->name('logistics.')
    ->middleware(EnsureLogisticsAuthenticated::class)
    ->group(function (): void {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/', [LogisticsDashboardController::class, 'index'])
            ->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Shared Operational Routes
        |--------------------------------------------------------------------------
        |
        | Main Logistics has provider-wide access.
        | Hub Operators have assigned-hub access.
        |
        | The respective controllers enforce actual data and action scoping.
        |
        */

        /*
        |--------------------------------------------------------------------------
        | Pickup Requests
        |--------------------------------------------------------------------------
        */

        Route::get('/pickup-requests', [LogisticsPickupRequestController::class, 'index'])
            ->name('pickup-requests');

        Route::post('/pickup-requests/batch/verify', [LogisticsPickupRequestController::class, 'verifyBatch'])
            ->name('pickup-requests.batch-verify');

        Route::post('/pickup-requests/{order}/verify', [LogisticsPickupRequestController::class, 'verify'])
            ->whereNumber('order')
            ->name('pickup-requests.verify');

        /*
        |--------------------------------------------------------------------------
        | Pickup Assignment
        |--------------------------------------------------------------------------
        */

        Route::get('/delivery-assignment', [LogisticsDeliveryAssignmentController::class, 'index'])
            ->name('delivery-assignment');

        Route::post('/delivery-assignment/batch/assign', [LogisticsDeliveryAssignmentController::class, 'assignBatch'])
            ->name('delivery-assignment.batch-assign');

        Route::post('/delivery-assignment/{order}/assign', [LogisticsDeliveryAssignmentController::class, 'assign'])
            ->whereNumber('order')
            ->name('delivery-assignment.assign');

        /*
        |--------------------------------------------------------------------------
        | Rider Management — Hub view is local and read-only
        |--------------------------------------------------------------------------
        */

        Route::get('/rider-management', [LogisticsRiderManagementController::class, 'index'])
            ->name('rider-management');

        /*
        |--------------------------------------------------------------------------
        | Incoming Parcels
        |--------------------------------------------------------------------------
        */

        Route::get('/incoming-parcels', [LogisticsIncomingParcelController::class, 'index'])
            ->name('incoming-parcels');

        Route::post('/incoming-parcels/{order}/receive', [LogisticsIncomingParcelController::class, 'receive'])
            ->whereNumber('order')
            ->name('incoming-parcels.receive');

        /*
        |--------------------------------------------------------------------------
        | Parcel Sorting
        |--------------------------------------------------------------------------
        */

        Route::get('/parcel-sorting', [LogisticsParcelSortingController::class, 'index'])
            ->name('parcel-sorting');

        Route::post('/parcel-sorting/{parcel}/sort', [LogisticsParcelSortingController::class, 'sort'])
            ->whereNumber('parcel')
            ->name('parcel-sorting.sort');

        /*
        |--------------------------------------------------------------------------
        | Hub Transfers
        |--------------------------------------------------------------------------
        */

        Route::get('/hub-transfers', [LogisticsHubTransferController::class, 'index'])
            ->name('hub-transfers');

        Route::post('/hub-transfers', [LogisticsHubTransferController::class, 'store'])
            ->name('hub-transfers.store');

        Route::post('/hub-transfers/{transfer}/dispatch', [LogisticsHubTransferController::class, 'dispatch'])
            ->whereNumber('transfer')
            ->name('hub-transfers.dispatch');

        Route::post('/hub-transfers/{transfer}/receive', [LogisticsHubTransferController::class, 'receive'])
            ->whereNumber('transfer')
            ->name('hub-transfers.receive');

        /*
        |--------------------------------------------------------------------------
        | Last-Mile Assignment
        |--------------------------------------------------------------------------
        */

        Route::get('/last-mile-assignment', [LogisticsLastMileAssignmentController::class, 'index'])
            ->name('last-mile-assignment');

        Route::post('/last-mile-assignment/batch/assign', [LogisticsLastMileAssignmentController::class, 'assignBatch'])
            ->name('last-mile-assignment.batch-assign');

        /*
        |--------------------------------------------------------------------------
        | Delivery Monitoring and Reports
        |--------------------------------------------------------------------------
        */

        Route::get('/delivery-monitoring', [LogisticsDeliveryMonitoringController::class, 'index'])
            ->name('delivery-monitoring');

        Route::get('/reports', [LogisticsReportsController::class, 'index'])
            ->name('reports');

        /*
        |--------------------------------------------------------------------------
        | Persistent Notifications
        |--------------------------------------------------------------------------
        |
        | Both Manager and Hub Operator can use these routes.
        |
        | The controller resolves the recipient from the authenticated
        | session. It never accepts a recipient ID from the browser.
        |
        | Reading notification history is GET.
        | Updating notification read state is POST + CSRF.
        |
        */

        Route::get('/notifications', [LogisticsNotificationController::class, 'index'])
            ->name('notifications');

        Route::get('/notifications/summary', [LogisticsNotificationController::class, 'summary'])
            ->name('notifications.summary');

        Route::post('/notifications/read-all', [LogisticsNotificationController::class, 'readAll'])
            ->middleware('throttle:10,1')
            ->name('notifications.read-all');

        Route::post('/notifications/{notification}/read', [LogisticsNotificationController::class, 'read'])
            ->whereNumber('notification')
            ->middleware('throttle:30,1')
            ->name('notifications.read');

        Route::post('/notifications/{notification}/open', [LogisticsNotificationController::class, 'open'])
            ->whereNumber('notification')
            ->middleware('throttle:30,1')
            ->name('notifications.open');

        /*
        |--------------------------------------------------------------------------
        | Manager-Only Routes
        |--------------------------------------------------------------------------
        |
        | Hub Operators must receive 403 Forbidden even if they manually
        | visit these URLs or submit a request.
        |
        */

        Route::middleware(EnsureLogisticsManager::class)
            ->group(function (): void {

                /*
                |--------------------------------------------------------------------------
                | Rider Applications
                |--------------------------------------------------------------------------
                */

                Route::get('/rider-applications', [LogisticsRiderApplicationController::class, 'index'])
                    ->name('rider-applications');

                Route::post('/rider-applications/{application}/approve', [LogisticsRiderApplicationController::class, 'approve'])
                    ->whereNumber('application')
                    ->name('rider-applications.approve');

                Route::post('/rider-applications/{application}/reject', [LogisticsRiderApplicationController::class, 'reject'])
                    ->whereNumber('application')
                    ->name('rider-applications.reject');

                Route::get('/rider-applications/{application}/document/{document}', [LogisticsRiderApplicationController::class, 'document'])
                    ->whereNumber('application')
                    ->whereIn('document', ['id', 'orcr'])
                    ->name('rider-applications.document');

                /*
                |--------------------------------------------------------------------------
                | Sorting Facilities / Municipal Hub Administration
                |--------------------------------------------------------------------------
                */

                Route::get('/sorting-facilities', [LogisticsFacilityController::class, 'index'])
                    ->name('facilities.index');

                Route::post('/sorting-facilities', [LogisticsFacilityController::class, 'store'])
                    ->name('facilities.store');

                Route::get('/sorting-facilities/{facility}/edit', [LogisticsFacilityController::class, 'edit'])
                    ->whereNumber('facility')
                    ->name('facilities.edit');

                Route::patch('/sorting-facilities/{facility}', [LogisticsFacilityController::class, 'update'])
                    ->whereNumber('facility')
                    ->name('facilities.update');

                Route::patch('/sorting-facilities/{facility}/status', [LogisticsFacilityController::class, 'toggle'])
                    ->whereNumber('facility')
                    ->name('facilities.toggle');

                Route::delete('/sorting-facilities/{facility}', [LogisticsFacilityController::class, 'destroy'])
                    ->whereNumber('facility')
                    ->name('facilities.destroy');

                /*
                |--------------------------------------------------------------------------
                | Delivery Route Editing
                |--------------------------------------------------------------------------
                */

                Route::get('/delivery-routes/{order}', [LogisticsDeliveryRouteController::class, 'edit'])
                    ->whereNumber('order')
                    ->name('delivery-routes.edit');

                Route::patch('/delivery-routes/{order}', [LogisticsDeliveryRouteController::class, 'update'])
                    ->whereNumber('order')
                    ->name('delivery-routes.update');

                Route::post('/delivery-routes/{order}/stops/{stop}/reached', [LogisticsDeliveryRouteController::class, 'reached'])
                    ->whereNumber('order')
                    ->whereNumber('stop')
                    ->name('delivery-routes.stops.reached');

                /*
                |--------------------------------------------------------------------------
                | Provider Messaging
                |--------------------------------------------------------------------------
                */

                Route::get('/messages', [LogisticsMessageController::class, 'index'])
                    ->name('messages');

                Route::post('/messages/{rider}', [LogisticsMessageController::class, 'send'])
                    ->whereNumber('rider')
                    ->name('messages.send');

                /*
                |--------------------------------------------------------------------------
                | Provider Account Management
                |--------------------------------------------------------------------------
                */

                Route::get('/account-management', [LogisticsAccountManagementController::class, 'index'])
                    ->name('account-management');

                Route::patch('/account-management', [LogisticsAccountManagementController::class, 'update'])
                    ->name('account-management.update');
            });

        /*
        |--------------------------------------------------------------------------
        | Shared Logout
        |--------------------------------------------------------------------------
        */

        Route::post('/logout', function (Request $request) {
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
