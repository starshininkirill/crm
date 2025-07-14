<?php

namespace App\Models\UserManagement;

use App\Helpers\DateHelper;
use App\Models\Traits\HasHistory;
use App\Models\UserManagement\User;
use App\Models\Global\WorkPlan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SimpleCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Department extends Model
{
    use HasFactory, HasHistory;

    public const DEPARTMENT_SALE = 0;
    public const DEPARTMENT_ADVERTISING = 1;
    public const DEPARTMENT_PROJECT_MANAGERS = 2;

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
        return Department::where('type', Department::DEPARTMENT_SALE);
    }

    public static function getMainSaleDepartment(): ?Department
    {
        return Department::where('type', Department::DEPARTMENT_SALE)
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
        $departmentIds = self::getDepartmentWithChildrenIds($this->id);

        if (!$date || DateHelper::isCurrentMonth($date)) {
            return User::whereIn('department_id', $departmentIds)
                ->with($relations)
                ->get();
        }

        $allHistoricalUsersQuery = User::getLatestHistoricalRecordsQuery($date)
            ->whereIn(DB::raw("CAST(JSON_UNQUOTE(JSON_EXTRACT(new_values, '$.department_id')) AS UNSIGNED)"), $departmentIds);

        return User::recreateFromQuery($allHistoricalUsersQuery, array_merge($relations, ['department']), $date);
    }

    protected function getUsersForDate(?Carbon $date = null, array $relations = []): SimpleCollection
    {
        if ($date) {
            $allHistoricalUsersQuery = User::getLatestHistoricalRecordsQuery($date)
                ->where('new_values->department_id', $this->id);

            return User::recreateFromQuery($allHistoricalUsersQuery, array_merge($relations, ['department']), $date);
        }

        return $this->users()->with($relations)->get();
    }
}
