<?php

namespace App\Models\States\Contract;

use App\Models\States\Contract\ContractState;

class Paused extends ContractState
{
    public static string $name = 'paused';

    public function name(): string
    {
        return 'Заморозка';
    }

    public function order(): int
    {
        return 3;
    }
} 