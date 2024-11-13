<?php

namespace App\Http\Controllers\Lk;

use App\Http\Controllers\Controller;
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
                    'category' => $category->name,
                    'services' => $category->services->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'name' => $service->name,
                            'price' => $service->price,
                        ];
                    })->toArray()
                ];
            })->toJson();
        }

        return view('lk.contract.create', [
            'cats' => $catsWithServices ?? [],
        ]);
    }
}
