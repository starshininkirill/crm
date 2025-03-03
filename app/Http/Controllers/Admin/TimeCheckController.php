<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\Business\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TimeCheckRequest;
use App\Services\TimeCheckService;
use Illuminate\Http\Request;

class TimeCheckController extends Controller
{
    public function makeAction(TimeCheckRequest $requst, TimeCheckService $service)
    {
        $action = $requst->validated()['action'];
        $user = auth()->user();

        if (!$user) {
            return new BusinessException('Пользователь не авторизован!');
        }

        $service->handleAction($action, $user);
    }
}
