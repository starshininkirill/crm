<?php

namespace App\Models;

use App\Models\Scopes\WorkStatusExcludeScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkStatus extends Model
{
    use HasFactory;

    const TYPE_SICK_LEAVE = 'sick_leave';
    const TYPE_HOMEWORK = 'homework';
    const TYPE_PART_TIME_DAY = 'part_time_day';
    const TYPE_OWN_DAY = 'own_day';
    const TYPE_VACATION = 'vacation';
    const TYPE_LATE = 'late';
    const TYPE_OVERWORK = 'overwork';

    // Типы которые по умолчанию удаляем из запроса
    public $excludeTypes = [
        self::TYPE_LATE,
        self::TYPE_OVERWORK,
    ];

    protected $fillable = ['name', 'type', 'hours', 'need_confirmation'];
    
    public $timestamps = false;

    protected static function booted(): void
    {
        static::addGlobalScope(new WorkStatusExcludeScope);
    }

    public static function lateStatuses()
    {
        return self::withoutGlobalScope(WorkStatusExcludeScope::class)
            ->where('type', self::TYPE_LATE);
    }

    public static function overworkStatuses()
    {
        return self::withoutGlobalScope(WorkStatusExcludeScope::class)
            ->where('type', self::TYPE_OVERWORK);
    }
}
