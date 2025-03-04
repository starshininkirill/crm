<?php 
use App\Http\Controllers\Main\MainController;
use Illuminate\Support\Facades\Route;


Route::get('/', [MainController::class, 'home'])->name('home');
Route::get('/fast-login', [MainController::class, 'loginHome'])->name('fastLogin');