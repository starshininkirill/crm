<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallStat extends Model
{
    protected $fillable = [
        'phone',
        'date',
        'income',
        'outcome',
        'duration',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'phone', 'phone');
    }

}