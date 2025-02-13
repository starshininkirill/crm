<?php

namespace App\Http\Controllers\Admin\Departments;

use App\Classes\T2Api;
use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Option;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\WorkPlan;
use App\Services\CallStatisticsService;
use App\Services\SaleDepartmentServices\PlansService;
use App\Services\SaleDepartmentServices\ReportInfo;
use App\Services\SaleDepartmentServices\ReportService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

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

        return Inertia::render('Admin/SaleDapartment/Index', [
            'department' => $department,
        ]);
    }

    public function callsReport(Request $request, CallStatisticsService $callStatisticsService)
    {
        $date = Carbon::now()->format('Y-m');

        if ($request->get('date')) {
            $date = $request->get('date');
        }

        $calculatedData = Cache::remember('callsStatisticData_' . $date, 60 * 60, function () use ($callStatisticsService, $date) {
            return $callStatisticsService->calculateTotalCallsData($date);
        });


        $callDurationPlan = 130;
        $callCountPlan = 30;

        return Inertia::render('Admin/SaleDapartment/Calls', [
            'date' => $date,
            'callsDataByDate' => $calculatedData['callsDataByDate'] ?? [],
            'totalNumberValues' => $calculatedData['totalNumberValues'] ?? [],
            'error' => $calculatedData['error'] ?? '',
            'callDurationPlan' => $callDurationPlan,
            'callCountPlan' => $callCountPlan,
            'dateProp' => $date,
        ]);
    }

    public function userReport(Request $request)
    {
        $departments = Department::getSaleDepartments();

        if ($request->filled(['department'])) {
            $selectDepartment = $departments->find($request->department);
        } else {
            $selectDepartment = $departments->whereNull('parent_id')->first();
        }

        $allUsers = $departments->whereNull('parent_id')->first()->activeUsers();

        $requestData = $request->only(['user', 'date']);


        if ($request->filled(['user', 'date'])) {
            try {
                $date = DateHelper::getValidatedDateOrNow($requestData['date']);
                $user = User::find($requestData['user']);
                $users = $selectDepartment->activeUsers($date);

                if ($user->getFirstWorkingDay()->format('Y-m') > $date->format('Y-m')) {
                    $error = 'Сотрудник ещё не работал в этот месяц.';
                }

                $reportInfo = new ReportInfo($date, null, $selectDepartment);
                $reportService = new ReportService($this->plansService, $reportInfo);

                $daylyReport = $reportService->monthByDayReport($user);

                $motivationReport = $reportService->motivationReport($user);
                $pivotWeeks = $reportService->pivotWeek();
                $pivotDaily = $reportService->monthByDayReport();

                $pivotUsers = $reportService->pivotUsers($users);

                $generalPlan = $reportService->generalPlan($pivotUsers);
            } catch (Exception $e) {
                if (isset($error)) {
                    $error .= ' Не хватает данных для расчёта. Проверьте, все ли планы заполненны';
                } else {
                    $error = ' Не хватает данных для расчёта. Проверьте, все ли планы заполненны';
                }
            }
        }


        return Inertia::render('Admin/SaleDapartment/UserReport', [
            'date' => isset($date) && $date != null ?  $date->format('Y-m') : now()->format('Y-m'),
            'users' => $allUsers,
            'selectUser' => $user ?? null,
            'departments' => $departments,
            'selectedDepartment' => $selectDepartment ?? null,
            'daylyReport' => $daylyReport ?? collect(),
            'motivationReport' => $motivationReport ?? collect(),
            'pivotDaily' => $pivotDaily ?? collect(),
            'pivotWeeks' => $pivotWeeks ?? collect(),
            'generalPlan' => $generalPlan ?? collect(),
            'pivotUsers' => $pivotUsers ?? collect(),
        ]);

        return view(
            'admin.departments.sale.report',
            [
                'departments' => $departments ?? collect(),
                'selectedDepartment' => $selectDepartment ?? null,
                'selectUsers' => $selectUsers,
                'users' => $users ?? collect(),
                'user' => $user ?? null,
                'date' => $date ?? null,
                'daylyReport' => $daylyReport ?? collect(),
                'motivationReport' => $motivationReport ?? collect(),
                'pivotWeeks' => $pivotWeeks ?? collect(),
                'pivotDaily' => $pivotDaily ?? collect(),
                'pivotUsers' => $pivotUsers ?? collect(),
                'generalPlan' => $generalPlan ?? collect(),
                'serviceCategoryModel' => ServiceCategory::class,
                'error' => $error ?? ''
            ]
        );
    }

    public function oldUserReport(Request $request)
    {

        $departments = Department::getSaleDepartments();

        if ($request->filled(['department'])) {
            $selectDepartment = $departments->find($request->department);
        } else {
            $selectDepartment = $departments->whereNull('parent_id')->first();
        }

        $selectUsers = $departments->whereNull('parent_id')->first()->activeUsers();

        $requestData = $request->only(['user', 'date']);

        $requestData['user'] = 2;
        $requestData['date'] = '2025-02';

        // if ($request->filled(['user', 'date'])) {
        try {
            $date = DateHelper::getValidatedDateOrNow($requestData['date']);
            $user = User::find($requestData['user']);

            if ($user->getFirstWorkingDay()->format('Y-m') > $date->format('Y-m')) {
                $error = 'Сотрудник ещё не работал в этот месяц.';
            }

            $reportInfo = new ReportInfo($date, null, $selectDepartment);
            $reportService = new ReportService($this->plansService, $reportInfo);

            $daylyReport = $reportService->monthByDayReport($user);

            $motivationReport = $reportService->motivationReport($user);
            $pivotWeeks = $reportService->pivotWeek();
            $pivotDaily = $reportService->monthByDayReport();

            $users = $selectDepartment->activeUsers($date);
            $pivotUsers = $reportService->pivotUsers($users);

            $generalPlan = $reportService->generalPlan($pivotUsers);
        } catch (Exception $e) {
            if (isset($error)) {
                $error .= ' Не хватает данных для расчёта. Проверьте, все ли планы заполненны';
            } else {
                $error = ' Не хватает данных для расчёта. Проверьте, все ли планы заполненны';
            }
        }
        // }

        // return Inertia::render('Admin/SaleDapartment/UserReport', [
        //     'departments' => $departments,
        //     'users' => $selectUsers,
        //     'user' => $user ?? null,
        //     'selectedDepartment' => $selectDepartment ?? null,
        //     'date' => isset($date) && $date != null ?  $date->format('Y-m') : now()->format('Y-m'),
        // ]);

        return view(
            'admin.departments.sale.report',
            [
                'departments' => $departments ?? collect(),
                'selectedDepartment' => $selectDepartment ?? null,
                'selectUsers' => $selectUsers,
                'users' => $users ?? collect(),
                'user' => $user ?? null,
                'date' => $date ?? null,
                'daylyReport' => $daylyReport ?? collect(),
                'motivationReport' => $motivationReport ?? collect(),
                'pivotWeeks' => $pivotWeeks ?? collect(),
                'pivotDaily' => $pivotDaily ?? collect(),
                'pivotUsers' => $pivotUsers ?? collect(),
                'generalPlan' => $generalPlan ?? collect(),
                'serviceCategoryModel' => ServiceCategory::class,
                'error' => $error ?? ''
            ]
        );
    }

    public function reportSettings(Request $request)
    {
        $requestDate = $request->query('date');

        $date = DateHelper::getValidatedDateOrNow($requestDate);

        $date->format('Y-m') == Carbon::now()->format('Y-m') ? $isCurrentMonth = true : $isCurrentMonth = false;

        $departmentId = Department::getMainSaleDepartment()->id;
        $plans = WorkPlan::plansForSaleSettings($date);

        $categoriesForCalculations = ServiceCategory::where('needed_for_calculations', true)->get();

        return Inertia::render('Admin/SaleDapartment/ReportSettings');

        return view('admin.departments.sale.reportSettings', [
            'departmentId' => $departmentId,
            'workPlanClass' => WorkPlan::class,
            'plans' => $plans,
            'date' => $date,
            'categoriesForCalculations' => !$categoriesForCalculations->isEmpty() ? $categoriesForCalculations : collect(),
            'isCurrentMonth' => $isCurrentMonth
        ]);
    }

    public function t2Settings()
    {
        $accessToken = Option::whereName('t2_access_token')->first();
        $refreshToken = Option::whereName('t2_refresh_token')->first();

        return Inertia::render('Admin/SaleDapartment/T2Settings', [
            'accessToken' => $accessToken->value ?? '',
            'refreshToken' => $refreshToken->value ?? '',
        ]);
    }

    public function t2LoadData(Request $request, CallStatisticsService $callStatisticsService)
    {
        if ($request->get('date')) {
            $dateNow = $request->get('date');
        } else {
            $dateNow = Carbon::now()->format('Y-m-d');
        }

        try {
            $t2Api = new T2Api;
            $data = $t2Api->getDataFromT2Api($dateNow, $dateNow);

            if (empty($data)) {
                return redirect()->back()->withErrors('Нет данных за данный период');
            }

            $callStatisticsService->importData($data);
        } catch (Exception $e) {
            $errors = $e->getMessage();
            return redirect()->back()->withErrors($errors);
        }

        Cache::forget('callsStatisticData_' . Carbon::create($dateNow)->format('Y-m'));

        return redirect()->back()->with('success', 'Данные успешно загружены');
    }
}
