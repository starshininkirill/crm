<?php

namespace App\Models\UserManagement;

use App\Models\Traits\HasHistory;
use App\Models\Global\WorkPlan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Position extends Model
{
    use HasFactory, HasHistory;

    protected $fillable = ['name', 'salary', 'has_probation'];


    public function users() : HasMany
    {
        return $this->hasMany(User::class);
    }

    public function workPlan(): HasOne
    {
        return $this->hasOne(WorkPlan::class);
    }

}
