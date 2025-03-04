<?php 
use App\Http\Controllers\Admin\Departments\SaleDepartmentController;

use Illuminate\Support\Facades\Route;

Route::prefix('sale-department')->group(function () {
    Route::get('/', [SaleDepartmentController::class, 'index'])->name('admin.sale-department.index');

    Route::get('/calls', [SaleDepartmentController::class, 'callsReport'])->name('admin.sale-department.calls');
    Route::get('/user-report', [SaleDepartmentController::class, 'userReport'])->name('admin.sale-department.user-report');

    Route::get('/plans-settings', [SaleDepartmentController::class, 'plansSettings'])->name('admin.sale-department.plans-settings');
    Route::put('/{workPlan}', [SaleDepartmentController::class, 'updateWorkPlan'])->name('admin.sale-department.work-plan.update');
    Route::delete('/{workPlan}', [SaleDepartmentController::class, 'destroyWorkPlan'])->name('admin.sale-department.work-plan.destroy');
    Route::post('/', [SaleDepartmentController::class, 'storeWorkPlan'])->name('admin.sale-department.work-plan.store');

    Route::get('/t2-settings', [SaleDepartmentController::class, 't2Settings'])->name('admin.sale-department.t2-settings');
    Route::get('/t2-load-data', [SaleDepartmentController::class, 't2LoadData'])->name('admin.sale-department.t2-load-data');
});