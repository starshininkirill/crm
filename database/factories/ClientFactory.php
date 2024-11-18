<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement([0, 1]); // 0 - физическое лицо, 1 - юридическое лицо

        return [
            'type' => $type,
            'tax' => $this->faker->randomElement([0, 1]), // 0 - без НДС, 1 - с НДС

            // Поля для физического лица
            'fio' => $type === 0 ? $this->faker->name : null,
            'passport_series' => $type === 0 ? $this->faker->numerify('####') : null,
            'passport_number' => $type === 0 ? $this->faker->numerify('######') : null,
            'passport_issued' => $type === 0 ? $this->faker->sentence : null,
            'physical_address' => $type === 0 ? $this->faker->address : null,

            // Поля для юридического лица
            'organization_name' => $type === 1 ? $this->faker->company : null,
            'organization_short_name' => $type === 1 ? $this->faker->companySuffix : null,
            'register_number_type' => $type === 1 ? $this->faker->randomElement([0, 1]) : null, // 0 - ОГРН, 1 - ОГРНИП
            'register_number' => $type === 1 ? $this->faker->numerify('#############') : null,
            'director_name' => $type === 1 ? $this->faker->name : null,
            'legal_address' => $type === 1 ? $this->faker->address : null,
            'inn' => $type === 1 ? $this->faker->numerify('##########') : null,
            'current_account' => $type === 1 ? $this->faker->numerify('################') : null,
            'correspondent_account' => $type === 1 ? $this->faker->numerify('################') : null,
            'bank_name' => $type === 1 ? $this->faker->company : null,
            'bank_bik' => $type === 1 ? $this->faker->numerify('########') : null,
        ];
    }
}