<?php

use App\Http\Controllers\Api\DocumentGenerator\DocumentGeneratorController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DocumentGeneratorController::class, 'test'])->name('api.test');
Route::post('/generate-document', [DocumentGeneratorController::class, 'generateDocument'])->name('api.generateDocument');
