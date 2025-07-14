<?php

namespace App\Models\States\Contract;

use App\Models\Contracts\Contract;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class ContractState extends State
{
    abstract public function name(): string;

    abstract public function order(): string;

    public static function getStates(): array
    {
        $allStates = collect(static::all());

        $statesWithData = $allStates->map(function (string $stateClass) {
            $stateInstance = new $stateClass(new Contract());

            return [
                'name' => $stateInstance->name(),
                'order' => $stateInstance->order(),
            ];
        });

        return $statesWithData->sortBy('order')->values()->toArray();
    }

    public static function config(): StateConfig
    {
        return parent::config()
            ->allowAllTransitions()
            ->default(Created::class);
    }
}
