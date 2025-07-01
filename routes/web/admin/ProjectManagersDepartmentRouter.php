<?php

use App\Http\Controllers\Web\Admin\ProjectManagersDepartment\ProjectManagersDepartmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('projects-department')->group(function () {
    Route::get('/report', [ProjectManagersDepartmentController::class, 'report'])->name('admin.projects-department.report');
    Route::get('/', [ProjectManagersDepartmentController::class, 'index'])->name('admin.projects-department.index');
});
