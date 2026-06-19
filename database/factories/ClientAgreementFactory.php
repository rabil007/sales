<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientAgreement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClientAgreement>
 */
class ClientAgreementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 year', '+1 month');
        $durationDays = fake()->numberBetween(30, 365);
        $endDate = (clone $startDate)->modify('+'.max($durationDays - 1, 0).' days');

        return [
            'client_id' => Client::factory(),
            'agreement_ref' => fake()->unique()->regexify('[A-Z]{3}-[0-9]{4}-[0-9]{3}'),
            'scope_of_work' => fake()->paragraph(),
            'duration_days' => $durationDays,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'monthly_invoice_value' => fake()->randomFloat(2, 1000, 50000),
        ];
    }
}
