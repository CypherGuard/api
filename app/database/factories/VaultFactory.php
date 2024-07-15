<?php

namespace App\Database\Factories;

use App\Models\Vault;

class VaultFactory extends Factory
{
    // If this model property isn't defined, Leaf will
    // try to generate the model name from the factory name
    public $vault = Vault::class;

    // You define your factory blueprint here
    // It should return an associative array
    public function definition(): array
    {
        return [
            'name' => strtolower($this->faker->name),
        ];
    }
}
