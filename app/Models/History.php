<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class History extends Model
{
    use HasFactory;

    protected $casts = ['new_values' => 'array'];
    protected $fillable = ['historyable_type', 'historyable_id', 'action', 'new_values'];

    public function historyable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForModel($query, $model)
    {
        return $query->where('historyable_type', get_class($model))
            ->where('historyable_id', $model->id);
    }
}
