<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| سایت عمومی
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| ورود ادمین (فقط مهمان) — ثبت‌نام عمومی وجود ندارد
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:10,1')->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| پنل مدیریت (فقط ادمین)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin', 'throttle:admin'])
    ->group(function () {
        Route::redirect('/', '/admin/products')->name('dashboard');

        Route::resource('products', ProductController::class)->except('show');
        Route::resource('categories', CategoryController::class)->except('show');

        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

        // مدیریت حساب‌های ادمین (ساخت، ویرایش، تغییر رمز، حذف)
        Route::resource('users', UserController::class)->except('show');

        Route::get('export', ExportController::class)->name('export');
        Route::view('help', 'admin.help')->name('help');
    });
