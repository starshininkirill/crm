<?php

namespace App\Http\Controllers\Web\Admin\Staff;

use App\Exceptions\Business\BusinessException;
use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Staff\ExportSalaryRequest;
use App\Http\Requests\Admin\Staff\NoteRequest;
use App\Http\Requests\Admin\Staff\TimeSheetRequest;
use App\Http\Requests\Admin\Staff\UserAdjustmentRequest;
use App\Models\UserManagement\Department;
use App\Models\UserManagement\EmploymentType;
use App\Models\UserManagement\User;
use App\Models\Finance\UserAdjustment;
use App\Services\TimeSheet\TimeSheetService;
use App\Services\UserServices\UserService;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use App\Exports\TimeSheet\SalaryExport;
use App\Models\Global\Note;
use App\Models\Global\Option;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Admin\Staff\ScheduleSalaryUpdateRequest;
use App\Models\UserManagement\ScheduledUpdate;

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

        if ($departments->isEmpty()) {
            return Inertia::render('Admin/Staff/TimeSheet/Index', $info);
        }

        $service->status = $request->filled('status')
            ? $request->input('status')
            : 'active';

        $info['usersReport'] = $service->generateUsersReport($departments, $targetDate);

        return Inertia::render('Admin/Staff/TimeSheet/Index', $info);
    }

    public function storeNote(NoteRequest $request)
    {
        $validated = $request->validated();

        $user = User::findOrFail($validated['user_id']);

        $note = $user->notes()->updateOrCreate(
            [
                'date' => Carbon::parse($validated['date'])->startOfMonth(),
                'type' => Note::TYPE_TIME_SHEET,
            ],
            [
                'content' => $validated['content'],
            ]
        );

        return response()->json(['note' => $note]);
    }

    public function destroyNote(Note $note)
    {
        $note->delete();

        return response()->json(['note' => $note]);
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
        $report = $service->generateUserReport($user, $date->copy()->startOfMonth()->subMonth());

        Log::info($report);

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
        $report = $service->generateUserReport($user, $date->copy()->startOfMonth()->subMonth());

        return response()->json(['user' => $report]);
    }

    public function scheduleSalaryUpdate(ScheduleSalaryUpdateRequest $request)
    {
        $validated = $request->validated();

        $user = User::findOrFail($validated['user_id']);

        $effectiveDate = Carbon::parse($validated['effective_date'])->startOfMonth();
        $reportDate = Carbon::parse($validated['date'])->startOfMonth();

        $scheduledUpdate = $user->scheduledUpdates()->create($validated);
        if(!$scheduledUpdate){
            throw new BusinessException('Не удалось запланировать изменение зарплаты');
        }

        if ($effectiveDate->lt($reportDate)) {
            return response()->json(['scheduled_update' => null]);
        }
        
        $scheduledUpdate->effective_date = Carbon::parse($scheduledUpdate->effective_date)->format('Y-m-d');

        return response()->json([
            'scheduled_update' => $scheduledUpdate->only('id', 'new_value', 'effective_date', 'field')
        ]);
    }

    public function exportSalary(ExportSalaryRequest $request, TimeSheetService $service)
    {
        $validated = $request->validated();
        
        $option = Option::firstWhere('name', 'ids_of_employment_types_for_generating_salary_table');
        if(!$option){
            throw new BusinessException('Не найдены типы трудоустройства для формирования отчета');
        }
        $employmentTypeIds = $option->value ? json_decode($option->value, true) : [];

        
        $date = Carbon::parse($validated['date'])->endOfMonth();
        $half = (int) $validated['half'];
        
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
