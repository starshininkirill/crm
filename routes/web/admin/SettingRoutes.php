<?php 
use App\Http\Controllers\Web\Admin\SettingsController;

use Illuminate\Support\Facades\Route;

Route::prefix('settings')->group(function () {
    
    Route::get('/calendar', [SettingsController::class, 'calendar'])->name('admin.settings.calendar');
    Route::post('/calendar/change-day', [SettingsController::class, 'toggleWorkingDayType'])->name('admin.settings.calendar.change-day');
    
    Route::get('/finance-week', [SettingsController::class, 'financeWeek'])->name('admin.settings.finance-week');
    Route::post('/finance-week', [SettingsController::class, 'setWeeks'])->name('admin.settings.finance-week.set-weeks');

    Route::get('/staff', [SettingsController::class, 'staff'])->name('admin.settings.staff');

    Route::get('/', [SettingsController::class, 'index'])->name('admin.settings.main');
}); 