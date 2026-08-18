<?php

use App\Http\Controllers\AdminSellerComplianceController;
use App\Http\Controllers\SellerComplianceMessageController;
use App\Http\Controllers\SellerDashboardController;
use App\Http\Controllers\SellerAdminChatController;
use App\Http\Controllers\SellerProductController;
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

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    /*
    |--------------------------------------------------------------------------
    | TEMPORARY ADMIN ACCOUNT
    |--------------------------------------------------------------------------
    */

    if (
        $request->email === 'admin@gmail.com' &&
        $request->password === 'admin123'
    ) {
        $request->session()->regenerate();

        $request->session()->put('is_admin', true);

        $request->session()->forget([
            'is_seller',
            'seller_account_id',
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

        $seller->refreshSuspensionStatus();
        $seller->ensureRealtimeToken();

        $request->session()->regenerate();

        $request->session()->put('is_seller', true);
        $request->session()->put('seller_account_id', $seller->id);

        $request->session()->forget('is_admin');

        /*
        | Suspended sellers are still allowed to log in so they can
        | view compliance information and contact the administrator.
        */
        return redirect()->route('seller.dashboard');
    }

    return back()
        ->withErrors([
            'email' => 'Invalid email or password.',
        ])
        ->onlyInput('email');
})->name('login.submit');

Route::get('/register', function () {
    return view('pages.register');
})->name('register');


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/admin/dashboard', function (Request $request) {
    if (!$request->session()->get('is_admin')) {
        return redirect()->route('login');
    }

    return view('admin.dashboard');
})->name('admin.dashboard');


Route::get('/admin/registrations', function (Request $request) {
    if (!$request->session()->get('is_admin')) {
        return redirect()->route('login');
    }

    return view('admin.registrations');
})->name('admin.registrations');


Route::get('/admin/users', function (Request $request) {
    if (!$request->session()->get('is_admin')) {
        return redirect()->route('login');
    }

    return view('admin.users');
})->name('admin.users');


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
    [AdminSellerComplianceController::class, 'suspend30']
)->name('admin.compliance.sellers.suspend30');


Route::post(
    '/admin/seller-compliance/sellers/{seller}/unsuspend',
    [AdminSellerComplianceController::class, 'unsuspend']
)->name('admin.compliance.sellers.unsuspend');


Route::post(
    '/admin/seller-compliance/sellers/{seller}/reply',
    [AdminSellerComplianceController::class, 'reply']
)->name('admin.compliance.sellers.reply');


Route::get('/admin/complaints', function (Request $request) {
    if (!$request->session()->get('is_admin')) {
        return redirect()->route('login');
    }

    return view('admin.complaints');
})->name('admin.complaints');


Route::get('/admin/commissions', function (Request $request) {
    if (!$request->session()->get('is_admin')) {
        return redirect()->route('login');
    }

    return view('admin.commissions');
})->name('admin.commissions');


Route::get('/admin/reports', function (Request $request) {
    if (!$request->session()->get('is_admin')) {
        return redirect()->route('login');
    }

    return view('admin.reports');
})->name('admin.reports');


Route::get('/admin/platform-settings', function (Request $request) {
    if (!$request->session()->get('is_admin')) {
        return redirect()->route('login');
    }

    return view('admin.platform-settings');
})->name('admin.platform-settings');


Route::get('/admin/messages', [SellerAdminChatController::class, 'adminIndex'])
    ->name('admin.messages');

Route::post('/admin/messages/{seller}', [SellerAdminChatController::class, 'adminSend'])
    ->name('admin.messages.send');

Route::post('/admin/messages/{seller}/read', [SellerAdminChatController::class, 'adminRead'])
    ->name('admin.messages.read');


Route::get('/admin/account', function (Request $request) {
    if (!$request->session()->get('is_admin')) {
        return redirect()->route('login');
    }

    return view('admin.account');
})->name('admin.account');


Route::post('/admin/logout', function (Request $request) {
    $request->session()->forget('is_admin');

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
)->name('seller.dashboard');


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
| Add Product
*/
Route::post(
    '/seller/products',
    [SellerProductController::class, 'store']
)->name('seller.products.store');


/*
| Archived Products Page
| Keep this route above product parameter routes for clarity.
*/
Route::get(
    '/seller/products/archive',
    [SellerProductController::class, 'archived']
)->name('seller.products.archive');


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


/*
| Update / Edit Product
*/
Route::put(
    '/seller/products/{product}',
    [SellerProductController::class, 'update']
)->name('seller.products.update');


/*
| Archive One Product
*/
Route::post(
    '/seller/products/{product}/archive',
    [SellerProductController::class, 'archive']
)->name('seller.products.archive-product');


/*
| Recoverable Delete
*/
Route::post(
    '/seller/products/{product}/delete',
    [SellerProductController::class, 'destroy']
)->name('seller.products.delete');


/*
| Restore Archived / Deleted Product
*/
Route::post(
    '/seller/products/{product}/restore',
    [SellerProductController::class, 'restore']
)->name('seller.products.restore');


/*
|--------------------------------------------------------------------------
| SELLER COMPLIANCE MESSAGE
|--------------------------------------------------------------------------
*/

Route::post(
    '/seller/compliance/message',
    [SellerComplianceMessageController::class, 'store']
)->name('seller.compliance.message');


/*
|--------------------------------------------------------------------------
| SELLER ORDER / REPORT / MESSAGE / ACCOUNT PAGES
|--------------------------------------------------------------------------
*/

Route::get('/seller/orders', function (Request $request) {
    if (!$request->session()->get('is_seller')) {
        return redirect()->route('login');
    }

    return view('seller.orders');
})->name('seller.orders');


Route::get('/seller/reports', function (Request $request) {
    if (!$request->session()->get('is_seller')) {
        return redirect()->route('login');
    }

    return view('seller.reports');
})->name('seller.reports');


Route::get('/seller/messages', [SellerAdminChatController::class, 'sellerIndex'])
    ->name('seller.messages');

Route::post('/seller/messages', [SellerAdminChatController::class, 'sellerSend'])
    ->name('seller.messages.send');

Route::post('/seller/messages/read', [SellerAdminChatController::class, 'sellerRead'])
    ->name('seller.messages.read');


Route::get('/seller/account', function (Request $request) {
    if (!$request->session()->get('is_seller')) {
        return redirect()->route('login');
    }

    return view('seller.account');
})->name('seller.account');


/*
| Protected seller/admin chat attachment
*/
Route::get('/messages/attachments/{message}', [SellerAdminChatController::class, 'attachment'])
    ->name('chat.attachments.show');


Route::post('/seller/logout', function (Request $request) {
    $request->session()->forget([
        'is_seller',
        'seller_account_id',
    ]);

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('seller.logout');
