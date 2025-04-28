<?php
use App\Http\Controllers\Admin\Service\ServiceCategoryController;
use App\Http\Controllers\Admin\Service\ServiceController;

use Illuminate\Support\Facades\Route;

Route::prefix('servicies')->group(function () {
    Route::prefix('categories')->group(function () {
        Route::get('/', [ServiceCategoryController::class, 'index'])->name('admin.service.category.index');
        Route::get('/{serviceCategory}/edit', [ServiceCategoryController::class, 'edit'])->name('admin.service.category.edit');

        Route::post('/', [ServiceCategoryController::class, 'store'])->name('admin.service-category.store');
        Route::patch('/{serviceCategory}', [ServiceCategoryController::class, 'update'])->name('admin.service-category.update');
        Route::delete('/{serviceCategory}', [ServiceCategoryController::class, 'destroy'])->name('admin.service-category.destroy');
    });

    Route::get('/{service}', [ServiceController::class, 'edit'])->name('admin.service.show');
    Route::get('/{service}/edit/', [ServiceController::class, 'edit'])->name('admin.service.edit');
    Route::get('/', [ServiceController::class, 'index'])->name('admin.service.index');

    Route::post('/', [ServiceController::class, 'store'])->name('admin.service.store');
    Route::patch('/{service}', [ServiceController::class, 'update'])->name('admin.service.update');
    Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('admin.service.destroy');
});