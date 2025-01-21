<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ServiceCategory extends Model
{
    use HasFactory;

    public const INDIVIDUAL_SITE = 0;
    public const READY_SITE = 1;
    public const RK = 2;
    public const SEO = 3;
    public const OTHER = 4;

    public const ARRAY_TYPES = [
        self::INDIVIDUAL_SITE => 'Сайты с индивидуальным дизайном',
        self::READY_SITE => 'Сайты с готовым дизайном',
        self::RK => 'Реклама',
        self::SEO => 'СЕО',
        self::OTHER => 'Иное',
    ];

    public const REVERCE_ARRAY_TYPES = [
        'Сайты с индивидуальным дизайном' => self::INDIVIDUAL_SITE,
        'Сайты с готовым дизайном' => self::READY_SITE,
        'Реклама' => self::RK,
        'СЕО' => self::SEO,
        'Иное' => self::OTHER
    ];



    protected $fillable = ['name', 'type'];


    public function services()
    {
        return $this->hasMany(Service::class, 'service_category_id');
    }

    public static function getUnusedTypes(bool $reverce = false): Collection
    {
        $services = self::whereNotNull('type')->get()->pluck('type')->toArray();
        return collect(array_filter(
            self::ARRAY_TYPES,
            function ($value, $key) use ($services) {
                return !in_array($key, $services);
            },
            ARRAY_FILTER_USE_BOTH
        ));
    }

    public static function getTypes(bool $reverce = false): Collection
    {
        return collect(self::ARRAY_TYPES);
    }

    public static function getReadableType(int $typeId): string
    {
        return self::ARRAY_TYPES[$typeId] ?? null;
    }
    public function readableType(): string|null
    {
        return self::ARRAY_TYPES[$this->type] ?? null;
    }
}
