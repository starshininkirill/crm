<?php

namespace App\Models;

use App\Helpers\TextFormaterHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'service_category_id', 'price', 'description', 'deal_template_ids', 'work_days_duration'];

    protected $casts = [
        'deal_template_ids' => 'array',
    ];

    public function contracts()
    {
        return $this->belongsToMany(Contract::class);
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function dealTemplateId(bool $isIndividual, bool $isDefault)
    {
        $searchString = $isIndividual ? 'physic_' : 'law_';
        $searchString = $isDefault ? $searchString . 'default' : $searchString . 'complex';
        return json_decode($this->deal_template_ids, true)[$searchString];
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
 