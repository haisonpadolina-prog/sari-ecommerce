<?php

use App\Http\Controllers\Admin\AdminSecurityCenterController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/security', [AdminSecurityCenterController::class, 'index'])
    ->name('admin.security');

Route::get('/admin/security/alerts/feed', [AdminSecurityCenterController::class, 'alertFeed'])
    ->name('admin.security.alert-feed');


Route::post('/admin/security/alerts/acknowledge', [AdminSecurityCenterController::class, 'acknowledge'])
    ->name('admin.security.alerts.acknowledge');

Route::post('/admin/security/sessions/{sessionHandle}/force-logout', [AdminSecurityCenterController::class, 'forceLogout'])
    ->where('sessionHandle', '[a-f0-9]{64}')
    ->name('admin.security.sessions.force-logout');
