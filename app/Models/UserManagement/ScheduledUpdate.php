<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ScheduledUpdate extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_APPLIED = 'applied';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'updatable_id',
        'updatable_type',
        'new_value',
        'effective_date',
        'status',
        'field',
    ];

    public function updatable(): MorphTo
    {
        return $this->morphTo();
    }
}
