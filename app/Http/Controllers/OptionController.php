<?php

namespace App\Http\Controllers;

use App\Http\Requests\OptionRequest;
use App\Models\Option;
use Illuminate\Http\Request;

class OptionController extends Controller
{

    public function store(OptionRequest $request)
    {
        $validated = $request->validated();

        Option::create($validated);

        return redirect()->back()->with('success', 'Опция успешно создана');
    }

    public function update(OptionRequest $request, Option $option)
    {
        $validated = $request->validated();
        
        $option->update($validated);

        return redirect()->back()->with('success', 'Опция успешно изменена');
    }

    public function destroy(string $id)
    {
        //
    }
}
