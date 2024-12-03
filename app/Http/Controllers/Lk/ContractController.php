<?php

namespace App\Http\Controllers\Lk;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\ServiceCategory;

class ContractController extends Controller
{
    public function create()
    {

        $cats = ServiceCategory::with('services')->get();

        if (!$cats->isEmpty()) {
            $catsWithServices = $cats->map(function ($category) {
                return [
                    'id' => $category->id,
                    'category' => $category->name,
                    'services' => $category->services->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'name' => $service->name,
                            'price' => $service->price,
                            'work_days_duration' => $service->numeric_working_days(),
                            'isRk' => $service->category->type == ServiceCategory::RK ? true : false,
                            'isSeo' => $service->category->type == ServiceCategory::SEO ? true : false,
                            'isReady' => $service->category->type == ServiceCategory::READY_SITE ? true : false,
                        ];
                    })->toArray()
                ];
            })->toJson();
        }

        $options = Option::whereIn('name', ['contract_main_categories', 'contract_secondary_categories', 'contract_rk_text'])->get()->keyBy('name');

        $mainCats = $options->get('contract_main_categories')->value ?? [];
        $secondaryCats = $options->get('contract_secondary_categories')->value ?? [];
        $contractRkText = $options->get('contract_rk_text')->value ?? [];

        return view('lk.contract.create', [
            'cats' => $catsWithServices ?? [],
            'mainCats' => $mainCats,
            'secondaryCats' => $secondaryCats,
            'rkText' => $contractRkText,
        ]);
    }
}
