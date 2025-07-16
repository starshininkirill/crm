<?php

namespace App\Console\Commands;

use App\Models\UserManagement\ScheduledUpdate;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ApplyScheduledUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:apply-scheduled-updates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновляет зарплату сотрудников в соответствии с запланированными изменениями';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $updates = ScheduledUpdate::where('status', ScheduledUpdate::STATUS_PENDING)
            ->whereDate('effective_date', '<=', Carbon::now())
            ->get();

        foreach ($updates as $update) {
            Log::info("Обновления: {$update}");
            try {
                $model = $update->updatable;

                if (!$model) {
                    $this->error("Не удалось найти модель для обновления: {$update->updatable_id} с типом: {$update->updatable_type}");
                    continue;
                }

                if (!array_key_exists($update->field, $model->getAttributes())) {
                    $this->error("Поле {$update->field} не существует в модели {$update->updatable_type}");
                    continue;
                }

                $field = $update->field;
                $model->$field = $update->new_value;
                $model->save();

                Log::info("Обновлено: {$model->id} {$model}");

                $update->status = ScheduledUpdate::STATUS_APPLIED;
                $update->save();
                Log::info("Обновлено: {$update->id} {$update}");
            } catch (\Throwable $e) {

                Log::info("Не удалось применить обновление: {$update->id}. Ошибка: {$e->getMessage()}");
                $update->status = ScheduledUpdate::STATUS_FAILED;
                $update->save();

                $this->error("Не удалось применить обновление: {$update->id}. Ошибка: {$e->getMessage()}");
                continue;
            }
        }
    }
}
