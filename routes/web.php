<?php

use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;


Route::get('/', [MainController::class, 'home'])->name('home');



Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

});

Route::middleware('auth')->group(function () {

    Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');

 });



Route::prefix('admin')->middleware('role:admin')->group(function () {

    Route::get('/', [MainController::class, 'admin'])->name('admin');

    Route::prefix('contract')->group(function () {
        Route::get('', [ContractController::class, 'index'])->name('admin.contract.index');
        Route::get('/create', [ContractController::class, 'create'])->name('admin.contract.create');
        Route::post('/store', [ContractController::class, 'store'])->name('admin.contract.store');
        Route::get('/show/{id}', [ContractController::class, 'show'])->name('admin.contract.show');
    });

    Route::prefix('payment')->group(function () {
        Route::get('', [PaymentController::class, 'index'])->name('admin.payment.index');
        Route::get('/show/{id}', [PaymentController::class, 'show'])->name('admin.payment.show');
    });

    Route::prefix('service')->group(function () {
        Route::get('', [ServiceController::class, 'index'])->name('admin.service.index');
        Route::get('/create', [ServiceController::class, 'create'])->name('admin.service.create');
        Route::post('/store', [ServiceController::class, 'store'])->name('admin.service.store');
    });

    Route::prefix('department')->group(function () {
        Route::get('', [DepartmentController::class, 'index'])->name('admin.department.index');
    });


    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('admin.user.index');
        Route::get('/create', [UserController::class, 'create'])->name('admin.user.create');
        Route::post('/store', [UserController::class, 'store'])->name('admin.user.store');
    });

});
