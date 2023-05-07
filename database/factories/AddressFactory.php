<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 *
 */
class AddressFactory extends Factory
{
    /**
     * @return array
     */
    public function definition(): array
    {
        return [
            'description' => fake()->address(),
            'place_id' => fake()->uuid(),
        ];
    }
}
