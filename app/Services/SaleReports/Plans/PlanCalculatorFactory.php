<?php

namespace App\Services\SaleReports\Plans;

use App\Models\UserManagement\User;
use App\Services\SaleReports\Plans\AbstractDepartmentPlanCalculator;
use Illuminate\Contracts\Foundation\Application;

final class PlanCalculatorFactory
{
    /**
     * @var array<int, class-string<AbstractDepartmentPlanCalculator>>
     */
    private array $calculators = [
        AdvertisingSalePlanCalculator::class,
    ];

    public function __construct(private Application $app)
    {
    }

    public function defaultCalculator(): AbstractDepartmentPlanCalculator
    {
        return $this->app->make(DefaultSalePlanCalculator::class);
    }

    public function makeFor(User $user): AbstractDepartmentPlanCalculator
    {
        $position = $user->position;

        if ($position) {
            foreach ($this->calculators as $calculatorClass) {
                if (method_exists($calculatorClass, 'handles') && $calculatorClass::handles($position)) {
                    return $this->app->make($calculatorClass);
                }
            }
        }

        return $this->app->make(DefaultSalePlanCalculator::class);
    }
} 