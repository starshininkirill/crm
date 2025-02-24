<?php

namespace App\Http\Controllers\admin;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WorkingDayRequest;
use App\Models\FinanceWeek;
use App\Models\Option;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\WorkingDay;
use Illuminate\Http\Request; 
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::all();
        $serviceCats = ServiceCategory::all();
        $mainCategoriesOption = Option::where('name', 'contract_generator_main_categories')->first();
        $secondaryCategoriesOption = Option::where('name', 'contract_generator_secondary_categories')->first();
        $contractRkText = Option::where('name', 'contract_generator_rk_text')->first();
        $contractTemplateIdsText = Option::where('name', 'contract_generator_template_ids_text')->first();
        $taxNds = Option::where('name', 'tax_nds')->first();
        $needSeoPages = Option::where('name', 'contract_generator_need_seo_pages')->first();
        $paymentDefaultLawTemplate = Option::where('name', 'payment_generator_default_law_template')->first();

        return Inertia::render('Admin/Settings/Index',[
            'serviceCategories' => $serviceCats ?? [],
            'services' => $services ?? [],
            'mainCategoriesOption' => $mainCategoriesOption ?? [],
            'secondaryCategoriesOption' => $secondaryCategoriesOption ?? [],
            'needSeoPages' => $needSeoPages,
            'taxNds' => $taxNds ?? [],
            'paymentDefaultLawTemplate' => $paymentDefaultLawTemplate ?? [],
            'contractRkText' => $contractRkText ?? [],

        ]);
    }

    public function calendar(Request $request)
    {
        $requestDate = $request->query('date');

        $date = DateHelper::getValidatedDateOrNow($requestDate);
        $date != null ? $formattedDate = $date->format('Y-m') : $formattedDate = now()->format('Y-m');

        $months = DateHelper::workingCalendar($date->format('Y'));

        return Inertia::render('Admin/Settings/Calendar', [
            'months' => $months,
            'date' => $formattedDate,
        ]);
    }

    public function toggleWorkingDayType(WorkingDayRequest $request)
    {
        $date = $request->validated();

        $updatedDay = WorkingDay::updateOrCreate(
            ['date' => $date['date']],
            ['is_working_day' => !$date['is_working_day']]
        );

        return response()->json($updatedDay);
    }

    public function financeWeek(Request $request)
    {
        $requestDate = $request->query('date');

        $date = DateHelper::getValidatedDateOrNow($requestDate);

        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        $formattedStartOfMonth = $startOfMonth->format('Y-m-d');
        $formattedEndOfMonth = $endOfMonth->format('Y-m-d');


        $financeWeeks = FinanceWeek::where('date_start', '>=',  $startOfMonth)
            ->where('date_end', '<=', $endOfMonth)
            ->get();

        return Inertia::render('Admin/Settings/FinanceWeek',[
            'date' => $date->format('Y-m'),
            'startOfMonth' => $formattedStartOfMonth,
            'endOfMonth' => $formattedEndOfMonth,
            'financeWeeks' => $financeWeeks ?? collect()
        ]);
    }
}
