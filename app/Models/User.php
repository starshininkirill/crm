<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Department;
use App\Models\Scopes\UserScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Traits\HasHistory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection; 

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasHistory;

    const ROLE_ADMIN = 'admin';
    const ROLE_SALLER = 'saller';
    const ROLE_USER = 'user';

    protected $fillable = [
        'first_name',
        'last_name',
        'surname',
        'email',
        'phone',
        'work_phone',
        'probation',
        'role',
        'position_id',
        'department_id',
        'password',
        'fired_at',
        'bitrix_id',
        'salary',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['full_name'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'fired_at' => 'date',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        // static::addGlobalScope(new UserScope);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('fired_at');
    }

    public function scopeFired($query)
    {
        return $query->whereNotNull('fired_at');
    }

    public function scopeAll($query)
    {
        return $query;
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->first_name . ' ' . $this->last_name,
        );
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'responsible_id');
    }

    public function employmentDetail(): HasOne
    {
        return $this->hasOne(EmploymentDetail::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function contracts(): BelongsToMany
    {
        return $this->belongsToMany(Contract::class);
    }

    public function CallHistorys(): HasMany
    {
        return $this->hasMany(CallHistory::class, 'number', 'number');
    }

    public function timeChecks(): HasMany
    {
        return $this->hasMany(TimeCheck::class);
    }

    public function dailyWorkStatuses(): HasMany
    {
        return $this->hasMany(DailyWorkStatus::class);
    }

    public function lateWorkStatuses()
    {
        return $this->hasMany(DailyWorkStatus::class)
            ->where('status', DailyWorkStatus::STATUS_APPROVED)
            ->whereHas('workStatus', function ($q) {
                $q->where('type', WorkStatus::TYPE_LATE);
            });
    }

    public function fire()
    {
        $this->fired_at = Carbon::now();
        return $this->save();
    }

    public function salary(): int
    {
        $position = $this->position;

        if (!$position) {
            return 0;
        }

        return $position->salary ?? 0;
    }

    public function lastAction(Carbon $date = null): HasOne
    {
        if ($date) {
            return $this->hasOne(TimeCheck::class)
                ->whereDate('date', $date)
                ->orderByDesc('id');
        } else {
            return $this->hasOne(TimeCheck::class)
                ->whereDate('date', Carbon::now())
                ->orderByDesc('id');
        }
    }

    public function getLastAction(Carbon $date = null)
    {
        return $this->lastAction($date)->first();
    }

    public function monthlyClosePaymentsWithRelations(Carbon $date): Collection
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $contractIds = $this->contracts->pluck('id')->unique();

        return Payment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->whereIn('contract_id', $contractIds)
            ->where('status', Payment::STATUS_CLOSE)
            ->with(['contract.services.category'])
            ->get();
    }

    public static function monthlyClosePaymentsForRoleGroup(Carbon $date, array|Collection $userIds, int $role)
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        return Payment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('status', Payment::STATUS_CLOSE)
            ->whereHas('contract.contractUsers', function ($query) use ($userIds, $role) {
                $query->whereIn('user_id', $userIds)
                    ->where('role', $role);
            })
            ->with([
                'contract.services.category',
                'contract.users'
            ])
            ->get();
    }
}
