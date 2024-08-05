<?php

use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;


Route::get('/', [MainController::class, 'home'])->name('home');

Route::prefix('admin')->group(function () {
    
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

    Route::prefix('user')->group(function () {
        Route::get('', [UserController::class, 'index'])->name('admin.user.index');
    });

});
