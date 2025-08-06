<?php

namespace App\Models\States\Contract;

use App\Models\Contracts\Contract;
use App\Models\States\Contract\ContractState;
use Spatie\ModelStates\State;

class Created extends ContractState
{
    public static $name = 'Создан';

    public function name(): string
    {
        return 'Создан';
    }

    public function order(): int
    {
        return 1;
    }
}
