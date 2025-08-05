<?php

use App\Http\Controllers\Web\Admin\AdvertisingDepartment\AdvertisingDepartmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('advertising-department')->group(function () {
    Route::get('/', [AdvertisingDepartmentController::class, 'index'])->name('admin.advertising-department.info');
});
