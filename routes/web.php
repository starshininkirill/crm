<?php

use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\MainController;
use App\Models\Departments\Department;
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

    Route::prefix('contracts')->group(function () {
        Route::get('', [ContractController::class, 'index'])->name('admin.contract.index');
        Route::get('/create', [ContractController::class, 'create'])->name('admin.contract.create');
        Route::post('/store', [ContractController::class, 'store'])->name('admin.contract.store');
        Route::get('/show/{contract}', [ContractController::class, 'show'])->name('admin.contract.show');
    });

    Route::prefix('payments')->group(function () {
        Route::get('', [PaymentController::class, 'index'])->name('admin.payment.index');
        Route::get('/unsettled', [PaymentController::class, 'unsettled'])->name('admin.payment.unsettled');
        Route::get('/show/{id}', [PaymentController::class, 'show'])->name('admin.payment.show');
    });

    Route::prefix('servicies')->group(function () {
        Route::get('', [ServiceController::class, 'index'])->name('admin.service.index');
        Route::get('/create', [ServiceController::class, 'create'])->name('admin.service.create');
        Route::post('/store', [ServiceController::class, 'store'])->name('admin.service.store');
        Route::prefix('categories')->group(function () {
            Route::get('/', [ServiceCategoryController::class, 'index'])->name('admin.service.category.index');
            Route::get('/create', [ServiceCategoryController::class, 'create'])->name('admin.service.category.create');
            Route::post('/store', [ServiceCategoryController::class, 'store'])->name('admin.service.category.store');
        });
    });

    Route::prefix('departments')->group(function () {
        Route::get('', [DepartmentController::class, 'index'])->name('admin.department.index');
        Route::get('/create', [DepartmentController::class, 'create'])->name('admin.department.create');
        Route::post('/store', [DepartmentController::class, 'store'])->name('admin.department.store');
        Route::get('/show/{department}', [DepartmentController::class, 'show'])->name('admin.department.show');

        Route::prefix('position')->group(function () {
            Route::get('/create', [PositionController::class, 'create'])->name('admin.department.position.create');
            Route::post('/store', [PositionController::class, 'store'])->name('admin.department.position.store');
        });

    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('admin.user.index');
        Route::get('/create', [UserController::class, 'create'])->name('admin.user.create');
        Route::post('/store', [UserController::class, 'store'])->name('admin.user.store');
        Route::get('/show/{user}', [UserController::class, 'show'])->name('admin.user.show');
    });

});

