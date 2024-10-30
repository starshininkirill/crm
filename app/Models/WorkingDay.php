<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingDay extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'is_working_day'];
    public $timestamps = false;

    public const WEEKEND_DAY = 0;
    public const WORKING_DAY = 1;

    public function isWorkingDay(): bool
    {
        return $this->is_working_day;
    }
}
