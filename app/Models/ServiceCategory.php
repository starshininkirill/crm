<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    use HasFactory;

    public const INDIVIDUAL_SITE = 0;
    public const READY_SITE = 1;
    public const RK = 2;
    public const SEO = 3;
    public const OTHER = 4;

    protected $fillable = ['name', 'type'];


    public function services()
    {
        return $this->hasMany(Service::class, 'service_category_id');
    }
}
