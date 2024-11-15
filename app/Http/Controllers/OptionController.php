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

        $value = $validated['value'];        
        $value = is_array($value) ? json_encode($value) : $value;

        Option::create([
            'name' => $validated['name'],
            'value' => $value
        ]);

        return redirect()->back()->with('success', 'Опция успешно создана');
    }

    public function update(OptionRequest $request, Option $option)
    {
        $validated = $request->validated();

        $value = $validated['value'];        
        $value = is_array($value) ? json_encode($value) : $value;
        
        $option->update(['value' => $value]);

        return redirect()->back()->with('success', 'Опция успешно изменена');
    }

    public function destroy(string $id)
    {
        //
    }
}
