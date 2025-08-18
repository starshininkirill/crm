<?php

namespace App\Services\SaleReports\Plans;

use App\Models\Global\WorkPlan;
use App\Models\UserManagement\Position;
use App\Services\SaleReports\DTO\ReportDTO;
use App\Services\SaleReports\DTO\UserDataDTO;
use App\Services\UserServices\UserService;
use Illuminate\Support\Collection;

class AdvertisingSalePlanCalculator extends AbstractDepartmentPlanCalculator
{
    protected $boneses = 0;

    public function __construct(
        protected UserService $userService
    ) {}

    public static function handles(Position $position): bool
    {
        return $position->plan_type === Position::PLAN_TYPE_ADVERTISING_SALE;
    }

    public function getBonuses()
    {
        $bonuses = $this->boneses;

        $this->boneses = 0;
        return $bonuses;
    }

    protected function getPlan(string|int $planType, ReportDTO|UserDataDTO $reportInfo): ?WorkPlan
    {
        return $reportInfo->workPlans
            ->where('type', $planType)
            ->where('position_id', $reportInfo->user->position_id)
            ->first();
    }

    protected function updateBonus(int|float $bonus): void
    {
        $this->boneses += $bonus;
    }
    

    public function b2Plan(UserDataDTO $reportInfo): Collection
    {
        return $this->calculateServicesPlan($reportInfo, WorkPlan::B2_PLAN);
    }

    public function b3Plan(UserDataDTO $reportInfo): Collection
    {
        return $this->calculateServicesPlan($reportInfo, WorkPlan::B3_PLAN);
    }

    public function b4Plan(UserDataDTO $reportInfo): Collection
    {
        return $this->calculateServicesPlan($reportInfo, WorkPlan::B4_PLAN);
    }

    protected function getLadderPlans(UserDataDTO $reportInfo): Collection
    {
        return $reportInfo->workPlans
            ->where('type', WorkPlan::PERCENT_LADDER)
            ->where('position_id', $reportInfo->user->position_id);
    }

    protected function calculateServicesPlan(UserDataDTO $reportInfo, string|int $planType): Collection
    {
        $result = collect([
            'completed' => false,
            'bonus' => 0,
        ]);

        $plan = $this->getPlan($planType, $reportInfo);

        if (!$plan || empty($plan->data)) {
            return $result;
        }

        $includedServiceIds = $plan->data['includeIds'] ?? [];
        $goal = $plan->data['goal'] ?? '';
        $bonus = $plan->data['bonus'] ?? 0;

        if (empty($includedServiceIds) || !is_numeric($goal) || $goal <= 0) {
            return $result;
        }

        $matchingContractCount = $reportInfo->contracts
            ->filter(function ($contract) use ($includedServiceIds) {
                $serviceIds = $contract->services->pluck('id');
                if ($serviceIds->intersect($includedServiceIds)->isEmpty()) {
                    return false;
                }
                return true;
            })
            ->count();

        if ($matchingContractCount >= $goal) {
            $result['completed'] = true;
            $result['bonus'] = $bonus;
            $this->updateBonus($bonus);
        }

        return $result;
    }
}
