<?php

namespace App\Http\Controllers\admin;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\FinanceWeek;
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

        $date = DateHelper::getValidatedDateOrNow($requestDate);

        $months = DateHelper::workingCalendar($date->format('Y'));

        return view('admin.settings.calendar', ['months' => $months, 'date' => $date]);
    }

    public function financeWeek(Request $request)
    {
        $requestDate = $request->query('date');

        $date = DateHelper::getValidatedDateOrNow($requestDate);

        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        $financeWeeks = FinanceWeek::where('date_start' , '>=',  $startOfMonth)
            ->where('date_end', '<=', $endOfMonth)
            ->get();
            
        return view('admin.settings.financeWeek', [
            'date' => $date,
            'financeWeeks' => $financeWeeks ?? collect()
        ]);
    }
}
