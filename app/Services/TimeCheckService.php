<?php

namespace App\Services;

use App\Exceptions\Business\BusinessException;
use App\Models\TimeCheck;
use App\Models\User;
use Carbon\Carbon;

class TimeCheckService
{
    public function handleAction(string $action, User $user)
    {
        if (!method_exists($this, $action)) {
            throw new BusinessException('Такого метода не существует');
        }

        if (!$this->$action($user)) {
            throw new BusinessException('Не удалось совершить действие');
        }

        return true;
    }

    private function start(User $user)
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

    private function pause(User $user)
    {
        $lastAction = $user->lastAction();

        if ($lastAction && $lastAction->action == TimeCheck::ACTION_PAUSE) {
            throw new BusinessException('Перерыв уже начат');
        }

        if (!$lastAction || ($lastAction->action != TimeCheck::ACTION_CONTINUE && $lastAction->action != TimeCheck::ACTION_START)) {
            throw new BusinessException('Вы не можете начать перерыв сейчас');
        }

        if (!$this->canGoBreak($user)) {
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

        return $this->registerAction($user, TimeCheck::ACTION_CONTINUE);
    }

    private function end(User $user)
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

    private function registerAction(User $user, $action)
    {
        return $isCreate = $user->timeChecks()->create([
            'date' => Carbon::now(),
            'action' => $action,
        ]);
    }

    private function canGoBreak(User $user)
    {
        return true;
    }
}
