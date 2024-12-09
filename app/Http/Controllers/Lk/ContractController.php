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
        $needSeoPages = json_decode(Option::where('name', 'contract_generator_need_seo_pages')->first()?->value, true) ?? [];

        if (!$cats->isEmpty()) {
            $catsWithServices = $cats->map(function ($category) use( $needSeoPages ) {
                return [
                    'id' => $category->id,
                    'category' => $category->name,
                    'isRk' => $category->type == ServiceCategory::RK,
                    'services' => $category->services->map(function ($service) use($needSeoPages) {
                        return [
                            'id' => $service->id,
                            'name' => $service->name,
                            'price' => $service->price,
                            'work_days_duration' => $service->numeric_working_days(),
                            'isRk' => $service->category->type == ServiceCategory::RK ? true : false,
                            'isSeo' => $service->category->type == ServiceCategory::SEO ? true : false,
                            'isReady' => $service->category->type == ServiceCategory::READY_SITE ? true : false,
                            'needSeoPages' => in_array($service->id, $needSeoPages),
                        ];
                    })->toArray()
                ];
            })->toJson();
        }

        $options = Option::whereIn('name', ['contract_generator_main_categories', 'contract_generator_secondary_categories', 'contract_generator_rk_text'])->get()->keyBy('name');

        $mainCats = $options->get('contract_generator_main_categories')->value ?? [];
        $secondaryCats = $options->get('contract_generator_secondary_categories')->value ?? [];
        $contractRkText = $options->get('contract_generator_rk_text')->value ?? [];

        return view('lk.contract.create', [
            'cats' => $catsWithServices ?? [],
            'mainCats' => $mainCats,
            'secondaryCats' => $secondaryCats,
            'rkText' => $contractRkText,
        ]);
    }
}
