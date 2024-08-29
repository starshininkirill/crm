<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contract>
 */
class ContractFactory extends Factory
{
    protected $model = Contract::class;

    public function definition(): array
    {
        return [
            'number' => strtoupper($this->faker->unique()->bothify('##??##')),
            'amount_price' => $this->faker->numberBetween(10000, 50000),
            'comment' => $this->faker->sentence,
            'client_id' => Client::factory(),
        ];
    }
}