<?php

namespace App\Http\Filters\Models;

use App\Http\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class DocumentTemplateFilter extends Filter
{
    protected function templateId(string $value): Builder
    {
        return $this->builder->where('template_id', 'like', $value . '%');
    }
}
