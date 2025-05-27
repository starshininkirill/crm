<?php

namespace App\Services\SaleReports\Plans;

use App\Exceptions\Business\InfoException;
use App\Models\WorkPlan;
use App\Services\SaleReports\DTO\ReportDTO;
use Exception;

class HeadsPlanCalculator
{
    public function percentPlan(ReportDTO $reportData, int|float $generalPlan, string $planType)
    {
        $workPlan = $reportData->workPlans->firstWhere('type', $planType);

        if (!$workPlan) {
            throw new InfoException('Нет Б1 плана для руководителя');
        }

        if (!array_key_exists('goal', $workPlan->data)) {
            throw new InfoException('Нет Цели для ' . $workPlan->name . ' Плана');
        }

        if (!array_key_exists('bonus', $workPlan->data)) {
            throw new InfoException('Нет Бонуса для ' . $workPlan->name . ' Плана');
        }

        $goalPercentage = $workPlan->data['goal'];
        $bonus = $workPlan->data['bonus'];
        $newMoney = $reportData->newMoney;

        $goalAmount = $generalPlan * ($goalPercentage / 100);
        $totalRequired = $generalPlan + $goalAmount;

        $isAchieved = $newMoney >= $totalRequired;

        return collect([
            'completed' => $isAchieved,
            'bonus' => $isAchieved ? $bonus : 0,
        ]);
    }
}
