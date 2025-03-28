<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeCheck extends Model
{
    use HasFactory;

    const DEFAULT_DAY_START = '09:01:00';
    const DEFAULT_DAY_END = '18:00:00';
    const DEAFULT_BREAKTIME = '01:20:00';
    const DEFAULT_WORKING_DAY_DURATION = 9;

    public $timestamps = false;

    public $fillable = [
        'user_id',
        'date',
        'action',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    const ACTION_START = 'start';
    const ACTION_END = 'end';
    const ACTION_PAUSE = 'pause';
    const ACTION_CONTINUE = 'continue';

    const ACTIONS = [
        self::ACTION_START,
        self::ACTION_END,
        self::ACTION_PAUSE,
        self::ACTION_CONTINUE,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
