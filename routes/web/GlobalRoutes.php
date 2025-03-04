<?php

use App\Http\Controllers\Global\TimeCheckController;
use App\Http\Controllers\Global\OptionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::post('/time-check', [TimeCheckController::class, 'makeAction'])->name('time-check.action');
    Route::post('/breaktime', [TimeCheckController::class, 'userBreaktime'])->name('time-check.breaktime');

    Route::middleware('role:admin')->group(function () {

        Route::prefix('option')->name('option')->group(function () {
            Route::post('/', [OptionController::class, 'store'])->name('.store');
            Route::patch('/{option}', [OptionController::class, 'update'])->name('.update');
            Route::put('/{option}', [OptionController::class, 'update'])->name('.update');
            Route::delete('/{option}', [OptionController::class, 'destroy'])->name('.destroy');
            Route::post('mass-update', [OptionController::class, 'massUpdate'])->name('.option.mass-update');
        });

    });
});
