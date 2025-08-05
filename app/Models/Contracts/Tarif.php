<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    use HasFactory;

    const TYPE_ADS = 'ads';
    const TYPE_SEO = 'seo';

    public $timestamps = false;

    
    protected $fillable = [
        'name',
        'minimal_price',
        'optimal_price',
        'description',
        'type'
    ];
}
