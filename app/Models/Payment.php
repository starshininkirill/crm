<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;
    // заменить
    protected $fillable = ['value', 'contract_id', 'status', 'order', 'confirmed_at', 'type', 'payment_method', 'is_technical', 'descr'];

    const STATUS_WAIT = 'open';
    const STATUS_CONFIRMATION = 'confirmation';
    const STATUS_CLOSE = 'close';

    const TYPE_NEW = 'new';
    const TYPE_OLD = 'old';

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    public function formatedType(): string
    {
        if($this->type == null){
            return '';
        }
        $statuses = [
            self::TYPE_NEW => 'Новые деньги',
            self::TYPE_OLD => 'Старые деньги'
        ];

        return $statuses[$this->type];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_WAIT => 'Ожидает оплату',
            self::STATUS_CONFIRMATION => 'Ожидает подтверждения',
            self::STATUS_CLOSE => 'Оплачен',
        ];
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }
    
    public function method():BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function generetePaymentMethodHierarchy(): string
    {
        $method = $this->method;
        if($method == null){
            return '';
        }
        if($method->parent == null){
            return $method->name;
        }

        $res = $method->parent->name;


        while($method->parent != null){
            $res = $res . ' / ' . $method->name;
            $method = $method->parent;
        }
        
        return $res;
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
