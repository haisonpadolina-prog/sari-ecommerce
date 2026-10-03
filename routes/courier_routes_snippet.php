<?php

use App\Http\Controllers\Courier\CourierDashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('courier')->name('courier.')->group(function () {
    Route::get('/dashboard', [CourierDashboardController::class, 'index'])
        ->name('dashboard');
});
