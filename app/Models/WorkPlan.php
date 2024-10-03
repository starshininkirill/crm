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

    protected $fillable = ['type', 'value', 'mounth', 'department_id', 'position_id', ];

    public $timestamps = false;

    public function position() : HasOne
    {
        return $this->HasOne(Position::class);
    }

    public function department(): BelongsTo
    {
        return $this->BelongsTo(Department::class);
    }
}
