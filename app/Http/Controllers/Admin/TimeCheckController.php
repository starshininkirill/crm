<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TimeCheck\WorkStatusRequest;
use App\Models\WorkStatus;
use App\Services\TimeCheckServices\ReportService;
use App\Services\WorkStatusService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TimeCheckController extends Controller
{
    public function index(Request $request, ReportService $servise)
    {
        $date = $request->get('date') ?? Carbon::now()->format('Y-m-d');
        $workStatuses = WorkStatus::all();

        DB::enableQueryLog();

        $todayReport = $servise->getCurrentWorkTimeReport();
        $dateReport = $servise->getWorkTimeDayReport($date);
        $logReport = $servise->getLogReport($date);

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // dd($queryCount);
        // dd($logReport);


        return Inertia::render('Admin/TimeCheck/Index', [
            'todayReport' => $todayReport,
            'dateReport' => $dateReport,
            'logReport' => $logReport,
            'date' => $date,
            'workStatuses' => $workStatuses,
        ]);
    }

    public function handleWorkStatus(WorkStatusRequest $request, WorkStatusService $service)
    {
        $validated = $request->validated();

        $service->handleChange($validated);

        return response()->json([
            'success' => true,
            'message' => 'Статус успешно обновлен!',
        ], 200);
    }
}
