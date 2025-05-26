<?php

namespace App\Http\Controllers\Admin\Departments;

use App\Classes\T2Api;
use App\Factories\SaleDepartment\ReportFactory;
use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaleWorkPlanRequest;
use App\Models\Department;
use App\Models\Option;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\WorkPlan;
use App\Services\CallHistoryService;
use App\Services\SaleDepartmentServices\ReportService;
use App\Services\SaleDepartmentServices\WorkPlanService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SaleDepartmentController extends Controller
{
    public function __construct(
        private ReportFactory $reportFactory,
    ) {}

    public function index()
    {
        $department = Department::getMainSaleDepartment();

        return Inertia::render('Admin/SaleDapartment/Index', [
            'department' => $department,
        ]);
    }

    public function callsReport(Request $request, CallHistoryService $CallHistoryService)
    {
        $date = Carbon::now()->format('Y-m');

        if ($request->get('date')) {
            $date = $request->get('date');
        }

        $calculatedData = $CallHistoryService->calculateTotalCallsData($date);

        $mainDepartment = Department::getMainSaleDepartment();
        $callsPlan = WorkPlan::where('department_id', $mainDepartment->id)
            ->whereYear('created_at', Carbon::parse($date)->format('Y'))
            ->whereMonth('created_at', Carbon::parse($date)->format('m'))
            ->where('type', WorkPlan::B1_PLAN)
            ->first();

        $callDurationPlan = $callsPlan ? $callsPlan->data['avgDurationCalls'] : 0;
        $callCountPlan = $callsPlan ? $callsPlan->data['avgCountCalls'] : 0;

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

    public function userReport(Request $request, ReportService $reportService)
    {
        $departments = Department::saleDepartments()->get();
        $mainDepartment = $departments->whereNull('parent_id')->first();

        $selectDepartment = $request->filled(['department']) ?
            $selectDepartment = $departments->find($request->get('department'))
            : $mainDepartment;

        $user = $request->get('user') ? User::find($request->get('user')) : null;

        $date = Carbon::parse($request->get('date')) ?? Carbon::now();
        $date = $date->endOfMonth();

        if ($user && $date) {
            $report = $reportService->generateFullReport($selectDepartment, $user, $date);
        }

        return Inertia::render('Admin/SaleDapartment/UserReport', [
            'date' => fn() => $date ? $date->format('Y-m') : now()->format('Y-m'),
            'users' => fn() => $mainDepartment->allUsers($date),
            'selectUser' => fn() => $user ?? null,
            'departments' => fn() => $departments ?? collect(),
            'selectedDepartment' => fn() => $selectDepartment ?? null,
            'report' => fn() => $report ?? [],
        ]);
    }

    public function usersInDepartment(Request $request)
    {
        $date = $request->filled('date') ? Carbon::parse($request->get('date')) : Carbon::now();
        $date = $date->endOfMonth();
        $department = Department::getMainSaleDepartment();

        return response()->json([
            'users' => $department->allUsers($date),
        ]);
    }

    public function heads(Request $request, ReportService $reportService)
    {
        $date = $request->filled('date') ? Carbon::parse($request->get('date'))->endOfMonth() : Carbon::now();

        $report = $reportService->generateHeadsReport($date);

        return Inertia::render('Admin/SaleDapartment/Head');
    }

    public function plansSettings(Request $request, WorkPlanService $workPlanService)
    {
        $requestDate = $request->query('date');

        $date = DateHelper::getValidatedDateOrNow($requestDate);
        $isCurrentMonth = DateHelper::isCurrentMonth($date);

        $departmentId = Department::getMainSaleDepartment()->id;
        $plans = $workPlanService->plansForSaleSettings($date);

        $services = Service::with('category')->get();
        $rkServices = $services->where('category.type', ServiceCategory::RK)->values();
        $seoServices = $services->where('category.type', ServiceCategory::SEO)->values();

        $serviceCats = ServiceCategory::all();

        return Inertia::render('Admin/SaleDapartment/PlansSettings', [
            'dateProp' => $date->format('Y-m'),
            'plans' => $plans,
            'isCurrentMonth' => $isCurrentMonth,
            'departmentId' => $departmentId,
            'rkServices' => $rkServices,
            'seoServices' => $seoServices,
            'services' => $services,
            'serviceCats' => $serviceCats,
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

    public function t2LoadData(Request $request, CallHistoryService $CallHistoryService)
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

            $CallHistoryService->importData($data);
        } catch (Exception $e) {
            $errors = $e->getMessage();
            return redirect()->back()->withErrors($errors);
        }

        return redirect()->back()->with('success', 'Данные успешно загружены');
    }

    public function storeWorkPlan(SaleWorkPlanRequest $request)
    {
        $validated = $request->validated();

        $workPlan = WorkPlan::create($validated);

        if (!$workPlan) {
            return redirect()->back()->withErrors('Не удалось создать план');
        }

        return redirect()->back()->with('success', 'План успешно создан');
    }

    public function updateWorkPlan(SaleWorkPlanRequest $request, WorkPlan $workPlan)
    {
        $validated = $request->validated();

        if (!$workPlan->update($validated)) {
            return redirect()->back()->withErrors('Не удалось обновить план');
        }

        return redirect()->back()->with('success', 'План успешно изменён');
    }

    public function destroyWorkPlan(WorkPlan $workPlan)
    {
        if (!$workPlan->delete()) {
            return redirect()->back()->withErrors('Не удалось Удалить план');
        }

        return redirect()->back()->with('success', 'План успешно удалён');
    }
}
