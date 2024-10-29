<?php

namespace App\Http\Controllers\Admin\Departments;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Option;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\WorkPlan;
use App\Services\SaleDepartmentServices\PlansService;
use App\Services\SaleDepartmentServices\ReportService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class SaleDepartmentController extends Controller
{
    protected $plansService;

    public function __construct(PlansService $plansService)
    {
        $this->plansService = $plansService;
    }

    public function index()
    {
        $department = Department::getMainSaleDepartment();
        return view('admin.departments.sale.index', ['department' => $department]);
    }

    public function userReport(Request $request)
    {

        $selectUsers = Department::getMainSaleDepartment()->activeUsers();

        $requestData = $request->only(['user', 'date']);

        if ($request->filled(['user', 'date'])) {
            try {
                $date = new Carbon($requestData['date']);
                $user = User::find($requestData['user']);

                if ($user->getFirstWorkingDay()->format('Y-m') > $date->format('Y-m')) {
                    $error = 'Сотрудник ещё не работал в этот месяц.';
                }

                $reportService = new ReportService($this->plansService, $date);

                $daylyReport = $reportService->mounthByDayReport($user);
                $motivationReport = $reportService->motivationReport($user);

                $pivotWeeks = $reportService->pivotWeek();
                $pivotDaily = $reportService->mounthByDayReport();

                $users = Department::getMainSaleDepartment()->activeUsers($date);

                $pivotUsers = $reportService->pivotUsers($users);

                $generalPlan = $reportService->generalPlan($pivotUsers);
            } catch (Exception $e) {
                    if(isset($error)){
                        $error .= ' Не хватает данных для расчёта. Проверьте, все ли планы заполненны';
                    }else{
                        $error = ' Не хватает данных для расчёта. Проверьте, все ли планы заполненны';
                    }
            }
        }
        return view(
            'admin.departments.sale.report',
            [
                'selectUsers' => $selectUsers,
                'users' => $users ?? collect(),
                'user' => isset($user) ? $user : null,
                'date' => isset($date) ? $date : null,
                'daylyReport' => isset($daylyReport) ? $daylyReport : collect(),
                'motivationReport' => isset($motivationReport) ? $motivationReport : collect(),
                'pivotWeeks' => isset($pivotWeeks) ? $pivotWeeks : collect(),
                'pivotDaily' => isset($pivotDaily) ? $pivotDaily : collect(),
                'pivotUsers' => isset($pivotUsers) ? $pivotUsers : collect(),
                'generalPlan' => isset($generalPlan) ? $generalPlan : collect(),
                'serviceCategoryModel' => ServiceCategory::class,
                'error' => $error ?? ''
            ]
        );
    }

    public function reportSettings(Request $request)
    {

        $requestDate = $request->query('date');

        if ($requestDate && DateHelper::isValidYearMonth($requestDate)) {
            $date = Carbon::parse($requestDate);
        } else {
            $date = Carbon::now();
        }

        $date->format('Y-m') == Carbon::now()->format('Y-m') ? $isCurrentMonth = true : $isCurrentMonth = false;

        $departmentId = Department::getMainSaleDepartment()->id;
        $plans = WorkPlan::plansForSaleSettings($date);

        $option = Option::where('name', 'sale_department_ladder_mounth')->first();

        $categoriesForCalculations = ServiceCategory::where('needed_for_calculations', true)->get();

        return view('admin.departments.sale.reportSettings', [
            'departmentId' => $departmentId,
            'workPlanClass' => WorkPlan::class,
            'plans' => $plans,
            'mounthOption' => $option,
            'date' => $date,
            'categoriesForCalculations' => !$categoriesForCalculations->isEmpty() ? $categoriesForCalculations : collect(),
            'isCurrentMonth' => $isCurrentMonth
        ]);
    }
}

// $queryCount = 0;
// DB::listen(function ($query) use (&$queryCount) {
//     $queryCount++;
// });
// $start = microtime(true);
// $end = microtime(true);
// dd($end - $start);
// dd($queryCount);