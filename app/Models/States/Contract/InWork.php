<?php

namespace App\Models\States\Contract;

use App\Models\States\Contract\ContractState;

class InWork extends ContractState
{
    public function name(): string
    {
        return 'В работе';
    }
    public function order(): string
    {
        return 2;
    }
}
