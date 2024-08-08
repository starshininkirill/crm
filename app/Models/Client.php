<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'email',
        'company',
        'inn',
        'phone',
    ];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
