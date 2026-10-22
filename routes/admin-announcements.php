<?php

use App\Http\Controllers\Admin\AdminPlatformAnnouncementController;
use App\Http\Controllers\Shared\PlatformAnnouncementReadController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/announcements', [AdminPlatformAnnouncementController::class, 'index'])
    ->name('admin.announcements');
Route::post('/admin/announcements', [AdminPlatformAnnouncementController::class, 'store'])
    ->name('admin.announcements.store');
Route::patch('/admin/announcements/{announcement}', [AdminPlatformAnnouncementController::class, 'update'])
    ->name('admin.announcements.update');
Route::post('/admin/announcements/{announcement}/publish', [AdminPlatformAnnouncementController::class, 'publish'])
    ->name('admin.announcements.publish');
Route::post('/admin/announcements/{announcement}/pause', [AdminPlatformAnnouncementController::class, 'pause'])
    ->name('admin.announcements.pause');
Route::delete('/admin/announcements/{announcement}', [AdminPlatformAnnouncementController::class, 'destroy'])
    ->name('admin.announcements.destroy');

Route::post(
    '/platform/announcements/{announcement}/read',
    PlatformAnnouncementReadController::class
)->name('platform-announcements.read');
