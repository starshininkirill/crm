<?php

namespace App\Models;

use App\Helpers\DateHelper;
use App\Helpers\TextFormaterHelper;
use App\Models\Department;
use App\Models\Traits\HasHistory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class WorkPlan extends Model
{
    use HasFactory, HasHistory;

    public const MOUNTH_PLAN = 'monthPlan';
    public const DOUBLE_PLAN = 'doublePlan';
    public const BONUS_PLAN = 'bonusPlan';
    public const WEEK_PLAN = 'weekPlan';
    public const SUPER_PLAN = 'superPlan';
    public const B1_PLAN = 'b1Plan';
    public const B2_PLAN = 'b2Plan';
    public const B3_PLAN = 'b3Plan';
    public const B4_PLAN = 'b4Plan';
    public const PERCENT_LADDER = 'percentLadder';
    public const NO_PERCENTAGE_MONTH = 'noPercentageMonth';

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
        self::NO_PERCENTAGE_MONTH,
    ];

    protected $fillable = [
        'type',
        'data',
        'goal',
        'month',
        'bonus',
        'service_category_id',
        'department_id',
        'position_id',
        'created_at'
    ];

    protected $casts = [
        'data' => 'array',
    ];

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

}
