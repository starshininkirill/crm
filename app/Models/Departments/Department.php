<?php

namespace App\Models\Departments;

use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'parent_id'];

    public function positions(){
        return $this->hasMany(Position::class);
    }

    
    public function departmentable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function getParentDepartments()
    {
        $departments = Department::where('parent_id', null)->get();

        return $departments->pluck('departmentable');
    }

    public function childDepartments(): HasMany
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

}
 
