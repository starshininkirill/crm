<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractRequest;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function store(ContractRequest $request)
    {
        $data = $request->validated();
        return back()->with('success', 'Договор успешно создан');
    }
}
