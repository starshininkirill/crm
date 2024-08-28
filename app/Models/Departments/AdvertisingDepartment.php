<?php

namespace App\Models\Departments;

use App\Classes\BaseDepartmentModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class AdvertisingDepartment extends BaseDepartmentModel
{
    use HasFactory;

    protected $fillable = ['name'];
    

    // Methods
    // department
    //getMainDepartment
}
