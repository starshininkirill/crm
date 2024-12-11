<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    const TYPE_INDIVIDUAL = 0;
    const TYPE_LEGAL_ENTITY = 1;

    const TAX_OGRN = 0;
    const TAX_OGRNIP = 1;

    protected $fillable = [
        'type',
        'tax',
        'fio',
        'passport_series',
        'passport_number',
        'passport_issued',
        'physical_address',
        'organization_name',
        'organization_short_name',
        'register_number_type',
        'register_number',
        'director_name',
        'legal_address',
        'inn',
        'current_account',
        'correspondent_account',
        'bank_name',
        'bank_bik',
    ];

    public static function translateType(int $type) : string
    {
        $translates = [
            Client::TYPE_INDIVIDUAL => 'physic',
            Client::TYPE_LEGAL_ENTITY => 'law',
        ];

        return $translates[$type];
    }
    
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
 