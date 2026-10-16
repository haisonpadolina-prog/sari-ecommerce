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
use App\Http\Controllers\Logistics\LogisticsParcelSortingController;
use App\Http\Controllers\Logistics\LogisticsPickupRequestController;
use App\Http\Controllers\Logistics\LogisticsReportsController;
use App\Http\Controllers\Logistics\LogisticsRiderApplicationController;
use App\Http\Controllers\Logistics\LogisticsRiderManagementController;
use App\Http\Middleware\Logistics\EnsureLogisticsAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| PUBLIC RIDER → LOGISTICS APPLICATION FLOW
|--------------------------------------------------------------------------
*/

Route::get(
    '/register/rider',
    [
        RiderRegistrationController::class,
        'index',
    ]
)->name('rider.logistics.index');


Route::get(
    '/register/rider/{logistics}',
    [
        RiderRegistrationController::class,
        'create',
    ]
)
    ->whereNumber('logistics')
    ->name('rider.logistics.apply');


Route::post(
    '/register/rider/{logistics}/email/send',
    [
        RiderRegistrationController::class,
        'sendEmailCode',
    ]
)
    ->whereNumber('logistics')
    ->middleware('throttle:8,1')
    ->name('rider.logistics.email.send');


Route::post(
    '/register/rider/{logistics}/email/verify',
    [
        RiderRegistrationController::class,
        'verifyEmailCode',
    ]
)
    ->whereNumber('logistics')
    ->middleware('throttle:12,1')
    ->name('rider.logistics.email.verify');


Route::post(
    '/register/rider/{logistics}',
    [
        RiderRegistrationController::class,
        'store',
    ]
)
    ->whereNumber('logistics')
    ->name('rider.logistics.submit');


/*
|--------------------------------------------------------------------------
| LOGISTICS PANEL
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

        Route::get(
            '/',
            [
                LogisticsDashboardController::class,
                'index',
            ]
        )->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Rider Applications
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/rider-applications',
            [
                LogisticsRiderApplicationController::class,
                'index',
            ]
        )->name('rider-applications');


        Route::post(
            '/rider-applications/{application}/approve',
            [
                LogisticsRiderApplicationController::class,
                'approve',
            ]
        )
            ->whereNumber('application')
            ->name('rider-applications.approve');


        Route::post(
            '/rider-applications/{application}/reject',
            [
                LogisticsRiderApplicationController::class,
                'reject',
            ]
        )
            ->whereNumber('application')
            ->name('rider-applications.reject');


        Route::get(
            '/rider-applications/{application}/document/{document}',
            [
                LogisticsRiderApplicationController::class,
                'document',
            ]
        )
            ->whereNumber('application')
            ->whereIn(
                'document',
                [
                    'id',
                    'orcr',
                ]
            )
            ->name('rider-applications.document');


        /*
        |--------------------------------------------------------------------------
        | Rider Management
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/rider-management',
            [
                LogisticsRiderManagementController::class,
                'index',
            ]
        )->name('rider-management');


        /*
        |--------------------------------------------------------------------------
        | Sorting Facilities
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/sorting-facilities',
            [
                LogisticsFacilityController::class,
                'index',
            ]
        )->name('facilities.index');


        Route::post(
            '/sorting-facilities',
            [
                LogisticsFacilityController::class,
                'store',
            ]
        )->name('facilities.store');


        Route::get(
            '/sorting-facilities/{facility}/edit',
            [
                LogisticsFacilityController::class,
                'edit',
            ]
        )
            ->whereNumber('facility')
            ->name('facilities.edit');


        Route::patch(
            '/sorting-facilities/{facility}',
            [
                LogisticsFacilityController::class,
                'update',
            ]
        )
            ->whereNumber('facility')
            ->name('facilities.update');


        Route::patch(
            '/sorting-facilities/{facility}/status',
            [
                LogisticsFacilityController::class,
                'toggle',
            ]
        )
            ->whereNumber('facility')
            ->name('facilities.toggle');


        Route::delete(
            '/sorting-facilities/{facility}',
            [
                LogisticsFacilityController::class,
                'destroy',
            ]
        )
            ->whereNumber('facility')
            ->name('facilities.destroy');


        /*
        |--------------------------------------------------------------------------
        | Pickup Requests
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Keep the static /batch/verify route ABOVE /{order}/verify.
        | Otherwise Laravel may treat "batch" as the order parameter.
        |
        */

        Route::get(
            '/pickup-requests',
            [
                LogisticsPickupRequestController::class,
                'index',
            ]
        )->name('pickup-requests');


        Route::post(
            '/pickup-requests/batch/verify',
            [
                LogisticsPickupRequestController::class,
                'verifyBatch',
            ]
        )->name('pickup-requests.batch-verify');


        Route::post(
            '/pickup-requests/{order}/verify',
            [
                LogisticsPickupRequestController::class,
                'verify',
            ]
        )
            ->whereNumber('order')
            ->name('pickup-requests.verify');


        /*
        |--------------------------------------------------------------------------
        | Pickup Assignment
        |--------------------------------------------------------------------------
        |
        | One Seller + same pickup location + same origin hub can contain
        | multiple Marketplace Orders and be assigned to one pickup Rider.
        |
        | Keep /batch/assign ABOVE the parameterized /{order}/assign route.
        |
        */

        Route::get(
            '/delivery-assignment',
            [
                LogisticsDeliveryAssignmentController::class,
                'index',
            ]
        )->name('delivery-assignment');


        Route::post(
            '/delivery-assignment/batch/assign',
            [
                LogisticsDeliveryAssignmentController::class,
                'assignBatch',
            ]
        )->name('delivery-assignment.batch-assign');


        Route::post(
            '/delivery-assignment/{order}/assign',
            [
                LogisticsDeliveryAssignmentController::class,
                'assign',
            ]
        )
            ->whereNumber('order')
            ->name('delivery-assignment.assign');


        /*
        |--------------------------------------------------------------------------
        | Incoming Parcels / Origin Hub Intake
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/incoming-parcels',
            [
                LogisticsIncomingParcelController::class,
                'index',
            ]
        )->name('incoming-parcels');


        Route::post(
            '/incoming-parcels/{order}/receive',
            [
                LogisticsIncomingParcelController::class,
                'receive',
            ]
        )
            ->whereNumber('order')
            ->name('incoming-parcels.receive');


        /*
        |--------------------------------------------------------------------------
        | Parcel Sorting
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/parcel-sorting',
            [
                LogisticsParcelSortingController::class,
                'index',
            ]
        )->name('parcel-sorting');


        Route::post(
            '/parcel-sorting/{parcel}/sort',
            [
                LogisticsParcelSortingController::class,
                'sort',
            ]
        )
            ->whereNumber('parcel')
            ->name('parcel-sorting.sort');


        /*
        |--------------------------------------------------------------------------
        | Hub-to-Hub Transfers
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/hub-transfers',
            [
                LogisticsHubTransferController::class,
                'index',
            ]
        )->name('hub-transfers');


        Route::post(
            '/hub-transfers',
            [
                LogisticsHubTransferController::class,
                'store',
            ]
        )->name('hub-transfers.store');


        Route::post(
            '/hub-transfers/{transfer}/dispatch',
            [
                LogisticsHubTransferController::class,
                'dispatch',
            ]
        )
            ->whereNumber('transfer')
            ->name('hub-transfers.dispatch');


        Route::post(
            '/hub-transfers/{transfer}/receive',
            [
                LogisticsHubTransferController::class,
                'receive',
            ]
        )
            ->whereNumber('transfer')
            ->name('hub-transfers.receive');


        /*
        |--------------------------------------------------------------------------
        | Last-Mile Assignment
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/last-mile-assignment',
            [
                LogisticsLastMileAssignmentController::class,
                'index',
            ]
        )->name('last-mile-assignment');


        Route::post(
            '/last-mile-assignment/batch/assign',
            [
                LogisticsLastMileAssignmentController::class,
                'assignBatch',
            ]
        )->name('last-mile-assignment.batch-assign');


        /*
        |--------------------------------------------------------------------------
        | Delivery Monitoring
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/delivery-monitoring',
            [
                LogisticsDeliveryMonitoringController::class,
                'index',
            ]
        )->name('delivery-monitoring');


        /*
        |--------------------------------------------------------------------------
        | Delivery Routes
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/delivery-routes/{order}',
            [
                LogisticsDeliveryRouteController::class,
                'edit',
            ]
        )
            ->whereNumber('order')
            ->name('delivery-routes.edit');


        Route::patch(
            '/delivery-routes/{order}',
            [
                LogisticsDeliveryRouteController::class,
                'update',
            ]
        )
            ->whereNumber('order')
            ->name('delivery-routes.update');


        Route::post(
            '/delivery-routes/{order}/stops/{stop}/reached',
            [
                LogisticsDeliveryRouteController::class,
                'reached',
            ]
        )
            ->whereNumber('order')
            ->whereNumber('stop')
            ->name('delivery-routes.stops.reached');


        /*
        |--------------------------------------------------------------------------
        | Reports
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/reports',
            [
                LogisticsReportsController::class,
                'index',
            ]
        )->name('reports');


        /*
        |--------------------------------------------------------------------------
        | Messages
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/messages',
            [
                LogisticsMessageController::class,
                'index',
            ]
        )->name('messages');


        Route::post(
            '/messages/{rider}',
            [
                LogisticsMessageController::class,
                'send',
            ]
        )
            ->whereNumber('rider')
            ->name('messages.send');


        /*
        |--------------------------------------------------------------------------
        | Account Management
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/account-management',
            [
                LogisticsAccountManagementController::class,
                'index',
            ]
        )->name('account-management');


        Route::patch(
            '/account-management',
            [
                LogisticsAccountManagementController::class,
                'update',
            ]
        )->name('account-management.update');


        /*
        |--------------------------------------------------------------------------
        | Logout
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/logout',
            function (
                Request $request
            ) {
                $request
                    ->session()
                    ->forget([
                        'is_logistics',
                        'logistics_account_id',
                        'logistics_email',
                        'logistics_name',
                    ]);

                $request
                    ->session()
                    ->invalidate();

                $request
                    ->session()
                    ->regenerateToken();

                return redirect()
                    ->route('login');
            }
        )->name('logout');
    });