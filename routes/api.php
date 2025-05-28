<?php

use App\Http\Controllers\Api\DocumentGenerator\DocumentGeneratorController;
use Illuminate\Support\Facades\Route;

Route::post('/generate-document', [DocumentGeneratorController::class, 'generateDocument'])->name('api.generateDocument');


