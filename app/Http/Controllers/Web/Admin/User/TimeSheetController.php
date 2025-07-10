<?php

namespace App\Http\Controllers\Web\Admin\User;

use App\Exceptions\Business\BusinessException;
use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\TimeSheetRequest;
use App\Http\Requests\Admin\User\UserAdjustmentRequest;
use App\Models\Department;
use App\Models\User;
use App\Models\UserAdjustment;
use App\Services\TimeSheet\TimeSheetService;
use App\Services\UserServices\UserService;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use App\Http\Requests\Admin\User\ExportSalaryRequest;
use App\Exports\TimeSheet\SalaryExport;
use App\Models\EmploymentType;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TimeSheetController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index(TimeSheetRequest $request, TimeSheetService $service)
    {
        $status = $request->input('status', 'active');

        $departments = $request->filled('department_id')
            ? collect([Department::with(['head', 'users.position'])->findOrFail($request->input('department_id'))])
            : Department::whereDoesntHave('childDepartments')->with(['head', 'users.position'])->get();

        $targetDate = $request->filled('date')
            ? Carbon::parse($request->input('date'))->endOfMonth()
            : Carbon::now()->endOfMonth();

        $info = [
            'days' => DateHelper::daysInMonth($targetDate),
            'departments' => Department::all(),
            'department' => $request->filled('department_id') ? Department::findOrFail($request->input('department_id')) : null,
            'status' => $status,
            'date' => $targetDate->format('Y-m'),
            'usersReport' => collect(),
            'employmentTypes' => EmploymentType::all(),
        ];

        if ($departments->isEmpty() || $targetDate > Carbon::now()->endOfMonth()) {
            return Inertia::render('Admin/User/TimeSheet/Index', $info);
        }

        $service->status = $request->filled('status')
            ? $request->input('status')
            : 'active';

        $info['usersReport'] = $service->generateUsersReport($departments, $targetDate);

        return Inertia::render('Admin/User/TimeSheet/Index', $info);
    }

    public function userAdjustmentStore(UserAdjustmentRequest $request, TimeSheetService $service)
    {
        $validated = $request->validated();

        $bonus = UserAdjustment::create($validated);

        if (!$bonus) {
            throw new BusinessException('Не удалось создать бонус');
        }

        $user = User::findOrFail($validated['user_id']);
        $date = Carbon::parse($validated['date']);

        $service->loadRelationsForUsers(new EloquentCollection([$user]), $date);
        $report = $service->generateUserReport($user, $date);

        return response()->json(['user' => $report]);
    }

    public function userAdjustmentDestroy(UserAdjustment $adjustment, TimeSheetService $service)
    {
        $user = $adjustment->user;
        $date = Carbon::parse($adjustment->date);

        if (!$adjustment->delete()) {
            throw new BusinessException('Не удалось удалить бонус');
        }

        $service->loadRelationsForUsers(new EloquentCollection([$user]), $date);
        $report = $service->generateUserReport($user, $date);

        return response()->json(['user' => $report]);
    }

    public function exportSalary(ExportSalaryRequest $request, TimeSheetService $service)
    {
        $validated = $request->validated();
        $date = Carbon::parse($validated['date'])->endOfMonth();
        $half = (int) $validated['half'];
        $employmentTypeIds = $validated['employment_type_ids'];
        $departmentId = $validated['department_id'] ?? null;

        $departments = $departmentId
            ? collect([Department::with(['head', 'users.position'])->findOrFail($request->input('department_id'))])
            : Department::whereDoesntHave('childDepartments')->with(['head', 'users.position'])->get();

        $report = $service->generateUsersReport($departments, $date);

        $filteredReport = $report->flatten(1)->filter(function ($userReport) use ($employmentTypeIds) {
            return in_array($userReport['employment_type_id'], $employmentTypeIds);
        })->filter(function ($userReport) use ($half) {
            $amount = $half === 1
                ? $userReport['amount_first_half_salary_with_compensation']
                : $userReport['amount_second_half_salary_with_compensation'];
            return $amount > 0;
        });

        $filename = "экспорт_зарплаты_от_{$date->format('Y_m')}_за_{$half}_половину_месяца.xlsx";

        return Excel::download(new SalaryExport($filteredReport, $half), $filename);
    }
}
