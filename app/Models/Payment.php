<?php

namespace App\Models;

use App\Models\Traits\HasHistory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Payment extends Model
{
    use HasFactory, HasHistory;

    protected $fillable = [
        'value',
        'inn',
        'contract_id',
        'status',
        'order',
        'confirmed_at',
        'created_at',
        'type',
        'is_technical',
        'organization_id',
        'description',
        'receipt_url',
        'operation_id'
    ];

    const STATUS_WAIT = 0;
    const STATUS_WAIT_CONFIRMATION = 1;
    const STATUS_CLOSE = 2;

    const TYPE_NEW = 0;
    const TYPE_OLD = 1;

    const STATUSES = [
        self::STATUS_WAIT,
        self::STATUS_WAIT_CONFIRMATION,
        self::STATUS_CLOSE,
    ];

    const ASOC_STATUSES = [
        self::STATUS_WAIT => 'Ожидает оплаты',
        self::STATUS_WAIT_CONFIRMATION => 'Ожидает распределения',
        self::STATUS_CLOSE => 'Оплачен'
    ];

    const TYPES = [
        self::TYPE_NEW,
        self::TYPE_OLD
    ];

    const ASOC_TYPES = [
        self::TYPE_NEW => 'Новые деньги',
        self::TYPE_OLD => 'Старые деньги',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function determineType(Payment $newPayment): int
    {
        if ($this->order == 1) {
            return self::TYPE_NEW;
        }

        $firstPayment = $this->contract->firstPayment();

        if (!$firstPayment) {
            return self::TYPE_NEW;
        }

        if ($firstPayment->created_at->format('Y-m') == $newPayment->created_at->format('Y-m')) {
            return self::TYPE_NEW;
        }

        return self::TYPE_OLD;
    }


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
            'confirmation' => self::STATUS_WAIT_CONFIRMATION,
            'close' => self::STATUS_CLOSE,
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_WAIT => 'Ожидает оплату',
            self::STATUS_WAIT_CONFIRMATION => 'Ожидает подтверждения',
            self::STATUS_CLOSE => 'Оплачен',
        ];
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
