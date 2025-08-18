<?php

namespace App\Models\Contracts;

use App\Models\Finance\Payment;
use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceMonth extends Model
{
    use HasFactory;

    public $timestamps = false;

    const TYPE_ADS = 'ads';
    const TYPE_SEO = 'seo';

    protected $fillable = [
        'price',
        'contract_id',
        'tarif_id',
        'payment_id',
        'month',
        'payment_date',
        'start_service_date',
        'end_service_date',
        'type',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'start_service_date' => 'date',
        'end_service_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function tarif(): BelongsTo
    {
        return $this->belongsTo(Tarif::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
