<?php

namespace App\Models;

use App\Models\Position;
use App\Models\User;
use App\Models\WorkPlan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SimpleCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    public const SALE_DEPARTMENT = 0;
    public const ADVERTISING_DEPARTMENT = 1;

    protected $fillable = ['name', 'parent_id', 'type'];

    public static function mainDepartments(): Collection
    {
        return Department::whereNull('parent_id')->get();
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

    public function allUsers(?Carbon $date = null, string $status = 'active'): SimpleCollection
    {
        $users = $this->getUsersForDate($date, $status);

        $this->childDepartments->each(function ($childDepartment) use (&$users, $status, $date) {
            $users = $users->merge($childDepartment->getUsersForDate($date, $status));
        });

        return $users;
    }

    protected function getUsersForDate(?Carbon $date, string $status): SimpleCollection
    {
        $query = $this->hasMany(User::class, 'department_id');

        if ($date && $date) {
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $userIds = History::where('historyable_type', User::class)
                ->whereDate('created_at', '<=', $endOfMonth)
                ->groupBy('historyable_id')
                ->pluck('historyable_id');

            $query->whereIn('id', $userIds);

            $users = $query->get()->filter(function ($user) use ($date, $status, $endOfMonth, $startOfMonth) {
                $version = $user->getVersionAtDate($date);


                if (!$version) {
                    return false;
                }

                $firedAt = $version->fired_at;

                switch ($status) {
                    case 'active':
                        return $firedAt === null || ($firedAt >= $startOfMonth && $firedAt <= $endOfMonth);
                    case 'fired':
                        return $firedAt !== null && $firedAt <= $endOfMonth;
                    case 'all':
                        return true;
                    default:
                        return $firedAt === null || $firedAt > $endOfMonth;
                }
            });

            return $users->map(function ($user) use ($date) {
                return $user->getVersionAtDate($date) ?? $user;
            });
        }

        switch ($status) {
            case 'active':
                $query->active();
                break;
            case 'fired':
                $query->fired();
                break;
            case 'all':
                break;
            default:
                $query->active();
        }

        return $query->get();
    }

    public static function getSaleDepartments()
    {
        return Department::where('type', Department::SALE_DEPARTMENT)
            ->get();
    }

    public static function getMainSaleDepartment(): ?Department
    {
        return Department::where('type', Department::SALE_DEPARTMENT)
            ->whereNull('parent_id')
            ->first();
    }
}
