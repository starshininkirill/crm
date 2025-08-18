<?php

use App\Http\Controllers\Web\Admin\Staff\PositionController;
use App\Http\Controllers\Web\Admin\Staff\EmploymentTypeController;
use App\Http\Controllers\Web\Admin\Staff\UserController;
use App\Http\Controllers\Web\Admin\Staff\OverworkController;
use App\Http\Controllers\Web\Admin\Staff\TimeSheetController;
use App\Http\Controllers\Web\Admin\Staff\TimeCheckController;
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

    Route::prefix('time-sheet')->group(function () {
        Route::get('/', [TimeSheetController::class, 'index'])->name('admin.time-sheet');
        Route::get('/export-salary', [TimeSheetController::class, 'exportSalary'])->name('admin.time-sheet.export-payments');
        Route::post('/user-adjustment', [TimeSheetController::class, 'userAdjustmentStore'])->name('admin.time-sheet.user-adjustment.store');
        Route::delete('/user-adjustment/{adjustment}', [TimeSheetController::class, 'userAdjustmentDestroy'])->name('admin.time-sheet.user-adjustment.destroy');
        Route::post('/note', [TimeSheetController::class, 'storeNote'])->name('admin.time-sheet.note.store');
        Route::delete('/note/{note}', [TimeSheetController::class, 'destroyNote'])->name('admin.time-sheet.note.destroy');

        Route::post('/schedule-salary-update', [TimeSheetController::class, 'scheduleSalaryUpdate'])->name('admin.time-sheet.schedule-salary-update.store');
        Route::delete('/schedule-salary-update/{scheduledUpdate}', [TimeSheetController::class, 'destroyScheduledSalaryUpdate'])->name('admin.time-sheet.schedule-salary-update.destroy');
    });


    Route::prefix('/overworks')->group(function () {
        Route::get('/', [OverworkController::class, 'index'])->name('admin.overwork');
        Route::post('/{overwork}/acept', [OverworkController::class, 'accept'])->name('admin.overwork.accept');
        Route::post('/{overwork}/reject', [OverworkController::class, 'reject'])->name('admin.overwork.reject');
    });

    Route::prefix('time-check')->group(function () {
        Route::post('/work-status', [TimeCheckController::class, 'handleWorkStatus'])->name('admin.time-check.handle-work-status');
        Route::post('/sick-leave', [TimeCheckController::class, 'handleMassUpdate'])->name('admin.time-check.handle-mass-update');
        Route::post('/close-sick-leave', [TimeCheckController::class, 'closeSickLeave'])->name('admin.time-check.close-sick-leave');
        Route::post('/reject-late', [TimeCheckController::class, 'rejectLate'])->name('admin.time-check.reject-late');

        Route::get('/', [TimeCheckController::class, 'index'])->name('admin.time-check.index');
    });

    Route::get('/', [UserController::class, 'index'])->name('admin.user.index');
    Route::get('/{user}', [UserController::class, 'show'])->name('admin.user.show');
    Route::post('/{user}/fire', [UserController::class, 'fire'])->name('admin.user.fire');
    Route::post('/', [UserController::class, 'store'])->name('admin.user.store');
});
