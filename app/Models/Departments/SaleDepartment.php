<?php

namespace App\Models\Departments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class SaleDepartment extends BaseDepartmentModel
{
    use HasFactory;

    protected $fillable = ['name'];

    // Methods
    // department
    //getMainDepartment
}
