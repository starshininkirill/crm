<?php

use App\Http\Controllers\ContractController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;


Route::get('/', [TestController::class, 'index'])->name('home');

Route::prefix('contract')->group(function(){
    Route::get('', [ContractController::class, 'index'])->name('contract.index');
    Route::get('/create', [ContractController::class, 'create'])->name('contract.create');
    Route::post('/store', [ContractController::class, 'store'])->name('contract.store');
});