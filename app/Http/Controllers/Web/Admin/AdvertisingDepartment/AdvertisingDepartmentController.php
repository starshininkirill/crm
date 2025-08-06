<?php

namespace App\Http\Controllers\Web\Admin\AdvertisingDepartment;

use App\Http\Controllers\Controller;
use App\Models\Contracts\ServiceMonth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdvertisingDepartmentController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/AdvertisingDepartment/Index', []);
    }

    public function report()
    {
        $currentDate = Carbon::now();
        $previousMonth = $currentDate->copy()->subMonth();

        $monthlyServices = ServiceMonth::with(['tarif', 'contract', 'payment', 'user'])
            ->where('type', ServiceMonth::TYPE_ADS)
            ->where(function ($query) use ($currentDate, $previousMonth) {
                $query->where(function ($q) use ($currentDate) {
                    $q->whereYear('start_service_date', $currentDate->year)
                        ->whereMonth('start_service_date', $currentDate->month);
                })->orWhere(function ($q) use ($previousMonth) {
                    $q->whereYear('start_service_date', $previousMonth->year)
                        ->whereMonth('start_service_date', $previousMonth->month);
                });
            })
            ->get()
            ->groupBy(function ($service) {
                return $service->start_service_date->format('Y-m');
            });

        return Inertia::render('Admin/AdvertisingDepartment/Report', [
            'previous_month' => [
                'period' => $previousMonth->format('Y-m'),
                'services' => $monthlyServices->get($previousMonth->format('Y-m'), collect())
            ],
            'current_month' => [
                'period' => $currentDate->format('Y-m'),
                'services' => $monthlyServices->get($currentDate->format('Y-m'), collect())
            ]
        ]);
    }
}
