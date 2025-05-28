<?php

use App\Http\Controllers\Web\Admin\DocumentGenerator\DocumentGeneratorController;
use Illuminate\Support\Facades\Route;

Route::prefix('document-generator')->group(function () {
    Route::get('', [DocumentGeneratorController::class, 'index'])->name('admin.document-generator.index');
    Route::post('', [DocumentGeneratorController::class, 'store'])->name('admin.document-generator.store');
    Route::delete('/{id}', [DocumentGeneratorController::class, 'destroy'])->name('admin.document-generator.destroy');
});