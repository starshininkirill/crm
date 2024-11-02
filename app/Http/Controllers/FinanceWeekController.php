<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFinanceWeekRequest;
use App\Models\FinanceWeek;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinanceWeekController extends Controller
{
    public function setWeeks(StoreFinanceWeekRequest $request)
    {
        $data = $request->validated();

        if (isset($data['week'])  && !empty($data['week'])) {
            FinanceWeek::insert($data['week']);
        }

        return redirect()->back()->with('success', 'Финансовые недели успешно добавлены');
    }

    public function updateWeeks(StoreFinanceWeekRequest $request)
    {
        $data = $request->validated();

        if (empty($data)) {
            return back()->withErrors('Ошибка при созданни договора')->withInput();
        }

        foreach ($data['week'] as $week) {
            $existingWeek = FinanceWeek::where('weeknum', $week['weeknum'])
                ->whereYear('date_start', Carbon::parse($week['date_start'])->format('Y'))
                ->whereMonth('date_start', Carbon::parse($week['date_start'])->format('m'))
                ->first();

            if ($existingWeek) {
                $existingWeek->update([
                    'date_start' => $week['date_start'],
                    'date_end' => $week['date_end'],
                ]);
            } else {
                FinanceWeek::create([
                    'date_start' => $week['date_start'],
                    'date_end' => $week['date_end'],
                    'weeknum' => $week['weeknum'],
                ]);
            }
        }

        return redirect()->back()->with('success', 'Финансовые недели успешно изменены');
    }
}
