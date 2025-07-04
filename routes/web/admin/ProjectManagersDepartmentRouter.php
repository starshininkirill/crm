<?php

use App\Http\Controllers\Web\Admin\ProjectManagersDepartment\ProjectManagersDepartmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('projects-department')->group(function () {
    Route::get('/report', [ProjectManagersDepartmentController::class, 'report'])->name('admin.projects-department.report');
    Route::get('/plans-settings', [ProjectManagersDepartmentController::class, 'plansSettings'])->name('admin.projects-department.plans-settings');

    Route::patch('/{workPlan}', [ProjectManagersDepartmentController::class, 'updateWorkPlan'])->name('admin.projects-department.work-plan.update');
    Route::put('/{workPlan}', [ProjectManagersDepartmentController::class, 'updateWorkPlan'])->name('admin.projects-department.work-plan.update');
    Route::delete('/{workPlan}', [ProjectManagersDepartmentController::class, 'destroyWorkPlan'])->name('admin.projects-department.work-plan.destroy');
    Route::post('/', [ProjectManagersDepartmentController::class, 'storeWorkPlan'])->name('admin.projects-department.work-plan.store');

    Route::get('/', [ProjectManagersDepartmentController::class, 'index'])->name('admin.projects-department.index');
});
