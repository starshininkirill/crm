<?php

use App\Http\Controllers\Admin\TimeCheck\OverworkController;
use App\Http\Controllers\Admin\TimeCheck\TimeSheetController;
use App\Http\Controllers\Admin\TimeCheck\TimeCheckController;
use Illuminate\Support\Facades\Route;

Route::prefix('time-check')->group(function () {
    Route::get('/time-sheet', [TimeSheetController::class, 'index'])->name('admin.time-sheet');
    
    Route::prefix('/overworks')->group(function(){
        Route::get('/', [OverworkController::class, 'index'])->name('admin.time-check.overwork');
        Route::post('/{overwork}/acept', [OverworkController::class, 'accept'])->name('admin.time-check.overwork.accept');
        Route::post('/{overwork}/reject', [OverworkController::class, 'reject'])->name('admin.time-check.overwork.reject');
    });

    Route::post('/work-status', [TimeCheckController::class, 'handleWorkStatus'])->name('admin.time-check.handle-work-status');

    Route::get('/', [TimeCheckController::class, 'index'])->name('admin.time-check.index');
});