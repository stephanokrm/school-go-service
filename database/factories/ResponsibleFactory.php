<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 *
 */
class ResponsibleFactory extends Factory
{
    /**
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
        ];
    }
}
