<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'service_category_id', 'price', 'description', 'deal_template_ids'];

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
}
