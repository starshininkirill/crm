<?php

namespace App\Models\Finance;

use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAdjustment extends Model
{
    use HasFactory;

    public $timestamps = false;

    const PERIOD_FIRST_HALF = 'first_half';
    const PERIOD_SECOND_HALF = 'second_half';

    const TYPE_BONUS = 'bonus';
    const TYPE_PENALTY = 'penalty';

    const TYPES = [
        self::TYPE_BONUS,
        self::TYPE_PENALTY,
    ];

    const PERIODS = [
        self::PERIOD_FIRST_HALF,
        self::PERIOD_SECOND_HALF,
    ];

    protected $fillable = [
        'user_id',
        'type',
        'period',
        'value',
        'description',
        'date',
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
