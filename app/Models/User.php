<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Helpers\DateHelper;
use App\Models\Department;
use App\Models\Scopes\UserScope;
use App\Models\Traits\HasFilter;
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
    use HasApiTokens, HasFactory, Notifiable, HasHistory, HasFilter;

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
        'probation_start',
        'probation_end',
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
            'probation_start' => 'date',
            'probation_end' => 'date',
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

    public function getSalary(): int
    {
        if ($this->salary) {
            return $this->salary;
        }

        $position = $this->position;

        if (!$position) {
            return 0;
        }

        return $position->salary ?? 0;
    }

    public function lastAction(Carbon $date = null): HasOne
    {
        $date = $date ?? Carbon::now();

        return $this->hasOne(TimeCheck::class)
            ->whereDate('date', $date)
            ->latest('id');
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
}
