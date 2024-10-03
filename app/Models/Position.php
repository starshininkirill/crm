<?php

namespace App\Models;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Position extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'salary', 'department_id'];

    public function department() : BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function users() : HasMany
    {
        return $this->hasMany(User::class);
    }

    public function workPlan(): HasOne
    {
        return $this->hasOne(WorkPlan::class);
    }

}
