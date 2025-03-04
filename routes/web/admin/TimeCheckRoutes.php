<?php

use App\Http\Controllers\Admin\TimeCheckController;
use Illuminate\Support\Facades\Route;

Route::prefix('time-check')->group(function () {
    Route::get('/', [TimeCheckController::class, 'index'])->name('admin.time-check.index');
});