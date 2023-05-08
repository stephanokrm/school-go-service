<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Driver;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Itinerary>
 */
class ItineraryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'monday' => fake()->boolean(),
            'tuesday' => fake()->boolean(),
            'wednesday' => fake()->boolean(),
            'thursday' => fake()->boolean(),
            'friday' => fake()->boolean(),
            'morning' => fake()->boolean(),
            'afternoon' => fake()->boolean(),
            'night' => fake()->boolean(),
            'driver_id' => Driver::factory(),
            'school_id' => School::factory(),
        ];
    }
}
