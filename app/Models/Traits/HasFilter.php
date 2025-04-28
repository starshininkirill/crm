<?php

namespace App\Models\Traits;

use App\Http\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait HasFilter
 *
 * @method static Builder filter(Filter $filter)
 */
trait HasFilter
{
    /**
     * @param Builder $builder
     * @param Filter $filter
     * @return Builder
     */
    public function scopeFilter(Builder $builder, Filter $filter): Builder
    {
        return $filter->apply($builder);
    }
}