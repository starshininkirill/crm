<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractStoreRequest;

class ContractController extends Controller
{
    public function store(ContractStoreRequest $request)
    {
        $data = $request->validated();
        dd($data);
        return back()->with('success', 'Договор успешно создан');
    }
}
