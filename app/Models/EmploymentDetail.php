<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EmploymentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'employment_type_id',
        'details',
        'payment_account',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function employmentType(): BelongsTo
    {
        return $this->belongsTo(EmploymentType::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
