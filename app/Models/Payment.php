<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['value', 'contract_id', 'status', 'order'];

    const STATUS_OPEN = 'open';
    const STATUS_CONFIRMATION = 'confirmation';
    const STATUS_CLOSE = 'close';

    public static function getStatuses()
    {
        return [
            self::STATUS_OPEN => 'Ожидает оплату',
            self::STATUS_CONFIRMATION => 'Ожидает подтверждения',
            self::STATUS_CLOSE => 'Оплачен',
        ];
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function getStatusNameAttribute()
    {
        $statuses = self::getStatuses();
        return $statuses[$this->status] ?? $this->status;
    }

    public function getFormatValue()
    {
        return number_format($this->value, 0, '.', ' ') . ' ₽';
    }
}
