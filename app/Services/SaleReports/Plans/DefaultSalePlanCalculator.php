<?php

namespace App\Services\SaleReports\Plans;

use App\Models\Global\WorkPlan;
use App\Services\SaleReports\DTO\ReportDTO;
use App\Services\SaleReports\DTO\UserDataDTO;
use App\Services\UserServices\UserService;
use Illuminate\Support\Collection;

class DefaultSalePlanCalculator extends AbstractDepartmentPlanCalculator
{
    protected $boneses = 0;

    public function __construct(
        protected UserService $userService
    ) {}

    public function getBonuses()
    {
        $bonuses = $this->boneses;

        $this->boneses = 0;
        return $bonuses;
    }

    protected function getPlan(string|int $planType, ReportDTO|UserDataDTO $reportInfo): ?WorkPlan
    {
        return $reportInfo->workPlans->where('type', $planType)
            ->whereNull('position_id')
            ->first();
    }

    protected function updateBonus(int|float $bonus): void
    {
        $this->boneses += $bonus;
    }

    public function b2Plan(UserDataDTO $reportInfo) : Collection
    {
        $result = collect([
            'completed' => false,
            'bonus' => 0,
        ]);

        $plan = $this->getPlan(WorkPlan::B2_PLAN, $reportInfo);

        if (!$plan || empty($plan->data)) {
            return $result;
        }

        $includedServiceIds = $plan->data['includeIds'] ?? [];
        $excludedServiceIds = $plan->data['excludeIds'] ?? [];
        $goal = $plan->data['goal'] ?? '';
        $bonus = $plan->data['bonus'] ?? 0;

        if (empty($includedServiceIds) || !is_numeric($goal) || $goal <= 0) {
            return $result;
        }

        $matchingContractCount = $reportInfo->contracts
            ->filter(function ($contract) use ($includedServiceIds, $excludedServiceIds) {
                $serviceIds = $contract->services->pluck('id');
                if ($serviceIds->intersect($includedServiceIds)->isEmpty()) {
                    return false;
                }

                if (!$serviceIds->intersect($excludedServiceIds)->isEmpty()) {
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

    public function b3Plan(UserDataDTO $reportInfo) : Collection
    {
        $result = collect([
            'completed' => false,
            'bonus' => 0,
        ]);

        $plan = $this->getPlan(WorkPlan::B3_PLAN, $reportInfo);

        if (!$plan || empty($plan->data)) {
            return $result;
        }

        $includedServiceIds = $plan->data['includeIds'] ?? [];
        $includedServiceCategoriesIds = $plan->data['includedCategoryIds'] ?? [];
        $excludeServicePairs = $plan->data['excludeServicePairs'] ?? [];
        $goal = $plan->data['goal'] ?? '';
        $bonus = $plan->data['bonus'] ?? 0;

        if (empty($includedServiceIds) || !is_numeric($goal) || $goal <= 0) {
            return $result;
        }

        $matchingContractCount = $reportInfo->contracts
            ->filter(function ($contract) use ($includedServiceIds, $includedServiceCategoriesIds, $excludeServicePairs) {
                $serviceIds = $contract->services->pluck('id');

                if ($serviceIds->count() < 2) {
                    return false;
                }
                $servicesFromCategories = $contract->services->whereIn('service_category_id', $includedServiceCategoriesIds)->pluck('id');

                $hasIncludedService = $serviceIds->intersect($includedServiceIds)->isNotEmpty();

                $hasIncludedCategory = $servicesFromCategories->intersect($includedServiceCategoriesIds)->isNotEmpty();

                $hasValidServices = $hasIncludedService && $hasIncludedCategory;

                if (!$hasValidServices) {
                    return false;
                }

                foreach ($excludeServicePairs as $pair) {
                    if ($serviceIds->contains($pair[0]) && $serviceIds->contains($pair[1])) {
                        return false;
                    }
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


    public function b4Plan(UserDataDTO $reportInfo) : Collection
    {
        $result = collect([
            'completed' => false,
            'bonus' => 0,
        ]);

        $plan = $this->getPlan(WorkPlan::B4_PLAN, $reportInfo);

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
                return !$serviceIds->intersect($includedServiceIds)->isEmpty();
            })
            ->count();

        if ($matchingContractCount >= $goal) {
            $result['completed'] = true;
            $result['bonus'] = $bonus;
            $this->updateBonus($bonus);
        }

        return $result;
    }

    protected function getLadderPlans(UserDataDTO $reportInfo): Collection
    {
        return $reportInfo->workPlans
            ->where('type', WorkPlan::PERCENT_LADDER)
            ->whereNull('position_id');
    }
}
