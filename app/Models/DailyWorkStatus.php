<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyWorkStatus extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending'; 
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'date',
        'user_id', 
        'work_status_id', 
        'hours', 
        'time_start', 
        'time_end',
        'links',
        'description',
        'report',
        'status',
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d',
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workStatus(): BelongsTo
    {
        return $this->belongsTo(WorkStatus::class);
    }
}
