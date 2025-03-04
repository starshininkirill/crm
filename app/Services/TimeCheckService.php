<?php

namespace App\Services;

use App\Exceptions\Business\BusinessException;
use App\Models\TimeCheck;
use App\Models\User;
use Carbon\Carbon;

class TimeCheckService
{
    public function userBreaktime(User $user, Carbon $date = null)
    {
        if (!$date) {
            $date = Carbon::now();
        }

        $breaktimes = $user->timeChecks()
            ->whereIn('action', [TimeCheck::ACTION_CONTINUE, TimeCheck::ACTION_PAUSE])
            ->whereDate('date', $date)
            ->orderBy('date')
            ->get();

        $totalBreakTime = 0;
        $pauseStart = null;

        foreach ($breaktimes as $timeCheck) {
            if ($timeCheck->action === TimeCheck::ACTION_PAUSE) {
                $pauseStart = Carbon::parse($timeCheck->date);
            } elseif ($timeCheck->action === TimeCheck::ACTION_CONTINUE && $pauseStart) {
                $continueTime = Carbon::parse($timeCheck->date);
                $totalBreakTime += $pauseStart->diffInSeconds($continueTime);
                $pauseStart = null;
            }
        }

        if ($pauseStart) {
            $totalBreakTime += $pauseStart->diffInSeconds(Carbon::now());
        }

        return $totalBreakTime;
    }

    public function handleAction(string $action, User $user)
    {
        if (!method_exists($this, $action)) {
            throw new BusinessException('Такого метода не существует');
        }

        $secondsOrBool = $this->$action($user);

        if (!$secondsOrBool) {
            throw new BusinessException('Не удалось совершить действие');
        }

        return $secondsOrBool;
    }

    private function start(User $user): bool
    {
        $lastAction = $user->lastAction();

        if ($lastAction && $lastAction->action == TimeCheck::ACTION_START) {
            throw new BusinessException('День уже начат, либо не завершен предыдущий');
        }

        if ($lastAction && $lastAction->action == TimeCheck::ACTION_END) {
            throw new BusinessException('Рабочий день уже завершён');
        }

        return $this->registerAction($user, TimeCheck::ACTION_START);
    }

    private function pause(User $user): bool
    {
        $lastAction = $user->lastAction();

        if ($lastAction && $lastAction->action == TimeCheck::ACTION_PAUSE) {
            throw new BusinessException('Перерыв уже начат');
        }

        if (!$lastAction || ($lastAction->action != TimeCheck::ACTION_CONTINUE && $lastAction->action != TimeCheck::ACTION_START)) {
            throw new BusinessException('Вы не можете начать перерыв сейчас');
        }

        if (!$this->canGoBreak($user)) {
            // TODO
            // Стучим на него Ивану
            throw new BusinessException('Сейчас на перерыв нельзя');
        }

        return $this->registerAction($user, TimeCheck::ACTION_PAUSE);
    }

    private function continue(User $user)
    {
        $lastAction = $user->lastAction();

        if ($lastAction && $lastAction->action == TimeCheck::ACTION_CONTINUE) {
            throw new BusinessException('Перерыв уже завершен');
        }

        if ($lastAction && $lastAction->action != TimeCheck::ACTION_PAUSE) {
            throw new BusinessException('Куда тыкаешь');
        }

        if (!$this->registerAction($user, TimeCheck::ACTION_CONTINUE)) {
            throw new BusinessException('Не удалось завершить перерыв');
        }

        return $this->userBreaktime($user);
    }

    private function end(User $user): bool
    {
        $lastAction = $user->lastAction();

        if ($lastAction && $lastAction->action == TimeCheck::ACTION_END) {
            throw new BusinessException('День уже завершен');
        }

        if ($lastAction && $lastAction->action == TimeCheck::ACTION_PAUSE) {
            throw new BusinessException('Сначала нужно завершить перерыв');
        }

        return $this->registerAction($user, TimeCheck::ACTION_END);
    }

    private function registerAction(User $user, $action): bool
    {
        $isCreate = $user->timeChecks()->create([
            'date' => Carbon::now(),
            'action' => $action,
        ]);

        if ($isCreate) {
            return true;
        }

        return false;
    }

    private function canGoBreak(User $user): bool
    {
        // TODO
        // Можно ли на перерыв
        return true;
    }
}
