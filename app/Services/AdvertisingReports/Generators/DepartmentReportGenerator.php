<?php

namespace App\Services\AdvertisingReports\Generators;

use App\Models\Global\WorkPlan;
use App\Models\UserManagement\Department;
use App\Models\UserManagement\User;
use App\Services\AdvertisingReports\Builders\ReportDataDTOBuilder;
use App\Services\AdvertisingReports\DTO\ReportDataDTO;
use App\Services\AdvertisingReports\DTO\UserDataDTO;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DepartmentReportGenerator
{

    public function __construct(
        private ReportDataDTOBuilder $reportDataDTOBuilder,
    ) {}

    public function generateReport(Carbon $date, Department $department)
    {
        $date = Carbon::now();

        $fullReportData = $this->reportDataDTOBuilder->prepareFullData($date, $department);

        $report = $fullReportData->users->map(function ($user) use ($fullReportData) {
            return $this->processUser($fullReportData, $user);
        });

        return [
            'pairs' => $fullReportData->pairs,
            'users_report' => $report,
        ];
    }

    protected function processUser(ReportDataDTO $fullReportData, User $user): Collection
    {
        $userDataDTO = $this->reportDataDTOBuilder->getUserSubdata($fullReportData, $user);

        $previousMonthsCount = $userDataDTO->previousMonthlyServices->count();
        $nextMonthsCount = $userDataDTO->nextMonthlyServices->count();

        $nextMonthsAmount = $userDataDTO->nextMonthlyServices->sum('price');

        $b1PlanResult = $this->calculateB1Plan($userDataDTO);
        $b2PlanResult = $this->calculateB2Plan($userDataDTO);
        $b3PlanResult = $this->calculateB3Plan($userDataDTO);

        $upsalesPlan = $this->calculateUpsalesPlan($userDataDTO);

        return collect([
            'user' => $user,
            'new_tarifs_count' => $userDataDTO->newTarifCount,
            'previouss_months_count' => $previousMonthsCount,
            'next_months_count' => $nextMonthsCount,
            'next_months_amount' => $nextMonthsAmount,
            'next_months_average' => $nextMonthsAmount / $nextMonthsCount,
            'b1' => $b1PlanResult,
            'b2' => $b2PlanResult,
            'b3' => $b3PlanResult,
            'upsells_plan' => $upsalesPlan,
        ]);
    }

    protected function calculateUpsalesPlan(UserDataDTO $userDataDTO): Collection
    {
        $result = collect([
            'upsales' => [],
            'bonus' => 0,
            'value' => 0,
        ]);

        $plan = $userDataDTO->workPlans->where('type', WorkPlan::UPSALE_BONUS)->first();   

        if(!$plan){
            return $result;
        }

        $upsailsMoney = $userDataDTO->upsails->sum('value');
        $bonus = $plan->data['bonus'];

        $result['value'] = $upsailsMoney;
        $result['bonus'] = $upsailsMoney / 100 * $bonus;

        return $result;
    }

    protected function calculateB3Plan(UserDataDTO $userDataDTO): Collection
    {
        $result = collect([
            'bonus' => 0,
            'completed' => false,
            'goal' => 0,
            'value' => $userDataDTO->newTarifCount,
        ]);

        $plan = $userDataDTO->workPlans->where('type', WorkPlan::B3_PLAN)->first();        

        return $result;
    }

    protected function calculateB2Plan(UserDataDTO $userDataDTO): Collection
    {
        $result = collect([
            'bonus' => 0,
            'completed' => false,
            'goal' => 0,
            'value' => $userDataDTO->newTarifCount,
        ]);

        return $result;

        // $plan = $userDataDTO->workPlans->where('type', WorkPlan::B1_PLAN)->first();

        // if(!$plan){
        //     return $result;
        // }

        // $result['goal'] = $plan->data['goal'];

        // if($userDataDTO->newTarifCount >= $plan->data['goal']){
        //     $result['bonus'] = $plan->data['bonus'];
        //     $result['completed'] = true;
        // }

        // return $result;
    }

    protected function calculateB1Plan(UserDataDTO $userDataDTO): Collection
    {
        $result = collect([
            'bonus' => 0,
            'completed' => false,
            'goal' => 0,
            'value' => $userDataDTO->newTarifCount,
        ]);

        $plan = $userDataDTO->workPlans->where('type', WorkPlan::B1_PLAN)->first();

        if(!$plan){
            return $result;
        }

        $result['goal'] = $plan->data['goal'];

        if($userDataDTO->newTarifCount >= $plan->data['goal']){
            $result['bonus'] = $plan->data['bonus'];
            $result['completed'] = true;
        }

        return $result;
    }
}
