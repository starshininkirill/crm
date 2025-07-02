<?php

namespace App\Models\States\Contract;

use App\Models\States\Contract\ContractState;

class Created extends ContractState
{
    public static $name = 'Создан';

    public function name(): string
    {
        return 'Создан';
    }

    public function order(): string
    {
        return 1;
    }
}
