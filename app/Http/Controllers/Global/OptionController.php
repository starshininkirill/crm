<?php

namespace App\Http\Controllers\Global;

use App\Http\Requests\Global\OptionRequest;
use App\Http\Controllers\Controller;
use App\Models\Option;

class OptionController extends Controller
{

    public function store(OptionRequest $request)
    {
        $validated = $request->validated();

        $value = $validated['value'];
        $value = is_array($value) ? json_encode($value) : $value;

        $result = Option::updateOrCreate([
            'name' => $validated['name'],
        ],[
            'value' => $value
        ]);

        if ($result->wasRecentlyCreated) {
            return redirect()->back()->with('success', 'Опция успешно создана');
        }else{
            return redirect()->back()->with('success', 'Опция успешно обновлена');
        }

    }

    public function update(OptionRequest $request, Option $option)
    {
        $validated = $request->validated();

        $value = $validated['value'];
        $value = is_array($value) ? json_encode($value) : $value;

        $option->update(['value' => $value]);

        return redirect()->back()->with('success', 'Опция успешно изменена');
    }

    public function massUpdate(OptionRequest $request)
    {
        $validated = $request->validated();

        foreach ($validated['options'] as $option) {
            if ($option['value'] == '' || $option['value'] == null) {
                Option::where('name', $option['name'])->delete();
            } else {
                Option::updateOrCreate(
                    ['name' => $option['name']],
                    ['value' => $option['value']]
                );
            }
        }

        return redirect()->back()->with('success', 'Опции успешно изменены');
    }

    public function destroy(string $id)
    {
        //
    }
}
