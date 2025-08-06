<?php

use App\Http\Controllers\Web\Admin\AdvertisingDepartment\AdvertisingDepartmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('advertising-department')->group(function () {

    Route::get('/report', [AdvertisingDepartmentController::class, 'report'])->name('admin.advertising-department.report');

    Route::get('/', [AdvertisingDepartmentController::class, 'index'])->name('admin.advertising-department.info');
});
