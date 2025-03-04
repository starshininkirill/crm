<?php 
use App\Http\Controllers\Admin\DepartmentController;

use Illuminate\Support\Facades\Route;

Route::prefix('departments')->group(function () {
    Route::get('', [DepartmentController::class, 'index'])->name('admin.department.index');
    Route::get('/create', [DepartmentController::class, 'create'])->name('admin.department.create');
    Route::get('/{department}', [DepartmentController::class, 'show'])->name('admin.department.show');
});