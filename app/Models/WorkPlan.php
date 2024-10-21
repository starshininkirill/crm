<?php

namespace App\Models;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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



    protected $fillable = ['type', 'goal', 'mounth', 'bonus','service_category_id' , 'department_id', 'position_id', ];

    public $timestamps = false;

    public function position() : HasOne
    {
        return $this->HasOne(Position::class);
    }

    public function serviceCategory() : BelongsTo
    {
        return $this->BelongsTo(ServiceCategory::class);
    }

    public function department(): BelongsTo
    {
        return $this->BelongsTo(Department::class);
    }
} 
