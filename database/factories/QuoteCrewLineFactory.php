<?php

namespace Database\Factories;

use App\Models\Quote;
use App\Models\QuoteCrewLine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuoteCrewLine>
 */
class QuoteCrewLineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quote_id' => Quote::factory(),
            'rank' => fake()->optional()->randomElement(['Master', 'Chief Engineer', 'AB Seaman']),
            'category' => 'Marine',
            'qty' => 1,
            'basis' => 'Day',
            'rate' => fake()->randomFloat(2, 500, 5000),
            'duration' => fake()->numberBetween(0, 120),
            'ot_rate' => 0.00,
            'mob_date' => fake()->optional()->dateTimeBetween('now', '+60 days'),
            'remarks' => fake()->optional()->sentence(),
        ];
    }
}
