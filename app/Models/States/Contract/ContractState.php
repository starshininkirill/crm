<?php

namespace App\Models\States\Contract;

use App\Models\Contracts\Contract;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class ContractState extends State
{
    abstract public function name(): string;

    abstract public function order(): int;

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
            ->default(Created::class)
            ->registerState(Created::class)
            ->registerState(Introduction::class)
            ->registerState(Paused::class)
            ->registerState(Close::class);
    }

    public static function getStatesForType(string $type): array
    {
        $states = match ($type) {
            Contract::TYPE_SITE => [
                Created::class,
                Close::class,
            ],
            Contract::TYPE_ADS => [
                Created::class,
                Introduction::class,
                Paused::class,
                Close::class,
            ],
            Contract::TYPE_SEO => [
                Created::class,
                Introduction::class,
                Paused::class,
                Close::class,
            ],
            default => [Created::class, Close::class],
        };

        return collect($states)->map(function (string $stateClass) {
            $stateInstance = new $stateClass(new Contract());
            return [
                'name' => $stateInstance->name(),
                'order' => $stateInstance->order(),
                'class' => $stateClass,
            ];
        })->sortBy('order')->values()->toArray();
    }
}
