<?php

namespace App\Http\Controllers\Admin\TimeCheck;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TimeCheck\WorkStatusRequest;
use App\Models\WorkStatus;
use App\Services\TimeCheckServices\ReportService;
use App\Services\WorkStatusService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TimeCheckController extends Controller
{
    public function index(Request $request, ReportService $servise)
    {
        $date = $request->get('date') ?? Carbon::now()->format('Y-m-d');
        $workStatuses = WorkStatus::mainStatuses()->get();

        $todayReport = $servise->getWorkTimeDayReport($date);
        $logReport = $servise->getLogReport($date);

        return Inertia::render('Admin/TimeCheck/Index', [
            'todayReport' => $todayReport,
            'logReport' => $logReport,
            'date' => $date,
            'workStatuses' => $workStatuses,
        ]);
    }

    public function handleWorkStatus(WorkStatusRequest $request, WorkStatusService $service)
    {
        $validated = $request->validated();

        $service->handleChange($validated);

        return redirect()->back()->with('success', 'Статус успешно обновлен!');
    }

    public function handleSickLeave(WorkStatusRequest $request, WorkStatusService $service)
    {
        $validated = $request->validated();

        $service->handleSickLeave($validated);

        return redirect()->back()->with('success', 'Больничный успешно проставлен!');
    }

    public function closeSickLeave(WorkStatusRequest $request, WorkStatusService $service)
    {
        $validated = $request->validated();
        $validated['image'] = $request->file('image');

        $service->closeSickLeave($validated);

        return redirect()->back()->with('success', 'Больничный успешно закрыт!');
    }

    public function rejectLate(WorkStatusRequest $request, WorkStatusService $service)
    {
        $validated = $request->validated();

        $service->rejectLate($validated['user_id'], $validated['date']);

        return redirect()->back()->with('success', 'Опоздание успешно отменено!');
    }
}
