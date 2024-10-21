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
use Illuminate\Support\Collection;
use PhpParser\ErrorHandler\Collecting;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_SALLER = 'saller';
    const ROLE_USER = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'position_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'responsible_id');
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

    public function getFirstWorkingDay(): Carbon
    {

        $employmentDate = $this->created_at;

        $workingDays = DateHelper::getWorkingDaysInMonth($employmentDate);

        while (!in_array($employmentDate->format('Y-m-d'), $workingDays)) {
            $employmentDate->add(1, 'day');
        }

        return $employmentDate;
    }
    public function getMounthWorked(): int
    {
        $employmentDate = $this->getFirstWorkingDay();
        $nowDate = Carbon::now();

        $startWorkingDay = $employmentDate->format('d');

        $monthsWorked = $employmentDate->floorMonth()->diffInMonths($nowDate->floorMonth()) + 1;
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
}
