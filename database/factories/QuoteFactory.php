<?php

namespace Database\Factories;

use App\Models\Quote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quote>
 */
class QuoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $issueDate = fake()->dateTimeBetween('-30 days', 'now');

        return [
            'doc_no' => sprintf('OMS-Q-%s-%03d', now()->format('Y'), fake()->unique()->numberBetween(1, 999)),
            'type' => fake()->randomElement(['Proposal', 'Rate Contract']),
            'issue_date' => $issueDate,
            'expiry_date' => fake()->optional()->dateTimeBetween($issueDate, '+90 days'),
            'status' => 'Draft',
            'currency' => 'AED',
            'client_name' => fake()->company(),
            'client_po' => fake()->optional()->bothify('PO-#####'),
            'vessel' => fake()->optional()->words(2, true),
            'location' => fake()->optional()->city(),
            'start_date' => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'end_date' => fake()->optional()->dateTimeBetween('+31 days', '+180 days'),
            'payment_terms' => fake()->optional()->randomElement([
                '30 days from invoice',
                '45 days from invoice',
                'Advance payment',
            ]),
            'scope' => fake()->optional()->sentence(12),
            'total_amount' => 0.00,
        ];
    }
}
