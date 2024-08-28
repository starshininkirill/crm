<?php

namespace App\Models\Departments;

use App\Classes\BaseDepartmentModel;
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

    public static function getMainDepartment(): ?Department
    {
        $depatrment = SaleDepartment::first()->department;

        if(!$depatrment){
            return null;
        }
        
        while($depatrment->parent != null){
            $depatrment = $depatrment->parent;
        }
        
        return $depatrment;
    }
}
