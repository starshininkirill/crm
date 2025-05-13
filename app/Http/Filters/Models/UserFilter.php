<?php

namespace App\Http\Filters\Models;

use App\Http\Filters\Filter;
use App\Models\Department;
use Illuminate\Database\Eloquent\Builder;

class UserFilter extends Filter
{
    protected function name(string $value): Builder
    {
        return $this->builder->where(function ($query) use ($value) {
            $query->where('first_name', 'like', '%' . $value . '%')
                ->orWhere('last_name', 'like', '%' . $value . '%');
        });
    }

    protected function department(string $value): Builder
    {
        $departmentIds = Department::getDepartmentWithChildrenIds($value);

        return $this->builder->whereIn('department_id', $departmentIds);
    }

    
}
