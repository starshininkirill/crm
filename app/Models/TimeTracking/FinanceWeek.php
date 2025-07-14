<?php

namespace App\Models\TimeTracking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceWeek extends Model
{
    use HasFactory;

    protected $fillable = ['date_start', 'date_end', 'weeknum'];
    public $timestamps = false;

    
}
