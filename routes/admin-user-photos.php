<?php

use App\Http\Controllers\Admin\AdminUsersController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->get('/admin/users/{role}/{id}/photo', [AdminUsersController::class, 'photo'])
    ->whereIn('role', ['buyer', 'seller', 'rider', 'logistics'])
    ->whereNumber('id')
    ->name('admin.users.photo');
