<?php

namespace App\Models\States\Contract;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class ContractState extends State
{
    abstract public function name(): string;

    abstract public function order(): string;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Created::class);
    }
}
