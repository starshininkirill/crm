<?php 
use App\Http\Controllers\admin\SettingsController;

use Illuminate\Support\Facades\Route;

Route::prefix('settings')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('admin.settings.index');
    
    Route::get('/calendar', [SettingsController::class, 'calendar'])->name('admin.settings.calendar');
    Route::post('/calendar/change-day', [SettingsController::class, 'toggleWorkingDayType'])->name('admin.settings.calendar.change-day');
    
    Route::get('/finance-week', [SettingsController::class, 'financeWeek'])->name('admin.settings.finance-week');
    Route::post('/finance-week', [SettingsController::class, 'setWeeks'])->name('admin.settings.finance-week.set-weeks');

    Route::get('/time-check', [SettingsController::class, 'timeCheck'])->name('admin.settings.time-check');
}); 