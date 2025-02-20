<?php

namespace App\Http\Controllers;

use App\Exceptions\Buisness\BuisnessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkingDayRequest;
use App\Models\WorkingDay;


class WorkingDayController extends Controller
{
    public function toggleType(WorkingDayRequest $request)
    {
        $date = $request->validated();

        $updatedDay = WorkingDay::updateOrCreate(
            ['date' => $date['date']],
            ['is_working_day' => !$date['is_working_day']]
        );

        return response()->json($updatedDay);
    }
}
