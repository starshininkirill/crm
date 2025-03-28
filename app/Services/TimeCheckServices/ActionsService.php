<?php

namespace App\Services\TimeCheckServices;

use App\Events\StartWorkDay;
use App\Exceptions\Business\BusinessException;
use App\Models\TimeCheck;
use App\Models\User;
use Carbon\Carbon;

class ActionsService
{
    public function __construct(
        protected WorkTimeService $workTimeService,
    ) {}

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
        $lastAction = $user->getLastAction();

        if ($lastAction && $lastAction->action == TimeCheck::ACTION_START) {
            throw new BusinessException('День уже начат, либо не завершен предыдущий');
        }

        if ($lastAction && $lastAction->action == TimeCheck::ACTION_END) {
            throw new BusinessException('Рабочий день уже завершён');
        }


        $action = $this->registerAction($user, TimeCheck::ACTION_START);

        if(!$action){
            return false;
        }

        StartWorkDay::dispatch($action);

        return true;
    }

    private function pause(User $user): bool
    {
        $lastAction = $user->getLastAction();

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

        if($this->registerAction($user, TimeCheck::ACTION_PAUSE)){
            return true;
        }
        
        return false;
    }

    private function continue(User $user)
    {
        $lastAction = $user->getLastAction();

        if ($lastAction && $lastAction->action == TimeCheck::ACTION_CONTINUE) {
            throw new BusinessException('Перерыв уже завершен');
        }

        if ($lastAction && $lastAction->action != TimeCheck::ACTION_PAUSE) {
            throw new BusinessException('Куда тыкаешь');
        }

        if (!$this->registerAction($user, TimeCheck::ACTION_CONTINUE)) {
            throw new BusinessException('Не удалось завершить перерыв');
        }

        return $this->workTimeService->userBreaktime($user);
    }

    private function end(User $user)
    {
        $lastAction = $user->getLastAction();

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
        $isCreate = $user->timeChecks()->create([
            'date' => Carbon::now(),
            'action' => $action,
        ]);

        if ($isCreate) {
            return $isCreate;
        }

        return null;
    }

    private function canGoBreak(User $user): bool
    {
        // TODO
        // Можно ли на перерыв
        return true;
    }
}
