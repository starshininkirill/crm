<?php

namespace App\Http\Controllers\Web\Admin\TimeCheck;

use App\Exceptions\Business\BusinessException;
use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TimeCheck\TimeSheetRequest;
use App\Http\Requests\Admin\TimeCheck\UserAdjustmentRequest;
use App\Models\Department;
use App\Models\UserAdjustment;
use App\Services\UserServices\TimeSheetService;
use App\Services\UserServices\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TimeSheetController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index(TimeSheetRequest $request, TimeSheetService $service)
    {
        $startTime = microtime(true);
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
        ];

        if ($departments->isEmpty() || $targetDate > Carbon::now()->endOfMonth()) {
            return Inertia::render('Admin/TimeCheck/TimeSheet/Index', $info);
        }

        $service->status = $request->filled('status')
            ? $request->input('status')
            : 'active';

        $info['usersReport'] = $service->newGenerateUsersReport($departments, $targetDate);

        return Inertia::render('Admin/TimeCheck/TimeSheet/Index', $info);
    }

    public function userAdjustmentStore(UserAdjustmentRequest $request)
    {
        $validated = $request->validated();

        $bonus = UserAdjustment::create($validated);

        if (!$bonus) {
            throw new BusinessException('Не удалось создать бонус');
        }

        return redirect()->back()->with('success', 'Выплата успешно создана');
    }

    public function userAdjustmentDestroy(UserAdjustment $adjustment)
    {
        if (!$adjustment->delete()) {
            throw new BusinessException('Не удалось удалить бонус');
        }

        return redirect()->back()->with('success', 'Выплата успешно удалёна');
    }
}
