<?php

use App\Http\Controllers\Lk\ContractGeneratorController;
use App\Http\Controllers\Lk\MainController;
use App\Http\Controllers\Lk\ActGeneratorController;
use App\Http\Controllers\Lk\OverworkController;
use App\Http\Controllers\Lk\SbpGeneratorController;


use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('/lk')->group(function () {
    Route::get('/', [MainController::class, 'index'])->name('lk');

    Route::prefix('/contracts')->group(function () {
        Route::get('/create', [ContractGeneratorController::class, 'create'])->name('lk.contract.create');
        Route::post('/store', [ContractGeneratorController::class, 'store'])->name('lk.contract.store');
    });

    Route::prefix('/acts')->group(function () {
        Route::get('/create', [ActGeneratorController::class, 'create'])->name('lk.act.create');
        Route::post('/store', [ActGeneratorController::class, 'store'])->name('lk.act.store');
    });

    Route::prefix('/sbp')->group(function () {
        Route::get('/create', [SbpGeneratorController::class, 'create'])->name('lk.sbp.create');
        Route::post('/store', [SbpGeneratorController::class, 'store'])->name('lk.sbp.store');
    });

    Route::prefix('/overworks')->group(function () {
        Route::get('/create', [OverworkController::class, 'create'])->name('lk.overwork.create');
        Route::post('/store', [OverworkController::class, 'store'])->name('lk.overwork.store');
    });
});
