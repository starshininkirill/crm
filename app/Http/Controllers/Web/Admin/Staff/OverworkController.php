<?php

namespace App\Http\Controllers\Web\Admin\Staff;

use App\Exceptions\Business\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Staff\OverworkRequest;
use App\Models\TimeTracking\DailyWorkStatus;
use App\Models\TimeTracking\WorkStatus;
use Inertia\Inertia;

class OverworkController extends Controller
{
    public function index()
    {
        $overworks = DailyWorkStatus::with('user')
            ->where('status', DailyWorkStatus::STATUS_PENDING)
            ->whereHas('workStatus', function ($query) {
                $query->where('type', WorkStatus::TYPE_OVERWORK);
            })->get();

        return Inertia::render('Admin/Staff/Overwork/Index', [
            'overworks' => $overworks,
        ]);
    }

    public function accept(OverworkRequest $request, DailyWorkStatus $overwork)
    {
        $validated = $request->validated();

        $overwork->description = $validated['description'];
        $overwork->status = DailyWorkStatus::STATUS_APPROVED;

        if (!$overwork->save()) {
            throw new BusinessException('Не удалось подтвердить переработку');
        }

        return redirect()->back()->with('success', 'Переработка одобрена');
    }

    public function reject(OverworkRequest $request, DailyWorkStatus $overwork)
    {
        $validated = $request->validated();

        $overwork->description = $validated['description'];
        $overwork->status = DailyWorkStatus::STATUS_REJECTED;

        if (!$overwork->save()) {
            throw new BusinessException('Не удалось отклонить переработку');
        }

        return redirect()->back()->with('success', 'Переработка отклонена');
    }
}
