<?php

namespace App\Http\Controllers\Admin\Departments;

use App\Classes\T2Api;
use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\CallStat;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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

        $dateNow = Carbon::yesterday()->format('Y-m-d');

        // $date_start = urlencode("{$dateNow}T00:00:01+03:00");
        // $date_end = urlencode("{$dateNow}T23:59:59+03:00");
        // $size = 3000;
        // $type = 'caller';
        // $client_number = '79922851746';
        // $access_token = 'eyJhbGciOiJIUzUxMiJ9.eyJVc2VyRGV0YWlsc0ltcGwiOnsiY29tcGFueUlkIjo0NjkyLCJ1c2VySWQiOjEyMTU2LCJsb2dpbiI6Ijc5MDA1MDEyMzUwIn0sInN1YiI6IkFDQ0VTU19PUEVOQVBJX1RPS0VOIiwiZXhwIjoxNzM4OTEwNTMyfQ.wao60mKA-_MzNbvzNtTxoU0s_5lauErP-44MWau3D0M7Jlr7NgNcOOKL8Po0TkkokwuOtcerRkps_F0zJKzKrQ';

        // $target_url = "https://ats2.t2.ru/crm/openapi/call-records/info?start={$date_start}&end={$date_end}&size={$size}";

        // $response = Http::withoutVerifying()->withHeaders([
        //     'Authorization' => $access_token,
        //     'Content-Type' => 'application/json',
        // ])->get($target_url);

        // $info = $response->headers();
        // $body = $response->body();

        // dd($body);


        // $data =  T2Api::getDataFromT2Api($dateNow, $dateNow);
        // T2Api::importDataFromApi($dateNow, $dateNow);


        $department = Department::getMainSaleDepartment();

        return Inertia::render('Admin/SaleDapartment/Index', [
            'department' => $department,
        ]);
    }

    public function callsReport(Request $request , CallStatisticsService $callStatisticsService)
    {
        $date = Carbon::now()->format('Y-m-d');

        if ($request->get('date')) {
            $date = $request->get('date');
        }
        
        $calculatedData = $callStatisticsService->calculateTotalCallsData($date);
        $callDurationPlan = 130;
        $callCountPlan = 30;

        return Inertia::render('Admin/SaleDapartment/Calls', [
            'date' => $date,
            'callsDataByDate' => $calculatedData['callsDataByDate'],
            'totalNumberValues' => $calculatedData['totalNumberValues'],
            'callDurationPlan' => $callDurationPlan,
            'callCountPlan' => $callCountPlan,
        ]);
    }

    public function userReport(Request $request)
    {

        $departments = Department::getSaleDepartments();

        if ($request->filled(['department'])) {
            $selectDepartment = $departments->where('id', $request->department)->first();
        } else {
            $selectDepartment = $departments->whereNull('parent_id')->first();
        }

        $selectUsers = $departments->whereNull('parent_id')->first()->activeUsers();

        $requestData = $request->only(['user', 'date']);


        if ($request->filled(['user', 'date'])) {
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
        }

        return Inertia::render('Admin/SaleDapartment/UserReport', [
            'departments' => $departments,
            'users' => $selectUsers,
            'user' => $user ?? null,
            'selectedDepartment' => $selectDepartment ?? null,
            'date' => isset($date) && $date != null ?  $date->format('Y-m') : now()->format('Y-m'),
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
        $accessToken = Option::where('name', 't2_access_token')->first();
        $refreshToken = Option::where('name', 't2_refresh_token')->first();


        // $dateNow = Carbon::yesterday()->format('Y-m-d');
        // $errors = '';

        // try {
        //     $t2Api = new T2Api;
        //     $t2Api->importDataFromApi($dateNow, $dateNow);
        // } catch (Exception $e) {
        //     $errors = $e->getMessage();
        // }

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
            
            if(empty($data)){
                return redirect()->back()->withErrors('Нет данных за данный период');
            }

            $callStatisticsService->importData($data);

        } catch (Exception $e) {
            $errors = $e->getMessage();
            return redirect()->back()->withErrors($errors);
        }

        return redirect()->back()->with('success', 'Данные успешно загружены');
    }
}
