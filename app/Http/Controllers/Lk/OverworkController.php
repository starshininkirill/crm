<?php

namespace App\Http\Controllers\Lk;

use App\Exceptions\Business\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lk\OverworkRequest;
use App\Models\DailyWorkStatus;
use App\Models\WorkStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OverworkController extends Controller
{
    public function create()
    {
        return Inertia::render('Lk/Overwork/Create');
    }

    public function store(OverworkRequest $request)
    {
        $validated = $request->validated();

        $overworkStatus = WorkStatus::overworkStatuses()->first();

        if(!$overworkStatus){
            throw new BusinessException('Статуса переработки не существует, обратитесь к програмисту!');
        };

        DailyWorkStatus::create([
            'date' => $validated['date'],
            'user_id' => auth()->user()->id,
            'work_status_id' => $overworkStatus->id,
            'status' => DailyWorkStatus::STATUS_PENDING,
            'hours' => $validated['hours'],
            'report' => $validated['report'],
            'links' => $validated['links'],
        ]);

        return redirect()->back()->with('success', 'Переработка успешно отправлена!');
    }
}
