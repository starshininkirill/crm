<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NumberStat extends Model
{
    protected $fillable = [
        'number',
        'date',
        'income',
        'outcome',
        'duration',
    ];

}