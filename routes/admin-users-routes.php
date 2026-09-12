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
    ->whereIn('role', ['buyer', 'seller', 'rider', 'logistics'])
    ->whereNumber('id')
    ->name('admin.users.suspend');

Route::post('/admin/users/{role}/{id}/restore', [AdminUsersController::class, 'restore'])
    ->whereIn('role', ['buyer', 'seller', 'rider', 'logistics'])
    ->whereNumber('id')
    ->name('admin.users.restore');

Route::post('/admin/users/{role}/{id}/note', [AdminUsersController::class, 'note'])
    ->whereIn('role', ['buyer', 'seller', 'rider', 'logistics'])
    ->whereNumber('id')
    ->name('admin.users.note');
