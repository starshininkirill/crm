<?php 
use App\Http\Controllers\Admin\User\PositionController;
use App\Http\Controllers\Admin\User\EmploymentTypeController;
use App\Http\Controllers\Admin\User\UserController;

use Illuminate\Support\Facades\Route;


Route::prefix('users')->group(function () {

    Route::prefix('employment-types')->group(function () {
        Route::get('/', [EmploymentTypeController::class, 'index'])->name('admin.employment-type.index');
        Route::post('/', [EmploymentTypeController::class, 'store'])->name('admin.employment-type.store');
        Route::delete('/{employmentType}', [EmploymentTypeController::class, 'destroy'])->name('admin.employment-type.destroy');
    });

    Route::prefix('positions')->group(function () {
        Route::get('/', [PositionController::class, 'index'])->name('admin.position.index');
        Route::post('/', [PositionController::class, 'store'])->name('admin.position.store');
    });

    Route::get('/', [UserController::class, 'index'])->name('admin.user.index');
    Route::get('/{user}', [UserController::class, 'show'])->name('admin.user.show');
    Route::post('/{user}/fire', [UserController::class, 'fire'])->name('admin.user.fire');
    Route::post('/', [UserController::class, 'store'])->name('admin.user.store');
});