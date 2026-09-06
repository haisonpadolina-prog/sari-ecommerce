<?php

/*
|--------------------------------------------------------------------------
| COURIER PAGE ROUTES — ADD ONLY THESE TO routes/web.php
|--------------------------------------------------------------------------
|
| Keep all existing Admin, Seller, Product, Compliance, Chat,
| Courier Dashboard, Courier Delivery Process, and Logout routes unchanged.
|
*/

use App\Http\Controllers\CourierPageController;

Route::get(
    '/courier/requests',
    [CourierPageController::class, 'requests']
)->name('courier.requests');

Route::get(
    '/courier/pickups',
    [CourierPageController::class, 'pickups']
)->name('courier.pickups');

Route::get(
    '/courier/deliveries',
    [CourierPageController::class, 'deliveries']
)->name('courier.deliveries');

Route::get(
    '/courier/earnings',
    [CourierPageController::class, 'earnings']
)->name('courier.earnings');

Route::get(
    '/courier/history',
    [CourierPageController::class, 'history']
)->name('courier.history');

Route::get(
    '/courier/messages',
    [CourierPageController::class, 'messages']
)->name('courier.messages');

Route::get(
    '/courier/profile',
    [CourierPageController::class, 'profile']
)->name('courier.profile');
