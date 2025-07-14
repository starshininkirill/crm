<?php

namespace App\Models\Global;

use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallHistory extends Model
{
    protected $fillable = [
        'phone',
        'date',
        'client_phone',
        'conversation_duration',
        'type'
    ];

    public $timestamps = false;

    protected $casts = [
        'date' => 'datetime:Y-m-d',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'phone', 'phone');
    }
}
