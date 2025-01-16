<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Payment extends Model
{
    use HasFactory;


    protected $fillable = ['value', 'inn', 'contract_id', 'status', 'order', 'confirmed_at', 'type', 'payment_method', 'is_technical', 'descr'];

    const STATUS_WAIT = 0;
    const STATUS_CONFIRMATION = 1;
    const STATUS_CLOSE = 2;

    const TYPE_NEW = 0;
    const TYPE_OLD = 1;

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    public function formatedType(): string
    {
        if ($this->type === null) {
            return '';
        }
        $statuses = [
            self::TYPE_NEW => 'Новые деньги',
            self::TYPE_OLD => 'Старые деньги'
        ];

        return $statuses[$this->type];
    }

    public static function vueStatuses(): array
    {
        return [
            'wait' => self::STATUS_WAIT,
            'confirmation' => self::STATUS_CONFIRMATION,
            'close' => self::STATUS_CLOSE,
        ];
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

    public function method(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function generetePaymentMethodHierarchy(): string
    {
        $method = $this->method;
        if ($method == null) {
            return '';
        }
        if ($method->parent == null) {
            return $method->name;
        }

        $res = $method->parent->name;


        while ($method->parent != null) {
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

    public static function getContractsByPaymentsWithRelations(Collection $payments): Collection
    {
        $uniqueIds = $payments->pluck('contract_id')->unique();
        return Contract::whereIn('id', $uniqueIds)
            ->with([
                'services.category',
                'users'
            ])
            ->get();
    }

    public static function getMonthlyPayments(Carbon $date): Collection
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        return Payment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('status', Payment::STATUS_CLOSE)
            ->with([
                'contract.services.category',
                'contract.users'
            ])
            ->get();
    }

    public static function getMonthlyPaymentsByUserGroup(Carbon $date, Collection $users, $role): Collection
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        return Payment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('status', Payment::STATUS_CLOSE)
            ->with([
                'contract.services.category',
                'contract.users'
            ])
            ->get();
    }
}
