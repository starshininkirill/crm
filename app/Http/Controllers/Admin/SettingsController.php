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

    public function calendar(Request $request)
    {
        $requestDate = $request->query('date');

        if ($requestDate && DateHelper::isValidYearMonth($requestDate)) {
            $date = Carbon::createFromDate($requestDate);
        } else {
            $date = Carbon::now();
        }


        $months = DateHelper::workingCalendar($date->format('Y'));

        return view('admin.settings.calendar', ['months' => $months, 'date' => $date ]);
    }
}
