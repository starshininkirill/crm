<?php

namespace App\Models\States\Contract;

use App\Models\States\Contract\ContractState;

class Introduction extends ContractState
{
    public static string $name = 'introduction';

    public function name(): string
    {
        return 'Ведение';
    }

    public function order(): int
    {
        return 2;
    }
} 