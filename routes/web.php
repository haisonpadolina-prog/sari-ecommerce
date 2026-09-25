<?php
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\BuyerPageController;
use App\Http\Controllers\BuyerProductController;
use App\Http\Controllers\BuyerCartController;
use App\Http\Controllers\BuyerCheckoutController;
use App\Http\Controllers\BuyerOrderController;
use App\Http\Controllers\BuyerSellerMessageController;
use App\Http\Controllers\SellerBuyerMessageController;
use App\Http\Controllers\BuyerReviewController;
use App\Http\Controllers\SellerReviewController;
use App\Http\Controllers\BuyerAccountController;
use App\Http\Controllers\CourierPageController;
use App\Http\Controllers\AdminSellerComplianceController;
use App\Http\Controllers\SellerComplianceMessageController;
use App\Http\Controllers\SellerDashboardController;
use App\Http\Controllers\SellerLayoutStateController;
use App\Http\Controllers\SellerAdminChatController;
use App\Http\Controllers\SellerProductController;
use App\Http\Controllers\SellerProductDraftController;
use App\Http\Controllers\SellerOrderController;
use App\Http\Controllers\CourierDeliveryController;
use App\Http\Middleware\EnsureSellerNotRestricted;
use App\Http\Middleware\EnsureSellerAccountAccessible;
use App\Http\Controllers\AdminSellerAccountStatusController;
use App\Services\SellerAccountStatusService;
use App\Http\Controllers\ChatMessageReactionController;
use App\Http\Controllers\AdminSellerChatActionController;
use App\Http\Middleware\HandleSellerSupportChat;
use App\Http\Controllers\PasswordResetOtpController;

/*
|--------------------------------------------------------------------------
| NEW — COURIER CONTROLLER
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\CourierDashboardController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AdminRegistrationController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\PlatformComplaintController;
use App\Http\Controllers\AdminComplaintsController;
use App\Http\Controllers\AdminAccountController;
use App\Http\Controllers\AdminPlatformSettingsController;
use App\Http\Controllers\AdminReportsController;
use App\Http\Controllers\AdminCommissionsController;
use App\Http\Controllers\AdminUsersController;
use App\Http\Controllers\AddressLookupController;
use App\Http\Controllers\MarketplaceCatalogController;
use App\Http\Controllers\MarketplaceAuthController;

use App\Models\AdminAccount;
use App\Models\BuyerAccount;
use App\Models\CourierAccount;
use App\Models\LogisticsAccount;
use App\Models\RegistrationApplication;
use Illuminate\Support\Facades\Hash;

use App\Models\SellerAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SARI Website Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('pages.home');
})->name('home');


Route::get('/login', function () {
    return view('pages.login');
})->name('login');


/*
|--------------------------------------------------------------------------
| SOCIAL LOGIN — GOOGLE / FACEBOOK
|--------------------------------------------------------------------------
|
| Social authentication always enters SARI as a Buyer.
| It never grants Seller, Courier, or Admin privileges automatically.
|
*/

Route::get(
    '/auth/{provider}/redirect',
    [SocialAuthController::class, 'redirect']
)
    ->whereIn('provider', ['google', 'facebook'])
    ->name('oauth.redirect');


Route::get(
    '/auth/{provider}/callback',
    [SocialAuthController::class, 'callback']
)
    ->whereIn('provider', ['google', 'facebook'])
    ->name('oauth.callback');



Route::post('/login', function (Request $request) {

    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    /*
    |--------------------------------------------------------------------------
    | ADMIN ACCOUNT — DATABASE BACKED
    |--------------------------------------------------------------------------
    */

    $adminEmail = strtolower(trim($request->email));
    $admin = AdminAccount::query()->where('email', $adminEmail)->first();

    if (!$admin && $adminEmail === 'admin@gmail.com') {
        $admin = AdminAccount::query()->firstOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'SARI Administrator', 'password' => Hash::make('admin123')]
        );
    }

    if ($admin && Hash::check($request->password, $admin->password)) {
        $request->session()->regenerate();
        $request->session()->put('is_admin', true);
        $request->session()->put('admin_account_id', $admin->id);
        $request->session()->forget([
            'is_seller', 'seller_account_id',
            'is_courier', 'courier_account_id', 'courier_email', 'courier_name',
            'is_buyer', 'buyer_account_id', 'buyer_social_account_id',
            'is_logistics', 'logistics_account_id', 'logistics_email', 'logistics_name',
        ]);
        return redirect()->route('admin.dashboard');
    }

    /*
    |--------------------------------------------------------------------------
    | TEMPORARY SELLER ACCOUNT
    |--------------------------------------------------------------------------
    */

    if (
        $request->email === 'seller@gmail.com' &&
        $request->password === 'seller123'
    ) {
        $seller = SellerAccount::firstOrCreate(
            [
                'email' => 'seller@gmail.com',
            ],
            [
                'store_name' => 'SARI Seller Store',
            ]
        );

        $seller = app(SellerAccountStatusService::class)->refresh($seller);
        $seller->ensureRealtimeToken();

        if (($seller->account_status ?: 'active') === 'banned') {
            return back()->withErrors([
                'email' => 'This seller account has been banned. Please contact the SARI Administrator if you need a review.',
            ])->onlyInput('email');
        }

        if (($seller->account_status ?: 'active') === 'deactivated') {
            return back()->withErrors([
                'email' => 'This seller account has been deactivated by the administrator.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $request->session()->put('is_seller', true);
        $request->session()->put('seller_account_id', $seller->id);

        $request->session()->forget([
            'is_admin',
            'is_courier',
            'courier_account_id',
            'courier_email',
            'courier_name',
            'is_buyer',
            'buyer_account_id',
            'is_logistics',
            'logistics_account_id',
            'logistics_email',
            'logistics_name',
        ]);

        /*
        | Suspended sellers are still allowed to log in so they can
        | view compliance information and contact the administrator.
        */
        return redirect()->route('seller.dashboard');
    }


    /*
    |--------------------------------------------------------------------------
    | NEW — TEMPORARY COURIER ACCOUNT
    |--------------------------------------------------------------------------
    |
    | Email: courier@gmail.com
    | Password: courier123
    |
    */

    if (
        $request->email === 'courier@gmail.com' &&
        $request->password === 'courier123'
    ) {
        $request->session()->regenerate();

        $request->session()->put('is_courier', true);
        $request->session()->put('courier_email', 'courier@gmail.com');
        $request->session()->put('courier_name', 'SARI Courier');

        /*
        | Prevent another role from remaining active in the same session.
        */
        $request->session()->forget([
            'is_admin',
            'is_seller',
            'seller_account_id',
            'is_buyer',
            'buyer_account_id',
            'is_logistics',
            'logistics_account_id',
            'logistics_email',
            'logistics_name',
        ]);

        return redirect()->route('courier.dashboard');
    }


    /*
    |--------------------------------------------------------------------------
    | TEMPORARY BUYER TEST ACCOUNT
    |--------------------------------------------------------------------------
    |
    | Email: buyer@gmail.com
    | Password: buyer123
    |
    | A real buyer_accounts row is created/updated so the Buyer pages can
    | safely use buyer_account_id during testing.
    |
    */

    if (
        $request->email === 'buyer@gmail.com' &&
        $request->password === 'buyer123'
    ) {
        $buyer = BuyerAccount::query()->updateOrCreate(
            [
                'email' => 'buyer@gmail.com',
            ],
            [
                'registration_application_id' => null,
                'last_name' => 'Buyer',
                'first_name' => 'SARI',
                'middle_initial' => null,
                'sex' => 'Male',
                'contact_no' => '09123456789',
                'birthday' => '2000-01-01',
                'age' => 26,

                'province_code' => 'TEST-PROVINCE',
                'province_name' => 'Metro Manila',
                'municipality_code' => 'TEST-CITY',
                'municipality_name' => 'Manila',
                'barangay_code' => 'TEST-BARANGAY',
                'barangay_name' => 'Test Barangay',
                'street_address' => 'SARI Buyer Test Address',

                'password' => Hash::make('buyer123'),
                'id_path' => null,
                'account_status' => 'active',
                'approved_at' => now(),
            ]
        );

        $request->session()->regenerate();

        $request->session()->put('is_buyer', true);
        $request->session()->put('buyer_account_id', $buyer->id);
        $request->session()->put('buyer_email', $buyer->email);
        $request->session()->put(
            'buyer_name',
            trim($buyer->first_name . ' ' . $buyer->last_name)
        );

        /*
        | Prevent another role/social Buyer identity from remaining active
        | in the same browser session.
        */
        $request->session()->forget([
            'buyer_social_account_id',
            'buyer_avatar',
            'is_admin',
            'is_seller',
            'seller_account_id',
            'is_courier',
            'courier_account_id',
            'courier_email',
            'courier_name',
            'is_logistics',
            'logistics_account_id',
            'logistics_email',
            'logistics_name',
        ]);

        return redirect()->route('buyer.dashboard');
    }


    /*
    |--------------------------------------------------------------------------
    | TEMPORARY LOGISTICS ACCOUNT
    |--------------------------------------------------------------------------
    |
    | Email: logistics@gmail.com
    | Password: logistics123
    |
    */

    if (
        $request->email === 'logistics@gmail.com' &&
        $request->password === 'logistics123'
    ) {
        $request->session()->regenerate();

        $request->session()->put('is_logistics', true);
        $request->session()->put('logistics_email', 'logistics@gmail.com');
        $request->session()->put('logistics_name', 'SARI Logistics');

        /*
        | Prevent another role from remaining active in the same session.
        */
        $request->session()->forget([
            'is_admin',
            'is_seller',
            'seller_account_id',
            'is_buyer',
            'buyer_account_id',
            'buyer_social_account_id',
            'buyer_email',
            'buyer_name',
            'buyer_avatar',
            'is_courier',
            'courier_account_id',
            'courier_email',
            'courier_name',
        ]);

        return redirect()->route('logistics.dashboard');
    }


    /*
    |--------------------------------------------------------------------------
    | APPROVED DATABASE ACCOUNTS
    |--------------------------------------------------------------------------
    */

    $email = strtolower(trim($request->email));

    $buyer = BuyerAccount::query()->where('email', $email)->first();

    if ($buyer && Hash::check($request->password, $buyer->password)) {
        if ($buyer->account_status !== 'active') {
            return back()->withErrors([
                'email' => 'This buyer account is not currently active.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->put('is_buyer', true);
        $request->session()->put('buyer_account_id', $buyer->id);
        $request->session()->put('buyer_email', $buyer->email);
        $request->session()->put(
            'buyer_name',
            trim($buyer->first_name . ' ' . $buyer->last_name) ?: 'SARI Buyer'
        );

        $request->session()->forget([
            'buyer_social_account_id',
            'buyer_avatar',
            'is_admin',
            'is_seller',
            'seller_account_id',
            'is_courier',
            'courier_account_id',
            'courier_email',
            'courier_name',
            'is_logistics',
            'logistics_account_id',
            'logistics_email',
            'logistics_name',
        ]);

        return redirect()->route('buyer.dashboard');
    }

    $registeredSeller = SellerAccount::query()
        ->where('email', $email)
        ->whereNotNull('password')
        ->first();

    if (
        $registeredSeller
        && Hash::check($request->password, $registeredSeller->password)
    ) {
        if (($registeredSeller->registration_status ?? 'approved') !== 'approved') {
            return back()->withErrors([
                'email' => 'This seller registration has not been approved yet.',
            ])->onlyInput('email');
        }

        $registeredSeller = app(SellerAccountStatusService::class)
            ->refresh($registeredSeller);

        $registeredSeller->ensureRealtimeToken();

        if (($registeredSeller->account_status ?: 'active') === 'banned') {
            return back()->withErrors([
                'email' => 'This seller account has been banned.',
            ])->onlyInput('email');
        }

        if (($registeredSeller->account_status ?: 'active') === 'deactivated') {
            return back()->withErrors([
                'email' => 'This seller account has been deactivated.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->put('is_seller', true);
        $request->session()->put('seller_account_id', $registeredSeller->id);

        $request->session()->forget([
            'is_admin',
            'is_buyer',
            'buyer_account_id',
            'is_courier',
            'courier_account_id',
            'courier_email',
            'courier_name',
            'is_logistics',
            'logistics_account_id',
            'logistics_email',
            'logistics_name',
        ]);

        return redirect()->route('seller.dashboard');
    }

    $courier = CourierAccount::query()->where('email', $email)->first();

    if ($courier && Hash::check($request->password, $courier->password)) {
        if ($courier->account_status !== 'active') {
            return back()->withErrors([
                'email' => 'This courier account is not currently active.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->put('is_courier', true);
        $request->session()->put('courier_account_id', $courier->id);
        $request->session()->put('courier_email', $courier->email);
        $request->session()->put(
            'courier_name',
            trim($courier->first_name . ' ' . $courier->last_name)
        );

        $request->session()->forget([
            'is_admin',
            'is_seller',
            'seller_account_id',
            'is_buyer',
            'buyer_account_id',
            'is_logistics',
            'logistics_account_id',
            'logistics_email',
            'logistics_name',
        ]);

        return redirect()->route('courier.dashboard');
    }

    $logistics = LogisticsAccount::query()->where('email', $email)->first();

    if ($logistics && Hash::check($request->password, $logistics->password)) {
        if ($logistics->account_status !== 'active') {
            return back()->withErrors([
                'email' => 'This logistics account is not currently active.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->put('is_logistics', true);
        $request->session()->put('logistics_account_id', $logistics->id);
        $request->session()->put('logistics_email', $logistics->email);
        $request->session()->put(
            'logistics_name',
            trim($logistics->first_name . ' ' . $logistics->last_name) ?: 'SARI Logistics'
        );

        $request->session()->forget([
            'is_admin',
            'is_seller',
            'seller_account_id',
            'is_buyer',
            'buyer_account_id',
            'buyer_social_account_id',
            'buyer_email',
            'buyer_name',
            'buyer_avatar',
            'is_courier',
            'courier_account_id',
            'courier_email',
            'courier_name',
        ]);

        return redirect()->route('logistics.dashboard');
    }

    $application = RegistrationApplication::query()
        ->where('email', $email)
        ->latest('id')
        ->first();

    if ($application && $application->status === 'pending') {
        $message = $application->role === 'rider'
            ? 'Your Rider application is still waiting for SARI Logistics review.'
            : 'Your registration is still waiting for administrator approval.';

        return back()->withErrors([
            'email' => $message,
        ])->onlyInput('email');
    }

    if ($application && $application->status === 'rejected') {
        return back()->withErrors([
            'email' => 'Your registration was not approved. You may submit a corrected registration using the same email address.',
        ])->onlyInput('email');
    }

    /*
    |--------------------------------------------------------------------------
    | INVALID LOGIN
    |--------------------------------------------------------------------------
    */

    return back()
        ->withErrors([
            'email' => 'Invalid email or password.',
        ])
        ->onlyInput('email');

})->name('login.submit');


Route::get(
    '/register',
    [RegistrationController::class, 'create']
)->name('register');

Route::post(
    '/register',
    [RegistrationController::class, 'store']
)->name('register.submit');

Route::get(
    '/registration/pending',
    [RegistrationController::class, 'pending']
)->name('registration.pending');

Route::get(
    '/registration/status',
    [RegistrationController::class, 'status']
)->name('registration.status');

Route::post('/complaints', [PlatformComplaintController::class, 'store'])
    ->name('platform.complaints.store');


/*
|--------------------------------------------------------------------------
| PUBLIC MARKETPLACE CATALOG
|--------------------------------------------------------------------------
|
| Guests can browse category products. Authentication is required only when
| they choose a buying action, which uses a Buyer-only login endpoint.
|
*/

Route::get('/marketplace/categories/{category}', [MarketplaceCatalogController::class, 'category'])
    ->name('marketplace.category');

Route::get('/marketplace/products/{product}/image', [MarketplaceCatalogController::class, 'image'])
    ->whereNumber('product')
    ->name('marketplace.product.image');

Route::post('/marketplace/login', [MarketplaceAuthController::class, 'login'])
    ->name('marketplace.login');


/*
|--------------------------------------------------------------------------
| ADDRESS LOOKUP
|--------------------------------------------------------------------------
*/

Route::get(
    '/address/provinces',
    [AddressLookupController::class, 'provinces']
)->name('address.provinces');

Route::get(
    '/address/provinces/{provinceCode}/municipalities',
    [AddressLookupController::class, 'municipalities']
)->name('address.municipalities');

Route::get(
    '/address/municipalities/{municipalityCode}/barangays',
    [AddressLookupController::class, 'barangays']
)->name('address.barangays');


/*
|--------------------------------------------------------------------------
| BUYER ROUTES — FULL BUYER ↔ SELLER INTEGRATION
|--------------------------------------------------------------------------
*/

Route::get('/buyer/dashboard', [BuyerPageController::class, 'home'])
    ->name('buyer.dashboard');

Route::get('/buyer/home', [BuyerPageController::class, 'home'])
    ->name('buyer.home');

Route::get('/buyer/products', [BuyerProductController::class, 'index'])
    ->name('buyer.products');

Route::get('/buyer/products/{product}/image', [BuyerProductController::class, 'image'])
    ->whereNumber('product')
    ->name('buyer.product.image');

Route::get('/buyer/product-images/{image}/image', [BuyerProductController::class, 'galleryImage'])
    ->whereNumber('image')
    ->name('buyer.product.gallery-image');

Route::get('/buyer/product-variants/{variant}/image', [BuyerProductController::class, 'variantImage'])
    ->whereNumber('variant')
    ->name('buyer.product.variant-image');

Route::get('/buyer/shop/{shop}', [BuyerProductController::class, 'shop'])
    ->name('buyer.shop');

Route::get('/buyer/products/{product}', [BuyerProductController::class, 'show'])
    ->whereNumber('product')
    ->name('buyer.product.details');

/* Buyer cart — MySQL backed */
Route::get('/buyer/cart', [BuyerCartController::class, 'index'])
    ->name('buyer.cart');
Route::post('/buyer/cart/items', [BuyerCartController::class, 'store'])
    ->name('buyer.cart.items.store');
Route::patch('/buyer/cart/items/{item}', [BuyerCartController::class, 'update'])
    ->name('buyer.cart.items.update');
Route::delete('/buyer/cart/items/{item}', [BuyerCartController::class, 'destroy'])
    ->name('buyer.cart.items.destroy');
Route::get('/buyer/cart-summary', [BuyerCartController::class, 'summary'])
    ->name('buyer.cart.summary');

/* Checkout creates real MarketplaceOrder records grouped by seller */
Route::get('/buyer/checkout', [BuyerCheckoutController::class, 'index'])
    ->name('buyer.checkout');
Route::post('/buyer/checkout', [BuyerCheckoutController::class, 'store'])
    ->name('buyer.checkout.store');

/* Buyer order history + live tracking + early cancellation */
Route::get('/buyer/orders', [BuyerOrderController::class, 'index'])
    ->name('buyer.orders');

Route::get('/buyer/orders/{order}', [BuyerOrderController::class, 'show'])
    ->name('buyer.orders.show');

Route::post('/buyer/orders/{order}/cancel', [BuyerOrderController::class, 'cancel'])
    ->name('buyer.orders.cancel');
Route::get('/buyer/orders-live-state', [BuyerOrderController::class, 'liveState'])
    ->name('buyer.orders.live-state');
Route::post('/buyer/orders/{order}/reviews', [BuyerReviewController::class, 'store'])
    ->name('buyer.orders.reviews.store');

/* Buyer ↔ Seller direct messaging */
Route::get('/buyer/messages', [BuyerSellerMessageController::class, 'buyerIndex'])
    ->name('buyer.messages');
Route::post('/buyer/messages/{seller}', [BuyerSellerMessageController::class, 'buyerSend'])
    ->name('buyer.messages.send');

/* Buyer account + dynamic rewards */
Route::get('/buyer/account', [BuyerAccountController::class, 'index'])
    ->name('buyer.account');
Route::patch('/buyer/account', [BuyerAccountController::class, 'update'])
    ->name('buyer.account.update');
Route::get('/buyer/rewards', [BuyerPageController::class, 'rewards'])
    ->name('buyer.rewards');

Route::post('/buyer/logout', [BuyerPageController::class, 'logout'])
    ->name('buyer.logout');


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    ->name('admin.dashboard');


Route::get(
    '/admin/registrations',
    [AdminRegistrationController::class, 'index']
)->name('admin.registrations');

Route::post(
    '/admin/registrations/{application}/approve',
    [AdminRegistrationController::class, 'approve']
)->name('admin.registrations.approve');

Route::post(
    '/admin/registrations/{application}/reject',
    [AdminRegistrationController::class, 'reject']
)->name('admin.registrations.reject');

Route::get(
    '/admin/registrations/{application}/document/{document}',
    [AdminRegistrationController::class, 'document']
)->name('admin.registrations.document');


/*
|--------------------------------------------------------------------------
| ADMIN — USER MANAGEMENT
|--------------------------------------------------------------------------
*/
Route::get('/admin/users', [AdminUsersController::class, 'index'])
    ->name('admin.users');

Route::patch('/admin/users/{role}/{id}', [AdminUsersController::class, 'update'])
    ->whereIn('role', ['buyer', 'seller', 'rider', 'logistics'])
    ->whereNumber('id')
    ->name('admin.users.update');

Route::post('/admin/users/{role}/{id}/suspend', [AdminUsersController::class, 'suspend'])
    ->whereIn('role', ['buyer', 'seller', 'rider', 'logistics', 'social_buyer'])
    ->whereNumber('id')
    ->name('admin.users.suspend');

Route::post('/admin/users/{role}/{id}/restore', [AdminUsersController::class, 'restore'])
    ->whereIn('role', ['buyer', 'seller', 'rider', 'logistics', 'social_buyer'])
    ->whereNumber('id')
    ->name('admin.users.restore');

Route::post('/admin/users/{role}/{id}/ban', [AdminUsersController::class, 'ban'])
    ->whereIn('role', ['buyer', 'seller', 'rider', 'logistics', 'social_buyer'])
    ->whereNumber('id')
    ->name('admin.users.ban');

Route::post('/admin/users/{role}/{id}/unban', [AdminUsersController::class, 'unban'])
    ->whereIn('role', ['buyer', 'seller', 'rider', 'logistics', 'social_buyer'])
    ->whereNumber('id')
    ->name('admin.users.unban');

Route::post('/admin/users/{role}/{id}/note', [AdminUsersController::class, 'note'])
    ->whereIn('role', ['buyer', 'seller', 'rider', 'logistics', 'social_buyer'])
    ->whereNumber('id')
    ->name('admin.users.note');


/*
|--------------------------------------------------------------------------
| ADMIN — SELLER COMPLIANCE
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/seller-compliance',
    [AdminSellerComplianceController::class, 'index']
)->name('admin.seller-compliance');


Route::post(
    '/admin/seller-compliance/products/{product}/approve',
    [AdminSellerComplianceController::class, 'approve']
)->name('admin.compliance.products.approve');


Route::post(
    '/admin/seller-compliance/products/{product}/reject',
    [AdminSellerComplianceController::class, 'reject']
)->name('admin.compliance.products.reject');


Route::post(
    '/admin/seller-compliance/products/{product}/warn',
    [AdminSellerComplianceController::class, 'warn']
)->name('admin.compliance.products.warn');


Route::post(
    '/admin/seller-compliance/sellers/{seller}/suspend-30',
    [AdminSellerAccountStatusController::class, 'suspend30']
)->name('admin.compliance.sellers.suspend30');


Route::post(
    '/admin/seller-compliance/sellers/{seller}/unsuspend',
    [AdminSellerAccountStatusController::class, 'liftSuspension']
)->name('admin.compliance.sellers.unsuspend');


Route::post(
    '/admin/seller-compliance/sellers/{seller}/reply',
    [AdminSellerComplianceController::class, 'reply']
)->name('admin.compliance.sellers.reply');


/*
|--------------------------------------------------------------------------
| ADMIN — SELLER ACCOUNT CONTROL
|--------------------------------------------------------------------------
*/
Route::get(
    '/admin/seller-account-control',
    [AdminSellerAccountStatusController::class, 'index']
)->name('admin.seller-accounts.control');

Route::post(
    '/admin/seller-account-control/{seller}/ban',
    [AdminSellerAccountStatusController::class, 'ban']
)->name('admin.seller-accounts.ban');

Route::post(
    '/admin/seller-account-control/{seller}/unban',
    [AdminSellerAccountStatusController::class, 'unban']
)->name('admin.seller-accounts.unban');

Route::post(
    '/admin/seller-account-control/{seller}/deactivate',
    [AdminSellerAccountStatusController::class, 'deactivate']
)->name('admin.seller-accounts.deactivate');

Route::post(
    '/admin/seller-account-control/{seller}/restore',
    [AdminSellerAccountStatusController::class, 'restore']
)->name('admin.seller-accounts.restore');


Route::get('/admin/complaints', [AdminComplaintsController::class, 'index'])
    ->name('admin.complaints');

Route::post('/admin/complaints/{complaint}/resolve', [AdminComplaintsController::class, 'resolve'])
    ->name('admin.complaints.resolve');

Route::post('/admin/complaints/{complaint}/reopen', [AdminComplaintsController::class, 'reopen'])
    ->name('admin.complaints.reopen');


Route::get('/admin/commissions', [AdminCommissionsController::class, 'index'])
    ->name('admin.commissions');

Route::post('/admin/commissions/payouts/{payout}/approve', [AdminCommissionsController::class, 'approvePayout'])
    ->name('admin.commissions.payouts.approve');
Route::post('/admin/commissions/payouts/{payout}/paid', [AdminCommissionsController::class, 'markPayoutPaid'])
    ->name('admin.commissions.payouts.paid');
Route::post('/admin/commissions/payouts/{payout}/reject', [AdminCommissionsController::class, 'rejectPayout'])
    ->name('admin.commissions.payouts.reject');


Route::get('/admin/reports', [AdminReportsController::class, 'index'])
    ->name('admin.reports');

Route::get('/admin/reports/export', [AdminReportsController::class, 'export'])
    ->name('admin.reports.export');


Route::get('/admin/platform-settings', [AdminPlatformSettingsController::class, 'index'])
    ->name('admin.platform-settings');

Route::patch('/admin/platform-settings', [AdminPlatformSettingsController::class, 'update'])
    ->name('admin.platform-settings.update');


Route::get(
    '/admin/messages',
    [SellerAdminChatController::class, 'adminIndex']
)->name('admin.messages');


Route::post(
    '/admin/messages/{seller}',
    [SellerAdminChatController::class, 'adminSend']
)->name('admin.messages.send');


Route::post(
    '/admin/messages/{seller}/read',
    [SellerAdminChatController::class, 'adminRead']
)->name('admin.messages.read');


Route::get('/admin/account', [AdminAccountController::class, 'index'])
    ->name('admin.account');

Route::patch('/admin/account', [AdminAccountController::class, 'update'])
    ->name('admin.account.update');

Route::patch('/admin/account/profile', [AdminAccountController::class, 'updateProfile'])
    ->name('admin.account.profile.update');

Route::patch('/admin/account/password', [AdminAccountController::class, 'updatePassword'])
    ->name('admin.account.password.update');


Route::post('/admin/logout', function (Request $request) {

    $request->session()->forget(['is_admin', 'admin_account_id']);

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');

})->name('admin.logout');


/*
|--------------------------------------------------------------------------
| SELLER ROUTES
|--------------------------------------------------------------------------
*/

Route::get(
    '/seller/dashboard',
    [SellerDashboardController::class, 'index']
)->middleware(EnsureSellerAccountAccessible::class)
  ->name('seller.dashboard');


/*
|--------------------------------------------------------------------------
| SELLER ACCOUNT STATE — REALTIME FALLBACK
|--------------------------------------------------------------------------
|
| Reverb handles instant account-status events when it is running.
| This JSON endpoint is a lightweight fallback used by the Seller layout
| so an Admin manual suspension still locks an already-open Seller page
| even when Reverb is temporarily unavailable.
|
*/
Route::get('/seller/account-state', function (Request $request) {

    if (!$request->session()->get('is_seller')) {
        return response()->json([
            'authenticated' => false,
        ], 401);
    }

    $seller = SellerAccount::find(
        $request->session()->get('seller_account_id')
    );

    if (!$seller) {
        return response()->json([
            'authenticated' => false,
        ], 401);
    }

    $seller = app(\App\Services\SellerAccountStatusService::class)
        ->refresh($seller);

    $status = (string) ($seller->account_status ?: 'active');

    $activeSuspension = $seller->suspended_until
        ? now()->lt(\Illuminate\Support\Carbon::parse($seller->suspended_until))
        : false;

    return response()->json([
        'authenticated' => true,
        'account_status' => $status,
        'warning_count' => (int) ($seller->warning_count ?? 0),
        'suspended_until' => $seller->suspended_until?->toIso8601String(),
        'restricted' => $status === 'active'
            && (
                (int) ($seller->warning_count ?? 0) >= 3
                || $activeSuspension
            ),
    ]);

})->name('seller.account-state');



/*
|--------------------------------------------------------------------------
| SELLER PRODUCT MANAGEMENT
|--------------------------------------------------------------------------
|
| Important:
| - seller.products.archive opens the Archived Products page.
| - seller.products.archive-product archives ONE product.
| - seller.products.delete is a recoverable delete that sends the product
|   to Archived Products instead of permanently deleting compliance history.
|
*/

/*
| Dedicated Products Page
| Presentation only: it reuses the existing library + CRUD endpoints below.
*/
Route::get(
    '/seller/products',
    [SellerProductController::class, 'index']
)->middleware(EnsureSellerNotRestricted::class)
  ->name('seller.products.index');


/*
| Dedicated Add Product Page
*/
Route::get(
    '/seller/products/create',
    [SellerProductController::class, 'create']
)->middleware(EnsureSellerNotRestricted::class)
  ->name('seller.products.create');


/*
| Add Product
*/
Route::post(
    '/seller/products',
    [SellerProductController::class, 'store']
)->middleware(EnsureSellerNotRestricted::class)
  ->name('seller.products.store');


/*
| Multiple Persistent Add Product Drafts
*/
Route::get(
    '/seller/product-drafts',
    [SellerProductDraftController::class, 'index']
)->middleware(EnsureSellerNotRestricted::class)
  ->name('seller.products.draft');

Route::post(
    '/seller/product-drafts',
    [SellerProductDraftController::class, 'store']
)->middleware(EnsureSellerNotRestricted::class)
  ->name('seller.products.draft.save');

Route::delete(
    '/seller/product-drafts',
    [SellerProductDraftController::class, 'destroy']
)->middleware(EnsureSellerNotRestricted::class)
  ->name('seller.products.draft.delete');

Route::get(
    '/seller/product-drafts/media/{kind}/{key?}',
    [SellerProductDraftController::class, 'media']
)->middleware(EnsureSellerNotRestricted::class)
  ->whereIn('kind', ['cover', 'gallery', 'variant'])
  ->name('seller.products.draft-media');


/*
| Lazy Product Library JSON
| Loaded only when the Seller opens the full Product Library modal.
*/
Route::get(
    '/seller/products/library-data',
    [SellerProductController::class, 'library']
)->middleware(EnsureSellerNotRestricted::class)
  ->name('seller.products.library');


/*
| Archived Products Page
| Keep this route above product parameter routes for clarity.
*/
Route::get(
    '/seller/products/archive',
    [SellerProductController::class, 'archived']
)->middleware(EnsureSellerNotRestricted::class)
  ->name('seller.products.archive');


/*
| Product Version Image
*/
Route::get(
    '/seller/product-versions/{version}/image',
    [SellerProductController::class, 'versionImage']
)->name('seller.products.version-image');


/*
| Current Product Image
*/
Route::get(
    '/seller/products/{product}/image',
    [SellerProductController::class, 'image']
)->name('seller.products.image');


Route::get(
    '/seller/product-images/{image}/image',
    [SellerProductController::class, 'galleryImage']
)->whereNumber('image')
  ->name('seller.products.gallery-image');

Route::get(
    '/seller/product-variants/{variant}/image',
    [SellerProductController::class, 'variantImage']
)->whereNumber('variant')
  ->name('seller.products.variant-image');


/*
| Update / Edit Product
*/
Route::put(
    '/seller/products/{product}',
    [SellerProductController::class, 'update']
)->middleware(EnsureSellerNotRestricted::class)
  ->name('seller.products.update');


/*
| Archive One Product
*/
Route::post(
    '/seller/products/{product}/archive',
    [SellerProductController::class, 'archive']
)->middleware(EnsureSellerNotRestricted::class)
  ->name('seller.products.archive-product');


/*
| Recoverable Delete
*/
Route::post(
    '/seller/products/{product}/delete',
    [SellerProductController::class, 'destroy']
)->middleware(EnsureSellerNotRestricted::class)
  ->name('seller.products.delete');


/*
| Restore Archived / Deleted Product
*/
Route::post(
    '/seller/products/{product}/restore',
    [SellerProductController::class, 'restore']
)->middleware(EnsureSellerNotRestricted::class)
  ->name('seller.products.restore');


/*
|--------------------------------------------------------------------------
| SELLER — BUYER INBOX + PRODUCT REVIEWS
|--------------------------------------------------------------------------
*/

Route::get('/seller/buyer-messages', [SellerBuyerMessageController::class, 'index'])
    ->middleware(EnsureSellerNotRestricted::class)
    ->name('seller.buyer-messages');

Route::post('/seller/buyer-messages/{buyerKey}', [SellerBuyerMessageController::class, 'send'])
    ->middleware(EnsureSellerNotRestricted::class)
    ->name('seller.buyer-messages.send');

Route::get('/seller/reviews', [SellerReviewController::class, 'index'])
    ->middleware(EnsureSellerNotRestricted::class)
    ->name('seller.reviews');


/*
|--------------------------------------------------------------------------
| SELLER COMPLIANCE MESSAGE
|--------------------------------------------------------------------------
*/

Route::post(
    '/seller/compliance/message',
    [SellerComplianceMessageController::class, 'store']
)->middleware(EnsureSellerAccountAccessible::class)
  ->name('seller.compliance.message');


/*
|--------------------------------------------------------------------------
| SELLER ORDER / REPORT / MESSAGE / ACCOUNT PAGES
|--------------------------------------------------------------------------
*/

Route::get(
    '/seller/orders',
    [SellerOrderController::class, 'index']
)->middleware(EnsureSellerNotRestricted::class)
  ->name('seller.orders');

Route::post(
    '/seller/orders/{order}/prepare',
    [SellerOrderController::class, 'prepare']
)->middleware(EnsureSellerNotRestricted::class)
  ->name('seller.orders.prepare');

Route::post(
    '/seller/orders/{order}/ready-pickup',
    [SellerOrderController::class, 'readyForPickup']
)->middleware(EnsureSellerNotRestricted::class)
  ->name('seller.orders.ready-pickup');

Route::get(
    '/seller/orders/{order}/waybill',
    [SellerOrderController::class, 'waybill']
)->middleware(EnsureSellerNotRestricted::class)
  ->name('seller.orders.waybill');

Route::get(
    '/seller/orders-live-state',
    [SellerOrderController::class, 'liveState']
)->name('seller.orders.live-state');


Route::get(
    '/seller/reports',
    [\App\Http\Controllers\SellerReportController::class, 'index']
)
    ->middleware(\App\Http\Middleware\EnsureSellerNotRestricted::class)
    ->name('seller.reports');


Route::get(
    '/seller/reports/download',
    [\App\Http\Controllers\SellerReportController::class, 'downloadCsv']
)
    ->middleware(\App\Http\Middleware\EnsureSellerNotRestricted::class)
    ->name('seller.reports.download');


Route::get(
    '/seller/messages',
    [SellerAdminChatController::class, 'sellerIndex']
)->middleware(EnsureSellerNotRestricted::class)
  ->name('seller.messages');


Route::post(
    '/seller/messages',
    [SellerAdminChatController::class, 'sellerSend']
)->middleware([
    EnsureSellerNotRestricted::class,
    HandleSellerSupportChat::class,
])->name('seller.messages.send');


Route::post(
    '/seller/messages/read',
    [SellerAdminChatController::class, 'sellerRead']
)->middleware(EnsureSellerNotRestricted::class)
  ->name('seller.messages.read');


Route::get('/seller/account', function (Request $request) {

    if (!$request->session()->get('is_seller')) {
        return redirect()->route('login');
    }

    return view('seller.account');

})->middleware(EnsureSellerNotRestricted::class)
  ->name('seller.account');


/*
| Protected seller/admin chat attachment
*/
Route::get(
    '/messages/attachments/{message}',
    [SellerAdminChatController::class, 'attachment']
)->name('chat.attachments.show');


Route::post('/seller/logout', function (Request $request) {

    $request->session()->forget([
        'is_seller',
        'seller_account_id',
            'is_logistics',
            'logistics_account_id',
            'logistics_email',
            'logistics_name',
    ]);

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');

})->name('seller.logout');


/*
|--------------------------------------------------------------------------
| NEW — COURIER ROUTES
|--------------------------------------------------------------------------
|
| Temporary courier authentication.
| Later, we will connect this to a real courier_accounts MySQL table.
|
*/


/*
|--------------------------------------------------------------------------
| COURIER DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get(
    '/courier/dashboard',
    [CourierDashboardController::class, 'index']
)->name('courier.dashboard');


/*
|--------------------------------------------------------------------------
| COURIER DELIVERY PROCESS
|--------------------------------------------------------------------------
|
| Added for the Courier Dashboard delivery workflow only.
| Existing Admin, Seller, Product, Compliance, and Chat backend routes
| above are left unchanged.
|
*/

Route::get(
    '/courier/requests',
    [CourierDeliveryController::class, 'requests']
)->name('courier.requests');

Route::post(
    '/courier/orders/{order}/accept',
    [CourierDeliveryController::class, 'accept']
)->name('courier.orders.accept');

Route::post(
    '/courier/orders/{order}/proceed-pickup',
    [CourierDeliveryController::class, 'proceedToPickup']
)->name('courier.orders.proceed-pickup');

Route::post(
    '/courier/orders/{order}/arrived-pickup',
    [CourierDeliveryController::class, 'arrivedAtPickup']
)->name('courier.orders.arrived-pickup');

Route::post(
    '/courier/orders/{order}/confirm-pickup',
    [CourierDeliveryController::class, 'confirmPickup']
)->name('courier.orders.confirm-pickup');

Route::post(
    '/courier/orders/{order}/arrived-buyer',
    [CourierDeliveryController::class, 'arrivedAtBuyer']
)->name('courier.orders.arrived-buyer');

Route::post(
    '/courier/orders/{order}/complete',
    [CourierDeliveryController::class, 'complete']
)->name('courier.orders.complete');

Route::get(
    '/courier/orders-live-state',
    [CourierDeliveryController::class, 'liveState']
)->name('courier.orders.live-state');


/*
|--------------------------------------------------------------------------
| COURIER LOGOUT
|--------------------------------------------------------------------------
*/

Route::post('/courier/logout', function (Request $request) {

    $request->session()->forget([
        'is_courier',
        'courier_account_id',
        'courier_email',
        'courier_name',
            'is_logistics',
            'logistics_account_id',
            'logistics_email',
            'logistics_name',
    ]);

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');

})->name('courier.logout');


/*
|--------------------------------------------------------------------------
| COURIER PAGES
|--------------------------------------------------------------------------
*/

Route::get('/courier/pickups', function (Request $request) {
    if (!$request->session()->get('is_courier')) {
        return redirect()->route('login');
    }

    $orders = \App\Models\MarketplaceOrder::query()
        ->where('courier_email', $request->session()->get('courier_email', 'courier@gmail.com'))
        ->whereIn('status', ['courier_accepted', 'heading_pickup', 'arrived_pickup'])
        ->latest('updated_at')
        ->get();

    return view('courier.pickups', compact('orders'));
})->name('courier.pickups');

Route::get('/courier/deliveries', function (Request $request) {
    if (!$request->session()->get('is_courier')) {
        return redirect()->route('login');
    }

    $orders = \App\Models\MarketplaceOrder::query()
        ->where('courier_email', $request->session()->get('courier_email', 'courier@gmail.com'))
        ->whereIn('status', ['in_transit', 'arrived_buyer'])
        ->latest('updated_at')
        ->get();

    return view('courier.deliveries', compact('orders'));
})->name('courier.deliveries');

Route::get(
    '/courier/earnings',
    [\App\Http\Controllers\CourierPageController::class, 'earnings']
)->name('courier.earnings');

Route::get('/courier/earnings/statement', [CourierPageController::class, 'earningsStatement'])
    ->name('courier.earnings.statement');
Route::post('/courier/earnings/payout', [CourierPageController::class, 'requestPayout'])
    ->name('courier.earnings.payout');


Route::get(
    '/courier/history',
    [\App\Http\Controllers\CourierPageController::class, 'history']
)->name('courier.history');

Route::get('/courier/history/export', [CourierPageController::class, 'historyExport'])
    ->name('courier.history.export');


Route::get(
    '/courier/messages',
    [\App\Http\Controllers\CourierPageController::class, 'messages']
)->name('courier.messages');

Route::post('/courier/messages', [CourierPageController::class, 'sendMessage'])
    ->name('courier.messages.send');


Route::get(
    '/courier/profile',
    [\App\Http\Controllers\CourierPageController::class, 'profile']
)->name('courier.profile');

Route::patch('/courier/profile', [CourierPageController::class, 'updateProfile'])
    ->name('courier.profile.update');

Route::patch('/courier/profile/availability', [CourierPageController::class, 'availability'])
    ->name('courier.profile.availability');

Route::post(
    '/admin/message-notifications/read-all',
    function (\Illuminate\Http\Request $request) {

        if (!$request->session()->get('is_admin')) {
            abort(403, 'Admin session required.');
        }

        $updated = \App\Models\ChatMessage::query()
            ->where('sender_role', 'seller')
            ->whereNull('read_by_admin_at')
            ->update([
                'read_by_admin_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'updated' => $updated,
        ]);
    }
)->name('admin.message-notifications.read-all');



Route::get(
    '/seller/messages/{message}/meta',
    [ChatMessageReactionController::class, 'sellerMeta']
)->name('seller.messages.meta');

Route::post(
    '/seller/messages/{message}/reaction',
    [ChatMessageReactionController::class, 'sellerToggle']
)->name('seller.messages.react');







Route::post(
    '/admin/messages/presence',
    [AdminSellerChatActionController::class, 'presence']
)->name('admin.messages.presence');

Route::post(
    '/admin/messages/{seller}/warning',
    [AdminSellerChatActionController::class, 'warning']
)->name('admin.messages.warning');

Route::post(
    '/admin/messages/{seller}/suspend',
    [AdminSellerChatActionController::class, 'suspend']
)->name('admin.messages.suspend');

Route::post(
    '/admin/messages/{seller}/block',
    [AdminSellerChatActionController::class, 'block']
)->name('admin.messages.block');

Route::post(
    '/admin/messages/{seller}/unblock',
    [AdminSellerChatActionController::class, 'unblock']
)->name('admin.messages.unblock');

Route::get(
    '/seller/layout-state',
    SellerLayoutStateController::class
)->middleware(EnsureSellerAccountAccessible::class)
  ->name('seller.layout-state');


/*
|--------------------------------------------------------------------------
| SARI Password Recovery — Email OTP
|--------------------------------------------------------------------------
|
| Email -> 6-digit OTP -> new password -> success.
| The page uses JSON requests so every step happens without leaving the page.
|
*/

Route::get('/forgot-password', [PasswordResetOtpController::class, 'show'])
    ->name('password.request');

Route::post('/forgot-password/send-code', [PasswordResetOtpController::class, 'sendCode'])
    ->middleware('throttle:8,1')
    ->name('password.otp.send');

// Compatibility route for older SARI forgot-password forms.
Route::post('/forgot-password', [PasswordResetOtpController::class, 'sendCode'])
    ->middleware('throttle:8,1')
    ->name('password.email');

Route::post('/forgot-password/verify-code', [PasswordResetOtpController::class, 'verifyCode'])
    ->middleware('throttle:12,1')
    ->name('password.otp.verify');

Route::post('/forgot-password/reset', [PasswordResetOtpController::class, 'reset'])
    ->middleware('throttle:8,1')
    ->name('password.update');
Route::post('/forgot-password/restart', [PasswordResetOtpController::class, 'restart'])
    ->middleware('throttle:12,1')
    ->name('password.otp.restart');

/*
|--------------------------------------------------------------------------
| LOGISTICS ROUTES
|--------------------------------------------------------------------------
*/

require __DIR__.'/logistics.php';





require __DIR__.'/messaging.php';
