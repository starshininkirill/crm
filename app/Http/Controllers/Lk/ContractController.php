<?php

namespace App\Http\Controllers\Lk;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;

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
                            'work_days_duration' => $service->work_days_duration
                        ];
                    })->toArray()
                ];
            })->toJson();
        }
        
        $mainCategoriesOption = Option::where('name', 'contract_main_categories')->first();
        $mainCats = $mainCategoriesOption != null ? $mainCategoriesOption->value : [];

        $secondaryCategoriesOption = Option::where('name', 'contract_secondary_categories')->first();
        $secondaryCats = $secondaryCategoriesOption != null ? $secondaryCategoriesOption->value : [];

        return view('lk.contract.create', [
            'cats' => $catsWithServices ?? [],
            'mainCats' => $mainCats,
            'secondaryCats' => $secondaryCats
        ]);
    }
}
