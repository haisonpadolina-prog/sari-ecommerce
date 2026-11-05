<?php

use App\Http\Controllers\Api\V1\MobileAuthController;
use App\Http\Controllers\Api\V1\MobileBuyerAccountController;
use App\Http\Controllers\Api\V1\MobileBuyerCartController;
use App\Http\Controllers\Api\V1\MobileBuyerCheckoutController;
use App\Http\Controllers\Api\V1\MobileBuyerMessageController;
use App\Http\Controllers\Api\V1\MobileBuyerNotificationController;
use App\Http\Controllers\Api\V1\MobileBuyerOrderActionController;
use App\Http\Controllers\Api\V1\MobileBuyerOrderController;
use App\Http\Controllers\Api\V1\MobileBuyerWalletController;
use App\Http\Controllers\Api\V1\MobileBuyerRegistrationController;
use App\Http\Controllers\Api\V1\MobileBuyerShippingController;
use App\Http\Controllers\Api\V1\MobileCatalogController;
use App\Http\Controllers\Api\V1\MobileRiderAssignmentController;
use App\Http\Controllers\Api\V1\MobileRiderDashboardController;
use App\Http\Controllers\Api\V1\MobileRiderFinanceController;
use App\Http\Controllers\Api\V1\MobileRiderLastMileController;
use App\Http\Controllers\Api\V1\MobileRiderMessageController;
use App\Http\Controllers\Api\V1\MobileRiderNotificationController;
use App\Http\Controllers\Api\V1\MobileRiderProfileController;
use App\Http\Controllers\Api\V1\MobileRiderProofController;
use App\Http\Controllers\Api\V1\MobileRiderRegistrationController;
use App\Http\Controllers\Api\V1\MobileRiderScannerController;
use App\Http\Controllers\Shared\AddressLookupController;
use App\Http\Controllers\Api\V1\MobilePasswordResetController;
use App\Http\Controllers\Api\V1\MobileRiderDeliveryExceptionController;
use App\Http\Controllers\Api\V1\MobileLogisticsController;
use App\Http\Controllers\Api\V1\MobileLogisticsRegistrationController;
use App\Http\Controllers\Api\V1\MobileLogisticsPickupController;
use App\Http\Controllers\Api\V1\MobileLogisticsLastMileAssignmentController;
use App\Http\Controllers\Api\V1\MobileLogisticsRemittanceController;
use App\Http\Controllers\Api\V1\MobileBuyerRewardController;
use App\Http\Middleware\Api\EnsureMobileRole;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('health', fn () => response()->json([
        'success' => true,
        'data' => [
            'service' => 'sari-api',
            'status' => 'ok',
            'version' => 'v1',
        ],
    ]))->name('api.v1.health');

    Route::get('products', [MobileCatalogController::class, 'index']);
    Route::get('products/{product}', [MobileCatalogController::class, 'show'])
        ->whereNumber('product');

    // Mobile-safe public address lookups. These reuse the same Laravel source
    // as the website but live under /api/v1, so registration/profile forms do
    // not depend on web routes, cookies, or CSRF state.
    Route::get('address/regions', [AddressLookupController::class, 'regions']);
    Route::get('address/regions/{regionCode}/provinces', [AddressLookupController::class, 'regionProvinces']);
    Route::get('address/provinces', [AddressLookupController::class, 'provinces']);
    Route::get('address/provinces/{provinceCode}/municipalities', [AddressLookupController::class, 'municipalities']);
    Route::get('address/municipalities/{municipalityCode}/barangays', [AddressLookupController::class, 'barangays']);

    /*
    |--------------------------------------------------------------------------
    | Mobile Buyer Registration
    |--------------------------------------------------------------------------
    | Stateless API registration. No browser CSRF/session cookies.
    */
    Route::post('registration/buyer', [MobileBuyerRegistrationController::class, 'apply'])
        ->middleware('throttle:5,1');
    Route::post('registration/buyer/otp/resend', [MobileBuyerRegistrationController::class, 'resend'])
        ->middleware('throttle:5,1');
    Route::post('registration/buyer/otp/verify', [MobileBuyerRegistrationController::class, 'verify'])
        ->middleware('throttle:10,1');


    /* Mobile Rider Registration - stateless, no browser CSRF/session dependency. */
    Route::get('registration/rider/providers', [MobileRiderRegistrationController::class, 'providers']);
    Route::post('registration/rider/{logistics}/otp/send', [MobileRiderRegistrationController::class, 'sendOtp'])
        ->whereNumber('logistics')->middleware('throttle:8,1');
    Route::post('registration/rider/{logistics}/otp/verify', [MobileRiderRegistrationController::class, 'verifyOtp'])
        ->whereNumber('logistics')->middleware('throttle:12,1');
    Route::post('registration/rider/{logistics}', [MobileRiderRegistrationController::class, 'apply'])
        ->whereNumber('logistics')->middleware('throttle:10,1');

    /* Stateless Logistics registration + OTP; Admin approval follows web workflow */
    Route::post('registration/logistics', [MobileLogisticsRegistrationController::class, 'apply'])->middleware('throttle:3,1');
    Route::post('registration/logistics/otp/resend', [MobileLogisticsRegistrationController::class, 'resend'])->middleware('throttle:5,1');
    Route::post('registration/logistics/otp/verify', [MobileLogisticsRegistrationController::class, 'verify'])->middleware('throttle:10,1');

    /* Stateless password reset for active mobile users */
    Route::post('auth/forgot-password/send', [MobilePasswordResetController::class, 'sendCode'])->middleware('throttle:3,1');
    Route::post('auth/forgot-password/verify', [MobilePasswordResetController::class, 'verifyCode'])->middleware('throttle:10,1');
    Route::post('auth/forgot-password/reset', [MobilePasswordResetController::class, 'reset'])->middleware('throttle:5,1');

    /* Authentication */
    Route::post('auth/login', [MobileAuthController::class, 'login'])
        ->middleware('throttle:5,1');

    Route::middleware(['auth:sanctum', EnsureMobileRole::class])->group(function (): void {
        Route::get('auth/me', [MobileAuthController::class, 'me']);
        Route::post('auth/logout', [MobileAuthController::class, 'logout']);
        Route::post('auth/logout-all', [MobileAuthController::class, 'logoutAll'])
            ->middleware('throttle:5,1');
    });

    /* Buyer */
    Route::middleware(['auth:sanctum', EnsureMobileRole::class.':buyer'])
        ->prefix('buyer')
        ->group(function (): void {
            Route::get('wallet', [MobileBuyerWalletController::class, 'index']);
            Route::get('orders', [MobileBuyerOrderController::class, 'index']);
            Route::get('orders/{order}', [MobileBuyerOrderController::class, 'show'])
                ->whereNumber('order');
            Route::post('orders/{order}/cancel', [MobileBuyerOrderActionController::class, 'cancel'])
                ->whereNumber('order');
            Route::post('orders/{order}/reviews', [MobileBuyerOrderActionController::class, 'review'])
                ->whereNumber('order');
            Route::get('orders/{order}/tracking', [MobileBuyerShippingController::class, 'tracking'])
                ->whereNumber('order');

            Route::get('cart', [MobileBuyerCartController::class, 'index']);
            Route::get('cart/summary', [MobileBuyerCartController::class, 'summary']);
            Route::post('cart/items', [MobileBuyerCartController::class, 'store']);
            Route::patch('cart/items/{item}', [MobileBuyerCartController::class, 'update'])
                ->whereNumber('item');
            Route::delete('cart/items/{item}', [MobileBuyerCartController::class, 'destroy'])
                ->whereNumber('item');

            Route::post('checkout/preview', [MobileBuyerCheckoutController::class, 'preview']);
            Route::post('checkout', [MobileBuyerCheckoutController::class, 'store'])
                ->middleware('throttle:10,1');

            Route::get('profile', [MobileBuyerAccountController::class, 'show']);
            Route::match(['patch', 'post'], 'profile', [MobileBuyerAccountController::class, 'update']);
            Route::patch('profile/password', [MobileBuyerAccountController::class, 'password']);

            Route::get('addresses', [MobileBuyerAccountController::class, 'addresses']);
            Route::post('addresses', [MobileBuyerAccountController::class, 'storeAddress']);
            Route::patch('addresses/{address}', [MobileBuyerAccountController::class, 'updateAddress'])
                ->whereNumber('address');
            Route::post('addresses/{address}/default', [MobileBuyerAccountController::class, 'defaultAddress'])
                ->whereNumber('address');
            Route::delete('addresses/{address}', [MobileBuyerAccountController::class, 'destroyAddress'])
                ->whereNumber('address');

            Route::get('notifications', [MobileBuyerNotificationController::class, 'index']);
            Route::get('notifications/summary', [MobileBuyerNotificationController::class, 'summary']);
            Route::post('notifications/read-all', [MobileBuyerNotificationController::class, 'readAll']);
            Route::post('notifications/{event}/read', [MobileBuyerNotificationController::class, 'read'])
                ->whereNumber('event');

            Route::get('shipping', [MobileBuyerShippingController::class, 'index']);
            Route::get('rewards', [MobileBuyerRewardController::class, 'index']);

            Route::get('messages', [MobileBuyerMessageController::class, 'index']);
            Route::get('messages/{seller}', [MobileBuyerMessageController::class, 'thread'])
                ->whereNumber('seller');
            Route::post('messages/{seller}', [MobileBuyerMessageController::class, 'send'])
                ->whereNumber('seller');
        });

    /* Rider */
    Route::middleware(['auth:sanctum', EnsureMobileRole::class.':rider'])
        ->prefix('rider')
        ->group(function (): void {
            Route::get('dashboard', [MobileRiderDashboardController::class, 'index']);

            Route::get('assignments', [MobileRiderAssignmentController::class, 'index']);
            Route::get('pickups', [MobileRiderAssignmentController::class, 'pickups']);
            Route::get('deliveries', [MobileRiderAssignmentController::class, 'deliveries']);
            Route::get('assignments/{order}', [MobileRiderAssignmentController::class, 'show'])
                ->whereNumber('order');

            Route::post('orders/{order}/proceed-pickup', [MobileRiderAssignmentController::class, 'proceedPickup'])
                ->whereNumber('order');
            Route::post('orders/{order}/arrived-pickup', [MobileRiderAssignmentController::class, 'arrivedPickup'])
                ->whereNumber('order');
            Route::post('orders/{order}/confirm-pickup', [MobileRiderAssignmentController::class, 'confirmPickup'])
                ->whereNumber('order');
            Route::post('orders/{order}/arrived-buyer', [MobileRiderAssignmentController::class, 'arrivedBuyer'])
                ->whereNumber('order');
            Route::post('orders/{order}/complete', [MobileRiderAssignmentController::class, 'complete'])
                ->whereNumber('order');

            Route::get('last-mile', [MobileRiderLastMileController::class, 'index']);
            Route::get('last-mile/{assignment}', [MobileRiderLastMileController::class, 'show'])
                ->whereNumber('assignment');
            Route::post('last-mile/{assignment}/start', [MobileRiderLastMileController::class, 'start'])
                ->whereNumber('assignment');
            Route::post('last-mile/{assignment}/orders/{order}/arrived', [MobileRiderLastMileController::class, 'arrived'])
                ->whereNumber('assignment')
                ->whereNumber('order');
            Route::post('last-mile/{assignment}/orders/{order}/delivered', [MobileRiderLastMileController::class, 'delivered'])
                ->whereNumber('assignment')
                ->whereNumber('order');

            Route::post('scanner/verify', [MobileRiderScannerController::class, 'verify'])
                ->middleware('throttle:60,1');
            Route::post('scanner/confirm-cod', [MobileRiderScannerController::class, 'confirmCod'])
                ->middleware('throttle:20,1');
            Route::post('orders/{order}/proof', [MobileRiderProofController::class, 'store'])
                ->whereNumber('order');

            Route::get('earnings', [MobileRiderFinanceController::class, 'earnings']);
            Route::get('earnings/transactions', [MobileRiderFinanceController::class, 'transactions']);
            Route::get('payouts', [MobileRiderFinanceController::class, 'payoutIndex']);
            Route::post('payouts', [MobileRiderFinanceController::class, 'payoutStore'])
                ->middleware('throttle:5,1');

            Route::get('remittances/hubs', [MobileRiderFinanceController::class, 'remittanceHubs']);
            Route::get('remittances/eligible', [MobileRiderFinanceController::class, 'remittanceEligible']);
            Route::get('remittances', [MobileRiderFinanceController::class, 'remittanceIndex']);
            Route::get('remittances/{batch}', [MobileRiderFinanceController::class, 'remittanceShow'])
                ->whereNumber('batch');
            Route::post('remittances', [MobileRiderFinanceController::class, 'remittanceStore'])
                ->middleware('throttle:10,1');

            Route::get('history', [MobileRiderFinanceController::class, 'history']);

            Route::post('orders/{order}/failed', [MobileRiderDeliveryExceptionController::class, 'failed'])->whereNumber('order');
            Route::post('orders/{order}/reschedule-request', [MobileRiderDeliveryExceptionController::class, 'reschedule'])->whereNumber('order');
            Route::post('orders/{order}/return-request', [MobileRiderDeliveryExceptionController::class, 'returnToSorting'])->whereNumber('order');


            Route::get('notifications', [MobileRiderNotificationController::class, 'index']);
            Route::get('notifications/summary', [MobileRiderNotificationController::class, 'summary']);
            Route::post('notifications/read-all', [MobileRiderNotificationController::class, 'readAll']);
            Route::post('notifications/{notification}/read', [MobileRiderNotificationController::class, 'read'])
                ->whereNumber('notification');

            Route::get('messages', [MobileRiderMessageController::class, 'index']);
            Route::post('messages', [MobileRiderMessageController::class, 'send']);

            Route::get('profile', [MobileRiderProfileController::class, 'show']);
            Route::match(['patch', 'post'], 'profile', [MobileRiderProfileController::class, 'update']);
            Route::patch('profile/availability', [MobileRiderProfileController::class, 'availability']);
            Route::patch('profile/password', [MobileRiderProfileController::class, 'password']);
        });

    /* Manager-only Logistics Mobile API. Every action verifies provider + hub ownership. */
    Route::middleware(['auth:sanctum', EnsureMobileRole::class.':logistics'])
        ->prefix('logistics')->group(function (): void {
            Route::get('dashboard', [MobileLogisticsController::class,'dashboard']);
            Route::get('profile', [MobileLogisticsController::class,'profile']);
            Route::get('branches', [MobileLogisticsController::class,'branches']);
            Route::get('incoming', [MobileLogisticsController::class,'incoming']);
            Route::get('sorting', [MobileLogisticsController::class,'sorting']);
            Route::get('pickups', [MobileLogisticsPickupController::class,'index']);
            Route::post('pickups/verify', [MobileLogisticsPickupController::class,'verify']);
            Route::post('pickups/assign', [MobileLogisticsPickupController::class,'assign']);
            Route::get('last-mile', [MobileLogisticsLastMileAssignmentController::class,'index']);
            Route::post('last-mile/assign', [MobileLogisticsLastMileAssignmentController::class,'assign']);
            Route::get('monitoring', [MobileLogisticsController::class,'monitoring']);
            Route::post('scanner/verify', [MobileLogisticsController::class,'verify'])->middleware('throttle:60,1');
            Route::post('incoming/{parcel}/receive', [MobileLogisticsController::class,'receive'])->whereNumber('parcel');
            Route::post('sorting/{parcel}/confirm', [MobileLogisticsController::class,'sort'])->whereNumber('parcel');
            Route::get('transfers', [MobileLogisticsController::class,'transfers']);
            Route::get('transfers/{transfer}', [MobileLogisticsController::class,'transfer'])->whereNumber('transfer');
            Route::post('transfers/{transfer}/{action}', [MobileLogisticsController::class,'transferAction'])
                ->whereNumber('transfer')->whereIn('action',['dispatch','receive']);
            Route::get('notifications', [MobileLogisticsController::class,'notifications']);
            Route::post('notifications/{notification}/read', [MobileLogisticsController::class,'readNotification'])->whereNumber('notification');
            Route::get('remittances', [MobileLogisticsRemittanceController::class,'index']);
            Route::get('remittances/{batch}', [MobileLogisticsRemittanceController::class,'show'])->whereNumber('batch');
            Route::post('remittances/{batch}/verify', [MobileLogisticsRemittanceController::class,'verify'])->whereNumber('batch');
            Route::get('riders', [MobileLogisticsController::class,'riders']);
            Route::get('messages', [MobileLogisticsController::class,'messages']);
            Route::post('messages', [MobileLogisticsController::class,'sendMessage']);
        });

});
