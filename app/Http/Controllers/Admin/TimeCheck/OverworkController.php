<?php

namespace App\Http\Controllers\Admin\TimeCheck;

use App\Exceptions\Business\BusinessException;
use App\Http\Controllers\Controller;
use App\Models\DailyWorkStatus;
use App\Models\WorkStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OverworkController extends Controller
{
    public function index()
    {
        $overworks = DailyWorkStatus::with('user')
            ->where('confirmed', false)
            ->whereHas('workStatus', function ($query) {
                $query->where('type', WorkStatus::TYPE_OVERWORK);
            })->get();

        return Inertia::render('Admin/TimeCheck/Overwork/Index', [
            'overworks' => $overworks,
        ]);
    }

    public function accept(Request $request, DailyWorkStatus $overwork)
    {
        $overwork->confirmed = true;
        if(!$overwork->save()){
            throw new BusinessException('Не удалось подтвердить переработку');
        }

        return redirect()->back()->with('success', 'Переработка одобрена');
    }
    
    public function reject()
    {

    }

}
