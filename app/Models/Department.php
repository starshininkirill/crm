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
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Department extends Model
{
    use HasFactory;

    public const SALE_DEPARTMENT = 0;
    public const ADVERTISING_DEPARTMENT = 1;

    protected $fillable = ['name', 'parent_id', 'type'];

    public static function getMainDepartments(): Collection
    {
        return Department::whereNull('parent_id')->get();
    }

    public function positions(): HasMany
    {
        if ($this->parent_id == null) {
            return $this->hasMany(Position::class);
        } else {
            return $this->parent()->first()->hasMany(Position::class);
        }
    }


    public function users(): SimpleCollection
    {
        $users = collect();

        $users = $users->merge($this->hasMany(User::class, 'department_id')->get());

        $this->childDepartments->each(function ($childDepartment) use (&$users) {
            $users = $users->merge($childDepartment->users());
        });

        return $users;
    }

    public function activeUsers(Carbon $date = null): SimpleCollection
    {
        $users = $this->users();

        if ($date != null) {
            $users = $users->where('created_at', '<=', $date->endOfMonth());
        }

        return $users;
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
