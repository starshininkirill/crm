<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    use HasFactory;

    public const INDIVIDUAL_SITE = 'individual_site';
    public const READY_SITE = 'ready_site';
    public const RK = 'rk';
    public const SEO = 'seo';
    public const OTHER = 'other';

    protected $fillable = ['name', 'type'];

    public function services()
    {
        return $this->hasMany(Service::class, 'service_category_id');
    }
}
