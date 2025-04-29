<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'fields',
        'compensation',
        'is_another_recipient'
    ];

    protected $casts = [
        'fields' => 'array',
    ];

    

}
