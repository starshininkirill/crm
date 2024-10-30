<?php

namespace App\Http\Controllers\admin;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\WorkingDay;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.layouts.settings');
    }

    public function calendar()
    {
        $year = 2024;
        $months = DateHelper::workingCalendar($year);

        $daysInstances = WorkingDay::whereYear('date', $year)->get();

        // dd(Carbon::now()->addDay(1)->format('Y-m-d'));

        // $date = WorkingDay::create([
        //     'date' => Carbon::now()->addDay(1),
        //     'is_working_day' => 0
        // ]);

        return view('admin.settings.calendar', ['months' => $months]);
    }
}
