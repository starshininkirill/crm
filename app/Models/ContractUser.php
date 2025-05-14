<?php

namespace App\Models;

use App\Models\Traits\HasHistory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Collection;

class ContractUser extends Pivot
{

    use HasHistory;

    protected $table = 'contract_user';

    public $incrementing = true;

    protected $fillable = [
        'contract_id',
        'user_id',
        'role'
    ];

    public const SALLER = 0;
    public const PROJECT = 1;
    public const DESIGNER = 2;
    public const PROGRAMMER = 3;
    public const CM = 4;
    public const DIRECTOLOG = 5;
    public const SEO_PM = 6;
    public const SEO_TEH = 7;

    public static function getRoles(): Collection
    {
        return collect([
            self::SALLER,
            self::PROJECT,
            self::DESIGNER,
            self::PROGRAMMER,
            self::CM,
            self::DIRECTOLOG,
            self::SEO_PM,
            self::SEO_TEH
        ]);
    }

    public static function roleName(int $role): string
    {
        $roles = [
            self::SALLER => 'Продажник',
            self::PROJECT => 'Проект Менеджер',
            self::DESIGNER => 'Дизайнер',
            self::PROGRAMMER => 'Програмист',
            self::CM => 'Контент Менеджер',
            self::DIRECTOLOG => 'Директолог',
            self::SEO_PM => 'SEO ПМ',
            self::SEO_TEH => 'SEO Тех. специалист',
        ];
    
        return $roles[$role] ?? 'Неизвестная роль';
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
