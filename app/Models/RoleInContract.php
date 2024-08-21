<?php

namespace App\Models;

use App\Models\Departments\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class RoleInContract extends Model
{
    use HasFactory;

    public const IS_SALLER = 1;

    protected $fillable = ['name', 'department_id'];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function getPerformers()
    {
        if ($this->department) {
            return new Collection($this->department->users());
        } 
        else {
            return User::all();
        }
    }
}
 