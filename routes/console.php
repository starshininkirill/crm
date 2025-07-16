<?php

use App\Console\Commands\ApplyScheduledUpdates;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

// Обновление зарплат сотрудникам
Schedule::command(ApplyScheduledUpdates::class)->everyMinut();
