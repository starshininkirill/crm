<?php

namespace App\Models;

use App\Helpers\TextFormaterHelper;
use App\Models\Traits\HasFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory, HasFilter;

    protected $fillable = ['name', 'service_category_id', 'price', 'description', 'work_days_duration'];

    public function contracts()
    {
        return $this->belongsToMany(Contract::class);
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_service_document_template')
                    ->withPivot('document_template_id', 'type');
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function numeric_working_days() : int
    {
        if($this->work_days_duration){
            return TextFormaterHelper::getNumberFromString($this->work_days_duration);
        }else{
            return 0;
        };
    }
}
 