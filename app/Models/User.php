<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Helpers\DateHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_SALLER = 'saller';
    const ROLE_USER = 'user';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'position_id',
        'probation',
        'department_id'
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
            'password' => 'hashed',
        ];
    }


    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->first_name . ' ' . $this->last_name,
        );
    }

    public static function active(): Collection
    {
        return User::all();
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

    public function callStats(): HasMany
    {
        return $this->hasMany(CallStat::class, 'number', 'number');
    }

    public function getFirstWorkingDay(): Carbon
    {

        $employmentDate = $this->created_at;

        $workingDays = DateHelper::getWorkingDaysInMonth($employmentDate);

        while (!$workingDays->contains($employmentDate->format('Y-m-d'))) {
            $employmentDate->add(1, 'day');
        }

        return $employmentDate;
    }
    public function getMonthWorked(Carbon $date = null): int
    {
        $date = $date ?? Carbon::now();

        $employmentDate = $this->getFirstWorkingDay();
        if ($date->format('Y-m') == $employmentDate->format('Y-m')) {
            return 1;
        }

        $startWorkingDay = $employmentDate->format('d');

        $monthsWorked = $employmentDate->floorMonth()->diffInMonths($date->floorMonth()) + 1;
        if ($startWorkingDay > 7) {
            $monthsWorked--;
        }
        return $monthsWorked;
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
