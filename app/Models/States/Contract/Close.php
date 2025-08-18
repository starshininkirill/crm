<?php

namespace App\Models\States\Contract;

use App\Models\States\Contract\ContractState;

class Close extends ContractState
{
    public static $name = 'Завершен';

    public function name(): string
    {
        return 'Завершен';
    }
    public function order(): int
    {
        return 4;
    }
}
