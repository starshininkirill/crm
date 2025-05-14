<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'fio',
        'phone',
        'sale',
        'amount_price',
        'parent_id',
        'client_id'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function childs()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role');
    }

    public function allUsers(?Carbon $date = null, array $relations = []): Collection
    {
        if (!$date) {
            return $this->users()->with($relations)->get();
        }

        $historicalUsers = $this->getHistoricalUsersForDate($date, $relations);

        return $historicalUsers;
    }

    protected function getHistoricalUsersForDate(Carbon $date, array $relations = []): Collection
    {
        $historicalContractUserIds = ContractUser::getLatestHistoricalRecordsQuery($date)
            ->whereNot('action', ContractUser::ACTION_DELETED)
            ->where('new_values->contract_id', $this->id)
            ->pluck('new_values')
            ->map(function ($data) {
                return $data['user_id'];
            });


        return User::getLatestHistoricalRecords($date, $relations)
            ->whereIn('id', $historicalContractUserIds);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function firstPayment()
    {
        return $this->payments()->whereOrder(1)->first();
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)->withPivot('price');
    }

    public function saller()
    {
        return $this->users()->wherePivot('role', ContractUser::SALLER)->first();
    }

    public function contractUsers()
    {
        return $this->hasMany(ContractUser::class);
    }

    public function getClientName()
    {
        if ($this->parent) {
            if ($this->parent->client) {
                return $this->parent->client->organization_short_name ?? $this->parent->client->fio;
            }
        }
        if ($this->client) {
            return $this->client->organization_name ?? $this->client->fio;
        }

        return '';
    }

    public function getInn()
    {
        if ($this->parent) {
            if ($this->parent->client) {
                return $this->parent->client->inn;
            }
        }
        if ($this->client) {
            return $this->client->inn;
        }

        return '';
    }


    public function attachUsersWithHistory($userId, $attributes = [])
    {
        $this->users()->attach($userId, $attributes);

        $pivotModel = $this?->pivot;

        if ($pivotModel) {
            $this->recordPivotHistory($pivotModel, 'created');
        }

        return $pivotModel;
    }

    protected function recordPivotHistory($pivotModel, $action)
    {
        History::create([
            'historyable_type' => get_class($pivotModel),
            'historyable_id' => $pivotModel->id,
            'action' => $action,
            'new_values' => $pivotModel->getAttributes(),
        ]);
    }

    public function addPayments(array $payments): void
    {
        collect($payments)
            ->filter()
            ->each(function ($payment, $index) {
                $this->payments()->create([
                    'value' => $payment,
                    'status' => Payment::STATUS_WAIT,
                    'order' => $index + 1,
                ]);
            });
    }

    public function getPerformers(): Collection
    {
        $roles = ContractUser::getRoles();

        $usersWithRoles = $this->users()->withPivot('role')->get();

        $groupedUsers = $usersWithRoles->groupBy('pivot.role');

        return $roles->map(function ($role) use ($groupedUsers) {
            return [
                'id' => $role,
                'name' => ContractUser::roleName($role),
                'performers' => $groupedUsers->get($role, collect()),
            ];
        });
    }
}
