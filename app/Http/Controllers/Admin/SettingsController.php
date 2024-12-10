<?php

namespace App\Http\Controllers\admin;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\FinanceWeek;
use App\Models\Option;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

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

        return view('admin.settings.index',[
            'serviceCategories' => $serviceCats ?? [],
            'services' => $services ?? [],
            'mainCategoriesOption' => $mainCategoriesOption ?? [],
            'secondaryCategoriesOption' => $secondaryCategoriesOption ?? [],
            'contractRkText' => $contractRkText,
            'contractTemplateIds' => $contractTemplateIdsText,
            'taxNds' => $taxNds,
            'needSeoPages' => $needSeoPages,
            'paymentDefaultLawTemplate' => $paymentDefaultLawTemplate,
        ]);
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

        $financeWeeks = FinanceWeek::where('date_start', '>=',  $startOfMonth)
            ->where('date_end', '<=', $endOfMonth)
            ->get();

        return view('admin.settings.financeWeek', [
            'date' => $date,
            'financeWeeks' => $financeWeeks ?? collect()
        ]);
    }
}
