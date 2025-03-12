<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkStatus extends Model
{
    use HasFactory;

    const TYPE_SICK_LEAVE = 'sick_leave';
    const TYPE_HOMEWORK = 'homework';
    const TYPE_PART_TIME_DAY = 'part_time_day';


    protected $fillable = ['name', 'type', 'hours', 'need_confirmation'];

    public $timestamps = false;
}
