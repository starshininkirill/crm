<?php

namespace App\Models\Departments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class AdvertisingDepartment extends Model
{
    use HasFactory;

    protected $fillable = ['name'];


    public function department(): MorphOne
    {
        return $this->morphOne(Department::class, 'departmentable');
    }
}