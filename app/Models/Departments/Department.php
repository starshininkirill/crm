<?php

namespace App\Models\Departments;

use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SimgpeCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'parent_id'];
    

    public static function getMainDepartmentsInstanses(): Collection
    {
        $departments = self::getMainDepartments();

        $departmentsInstanses = $departments->map(function ($department) {
            return $department->departmentable;
        });
    
        return new Collection($departmentsInstanses);
    }

    public static function getMainDepartments(): Collection
    {
        return Department::whereNull('parent_id')->get();
    }

    public function positions(): HasMany
    {
        if($this->parent_id == null){
            return $this->hasMany(Position::class);   
        }else{
            return $this->parent()->first()->hasMany(Position::class);
        }
    }


    public function users(): SimgpeCollection
    {
        $users = collect();
        
        $users = $users->merge($this->hasMany(User::class, 'department_id')->get());

        $this->childDepartments->each(function ($childDepartment) use (&$users) {
            $users = $users->merge($childDepartment->users());
        });

        return $users;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }
    
    public function departmentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function childDepartments(): HasMany
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

}
 
