<?php

namespace App\Models\Global;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Note extends Model
{
    use HasFactory;

    const TYPE_TIME_SHEET = 'time_sheet';   

    const TYPES = [
        self::TYPE_TIME_SHEET,
    ];  

    protected $fillable = [
        'content',
        'date',
        'type',
        'noteable_id',
        'noteable_type',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
    ];

    public function noteable(): MorphTo
    {
        return $this->morphTo();
    }
}
