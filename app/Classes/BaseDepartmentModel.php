<?php

namespace App\Classes;

use App\Models\Departments\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

abstract class BaseDepartmentModel extends Model
{
    public function department(): MorphOne
    {
        return $this->morphOne(Department::class, 'departmentable');
    }

    public function getParentDepartment(): ?Department
    {
        return Department::all()->first();
        // return $this->department()->where('parent_id', 0)->first();
    }
}
