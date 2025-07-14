<?php

use App\Http\Controllers\Web\Admin\ContractController;
use Illuminate\Support\Facades\Route;

Route::prefix('contracts')->group(function () {
    Route::get('', [ContractController::class, 'index'])->name('admin.contract.index');
    Route::get('/unallocated-contracts', [ContractController::class, 'unallocatedContracts'])->name('admin.unallocated-contracts');

    Route::post('/{contract}/attach-performers', [ContractController::class, 'attachPerformers'])->name('admin.contract.attach-performer');
    Route::get('/{contract}', [ContractController::class, 'show'])->name('admin.contract.show');
});
