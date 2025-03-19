<?php

use App\Http\Controllers\Admin\TimeCheckController;
use Illuminate\Support\Facades\Route;

Route::prefix('time-check')->group(function () {
    Route::post('/work-status', [TimeCheckController::class, 'handleWorkStatus'])->name('admin.time-check.handle-work-status');

    Route::get('/', [TimeCheckController::class, 'index'])->name('admin.time-check.index');
});