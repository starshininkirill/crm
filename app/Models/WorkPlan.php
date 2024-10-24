<?php

namespace App\Models;

use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class WorkPlan extends Model
{
    use HasFactory;

    public const MOUNTH_PLAN = 0;
    public const DOUBLE_PLAN = 1;
    public const BONUS_PLAN = 2;
    public const WEEK_PLAN = 3;
    public const SUPER_PLAN = 4;
    public const B1_PLAN = 5;
    public const B2_PLAN = 6;
    public const B3_PLAN = 7;
    public const B4_PLAN = 8;
    public const PERCENT_LADDER = 9;

    public const ALL_PLANS = [
        self::MOUNTH_PLAN,
        self::DOUBLE_PLAN,
        self::BONUS_PLAN,
        self::WEEK_PLAN,
        self::SUPER_PLAN,
        self::B1_PLAN,
        self::B2_PLAN,
        self::B3_PLAN,
        self::B4_PLAN,
        self::PERCENT_LADDER,
    ];



    protected $fillable = ['type', 'goal', 'mounth', 'bonus', 'service_category_id', 'department_id', 'position_id',];

    public function position(): HasOne
    {
        return $this->HasOne(Position::class);
    }

    public function serviceCategory(): BelongsTo
    {
        return $this->BelongsTo(ServiceCategory::class);
    }

    public function department(): BelongsTo
    {
        return $this->BelongsTo(Department::class);
    }

    public static function plansForSaleSettings(Carbon $date): Collection
    {
        $departmentId = Department::getMainSaleDepartment()->id;
        $plans = WorkPlan::where('department_id', $departmentId)
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month) 
            ->orderBy('bonus')
            ->orderBy('goal')
            ->get()
            ->groupBy('type');

        if($plans->has(WorkPlan::MOUNTH_PLAN)){
            $plans[WorkPlan::MOUNTH_PLAN] = $plans[WorkPlan::MOUNTH_PLAN]->filter(function ($plan) {
                return $plan->mounth != null;
            });
    
        }

        return $plans;
    }
}
