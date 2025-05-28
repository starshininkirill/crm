<?php

namespace App\Models;

use App\Helpers\DateHelper;
use App\Models\Traits\HasHistory;
use App\Models\User;
use App\Models\WorkPlan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SimpleCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Department extends Model
{
    use HasFactory, HasHistory;

    public const SALE_DEPARTMENT = 0;
    public const ADVERTISING_DEPARTMENT = 1;

    protected $fillable = [
        'head_id',
        'name',
        'parent_id',
        'type'
    ];

    public static function mainDepartments(): Collection
    {
        return Department::whereNull('parent_id')->get();
    }

    public function head(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'head_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'department_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    public function childDepartments(): HasMany
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    public function workPlans(): HasMany
    {
        return $this->hasMany(WorkPlan::class);
    }

    public static function saleDepartments()
    {
        return Department::where('type', Department::SALE_DEPARTMENT);
    }

    public static function getMainSaleDepartment(): ?Department
    {
        return Department::where('type', Department::SALE_DEPARTMENT)
            ->whereNull('parent_id')
            ->first();
    }

    public static function getDepartmentWithChildrenIds(int $departmentId): array
    {
        $department = Department::with('childDepartments')->find($departmentId);

        if (!$department) {
            return [$departmentId];
        }

        $ids = [$department->id];

        foreach ($department->childDepartments as $child) {
            $ids = array_merge($ids, self::getDepartmentWithChildrenIds($child->id));
        }

        return $ids;
    }

    public function allUsers(?Carbon $date = null, array $relations = []): SimpleCollection
    {
        if (!$date || DateHelper::isCurrentMonth($date)) {
            $users = $this->users->load($relations);

            $this->childDepartments->each(function ($childDepartment) use (&$users, $relations) {
                $users = $users->merge($childDepartment->users->load($relations));
            });

            return $users;
        }

        $users = $this->getUsersForDate($date, $relations);

        $this->childDepartments->each(function ($childDepartment) use (&$users, $date, $relations) {
            $users = $users->merge($childDepartment->getUsersForDate($date, $relations));
        });

        return $users;
    }

    protected function getUsersForDate(?Carbon $date = null, array $relations = [],): SimpleCollection
    {
        if ($date) {
            $allHistoricalUsersQuery = User::getLatestHistoricalRecordsQuery($date)
                ->where('new_values->department_id', $this->id);

            return User::recreateFromQuery($allHistoricalUsersQuery, array_merge($relations, ['department']), $date);
        }

        return $this->users()->with($relations)->get();
    }
}
