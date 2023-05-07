<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Responsible;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 *
 */
class StudentFactory extends Factory
{
    /**
     * @return array
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'goes' => fake()->boolean(),
            'return' => fake()->boolean(),
            'morning' => fake()->boolean(),
            'afternoon' => fake()->boolean(),
            'night' => fake()->boolean(),
            'address_id' => Address::factory(),
            'responsible_id' => Responsible::factory(),
            'school_id' => School::factory(),
        ];
    }
}
