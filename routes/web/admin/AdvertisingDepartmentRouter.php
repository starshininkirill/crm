<?php

use App\Http\Controllers\Web\Admin\AdvertisingDepartment\AdvertisingDepartmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('advertising-department')->group(function () {

    Route::get('/report', [AdvertisingDepartmentController::class, 'report'])->name('admin.advertising-department.report');
    Route::get('/plans-settings', [AdvertisingDepartmentController::class, 'plansSettings'])->name('admin.advertising-department.plansSettings');
    
    Route::patch('/{workPlan}', [AdvertisingDepartmentController::class, 'updateWorkPlan'])->name('admin.advertising-department.work-plan.update');
    Route::put('/{workPlan}', [AdvertisingDepartmentController::class, 'updateWorkPlan'])->name('admin.advertising-department.work-plan.update');
    Route::delete('/{workPlan}', [AdvertisingDepartmentController::class, 'destroyWorkPlan'])->name('admin.advertising-department.work-plan.destroy');
    Route::post('/', [AdvertisingDepartmentController::class, 'storeWorkPlan'])->name('admin.advertising-department.work-plan.store');

    Route::get('/', [AdvertisingDepartmentController::class, 'index'])->name('admin.advertising-department.info');
});
