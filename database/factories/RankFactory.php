<?php

namespace Database\Factories;

use App\Models\Rank;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rank>
 */
class RankFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Master',
                'Chief Officer',
                'AB Seaman',
                'Cook',
                'Crane Operator',
                'Rigger',
            ]),
            'category' => fake()->randomElement(['Marine', 'Catering', 'Technical', 'Offshore']),
            'default_basis' => fake()->randomElement(['Day', 'Month', 'Fixed']),
            'default_rate' => fake()->randomFloat(2, 200, 5000),
            'is_active' => true,
        ];
    }
}
