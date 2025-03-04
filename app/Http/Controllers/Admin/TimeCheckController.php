<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Carbon\Carbon;
use Inertia\Inertia;

class TimeCheckController extends Controller
{
    public function index()
    {
        $mainDepartments = Department::with([
            'users' => function ($query) {
                // Добавить проверку на уволенного сотрудника

                $query->with(['lastAction' => function ($query) {
                    $query->whereDate('date', Carbon::now());
                }]);
            },
            'childDepartments',
            'childDepartments.users' => function ($query) {

                // TODO
                // Добавить проверку на уволенного сотрудника
                // $query->where('created_at', '<=', $date);

                $query->with(['lastAction' => function ($query) {
                    $query->whereDate('date', Carbon::now());
                }]);
            },
        ])->whereNull('parent_id')->get();

        return Inertia::render('Admin/TimeCheck/Index', [
            'mainDepartments' => $mainDepartments,
        ]);
    }
}
