<?php

namespace App\Models;

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
        return $this->belongsToMany(User::class);
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

        $performers = $this->belongsToMany(User::class, 'contract_user')
            ->get()
            ->groupBy(function ($performer) {
                return $performer->pivot->role;
            });

        $result = $roles->map(function ($role) use ($performers) {
            return [
                'role' => $role,
                'role_name' => ContractUser::roleName($role),
                'performers' => $performers->get($role, collect())
            ];
        });

        return $result;
    }

    public function attachPerformer(int $userId, int $roleId): bool
    {
        $exists = $this->users()
            ->wherePivot('user_id', $userId)
            ->wherePivot('role', $roleId)
            ->exists();

        if (!$exists) {
            $this->users()->attach($userId, [
                'role' => $roleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return true;
        }
        return false;
    }
}
