<?php

use App\Http\Controllers\Admin\AdminPlatformCampaignController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/promotions', [AdminPlatformCampaignController::class, 'index'])
    ->name('admin.platform-campaigns');
Route::post('/admin/promotions', [AdminPlatformCampaignController::class, 'store'])
    ->name('admin.platform-campaigns.store');
Route::patch('/admin/promotions/{campaign}', [AdminPlatformCampaignController::class, 'update'])
    ->name('admin.platform-campaigns.update');
Route::post('/admin/promotions/{campaign}/publish', [AdminPlatformCampaignController::class, 'publish'])
    ->name('admin.platform-campaigns.publish');
Route::post('/admin/promotions/{campaign}/pause', [AdminPlatformCampaignController::class, 'pause'])
    ->name('admin.platform-campaigns.pause');
Route::delete('/admin/promotions/{campaign}', [AdminPlatformCampaignController::class, 'destroy'])
    ->name('admin.platform-campaigns.destroy');
