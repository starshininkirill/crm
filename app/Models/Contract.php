<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'number',
        'client_id',
        'amount_price',
        'comment',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'parent_id');
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

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    public function getPrice(): string
    {
        return number_format($this->amount_price, 0, '.', ' ') . ' ₽';
    }

    public function saller()
    {
        return $this->users()->wherePivot('role', ContractUser::SALLER)->first();
    }
    public function contractUsers()
{
    return $this->hasMany(ContractUser::class);
}
}
