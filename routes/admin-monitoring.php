<?php

use App\Http\Controllers\Admin\AdminPlatformMonitoringController;
use App\Http\Controllers\Admin\AdminUsersController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/monitoring', [AdminPlatformMonitoringController::class, 'index'])
    ->name('admin.monitoring');

Route::get('/admin/monitoring/sellers/{seller}', [AdminPlatformMonitoringController::class, 'seller'])
    ->whereNumber('seller')
    ->name('admin.monitoring.sellers.show');

Route::get('/admin/monitoring/logistics/{logistics}', [AdminPlatformMonitoringController::class, 'logistics'])
    ->whereNumber('logistics')
    ->name('admin.monitoring.logistics.show');

Route::get('/admin/monitoring/buyers/{role}/{id}', [AdminPlatformMonitoringController::class, 'buyer'])
    ->whereIn('role', ['buyer', 'social_buyer'])
    ->whereNumber('id')
    ->name('admin.monitoring.buyers.show');

Route::get('/admin/monitoring/riders/{rider}', [AdminPlatformMonitoringController::class, 'rider'])
    ->whereNumber('rider')
    ->name('admin.monitoring.riders.show');

Route::post('/admin/users/{role}/{id}/block', [AdminUsersController::class, 'block'])
    ->whereIn('role', ['buyer', 'seller', 'rider', 'logistics', 'social_buyer'])
    ->whereNumber('id')
    ->name('admin.users.block');

Route::post('/admin/users/{role}/{id}/unblock', [AdminUsersController::class, 'unblock'])
    ->whereIn('role', ['buyer', 'seller', 'rider', 'logistics', 'social_buyer'])
    ->whereNumber('id')
    ->name('admin.users.unblock');
