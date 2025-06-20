<?php

namespace App\Http\Filters\Models;

use App\Http\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class GeneratedDocumentFilter extends Filter
{
    protected function name(string $value): Builder
    {
        return $this->builder->where('file_name', 'like', '%' . $value . '%');
    }

    protected function type(string $value): Builder
    {
        return $this->builder->where('type', $value);
    }

    protected function date(string $value): Builder
    {
        return $this->builder->whereDate('created_at', $value);
    }
}
