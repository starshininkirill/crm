<?php

namespace App\Http\Controllers\Global;

use App\Exceptions\Business\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Global\TimeCheckRequest;
use App\Services\TimeCheckService;


class TimeCheckController extends Controller
{
    public function makeAction(TimeCheckRequest $requst, TimeCheckService $service)
    {
        $action = $requst->validated()['action'];
        $user = auth()->user();

        if (!$user) {
            return new BusinessException('Пользователь не авторизован!');
        }

        $secondsOrBool = $service->handleAction($action, $user);

        return response()->json([
            'seconds' => $secondsOrBool
        ], 200);
    }

    public function userBreaktime(TimeCheckService $service)
    {
        $user = auth()->user();
        $breaktime = $service->userBreaktime($user);

        return response()->json([
            'breaktime' => $breaktime,
        ], 200);
    }
}
