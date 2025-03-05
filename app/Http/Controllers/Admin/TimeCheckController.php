<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TimeCheckService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TimeCheckController extends Controller
{
    public function index(Request $request, TimeCheckService $servise)
    {
        $date = $request->get('date') ?? Carbon::now()->format('Y-m-d');

        DB::enableQueryLog();
        
        $todayReport = $servise->getCurrentWorkTimeReport();
        $dateReport = $servise->getWorkTimeDayReport($date);

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // dd($queryCount);
        // dd($dateReport);
        

        return Inertia::render('Admin/TimeCheck/Index', [
            'todayReport' => $todayReport,
            'dateReport' => $dateReport,
            'date' => $date,
        ]);
    }
}
