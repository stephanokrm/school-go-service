<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 *
 */
class SchoolFactory extends Factory
{
    /**
     * @return array
     */
    public function definition(): array
    {
        $morning = fake()->boolean();
        $afternoon = fake()->boolean();
        $night = fake()->boolean();

        $morningDepartureTime = $morning ? fake()->time() : null;
        $afternoonDepartureTime = $afternoon ? fake()->time() : null;
        $nightDepartureTime = $night ? fake()->time() : null;

        return [
            'name' => fake()->company(),
            'morning' => $morning,
            'afternoon' => $afternoon,
            'night' => $night,
            'morning_entry_time' => $morning ? fake()->time('H:i:s', $morningDepartureTime) : null,
            'morning_departure_time' => $morningDepartureTime,
            'afternoon_entry_time' => $afternoon ? fake()->time('H:i:s', $afternoonDepartureTime) : null,
            'afternoon_departure_time' => $afternoonDepartureTime,
            'night_entry_time' => $night ? fake()->time('H:i:s', $nightDepartureTime) : null,
            'night_departure_time' => $nightDepartureTime,
            'address_id' => Address::factory(),
        ];
    }
}
