/*
|--------------------------------------------------------------------------
| BUYER — SINGLE ORDER DETAILS
|--------------------------------------------------------------------------
| PLACE THIS IMMEDIATELY AFTER the existing buyer.orders GET route:
|
| Route::get('/buyer/orders', [BuyerPageController::class, 'orders'])
|     ->name('buyer.orders');
|--------------------------------------------------------------------------
*/

Route::get(
    '/buyer/orders/{order}',
    \App\Http\Controllers\BuyerOrderDetailsController::class
)
    ->whereNumber('order')
    ->name('buyer.orders.show');
