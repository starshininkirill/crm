<?php
namespace App\Http\Filters\Models;

use App\Http\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ServiceFilter extends Filter
{
    protected function name(string $value): Builder
    {
        return $this->builder->where('name', 'like', '%' . $value . '%');
    }

    protected function category(string $value): Builder
    {
        return $this->builder->where('service_category_id', $value);
    }
}
