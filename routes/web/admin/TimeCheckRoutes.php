<?php

use App\Http\Controllers\Admin\TimeCheckController;
use Illuminate\Support\Facades\Route;

Route::prefix('time-check')->group(function () {
    Route::post('/work-status', [TimeCheckController::class, 'handleWorkStatus'])->name('admin.time-check.handle-work-status');

    Route::get('/time-sheet', [TimeCheckController::class, 'timeSheet'])->name('admin.time-sheet');

    Route::get('/overworks', [TimeCheckController::class, 'overwork'])->name('admin.time-check.overwork');

    Route::get('/', [TimeCheckController::class, 'index'])->name('admin.time-check.index');
});