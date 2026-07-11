<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
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
            'doc_no' => sprintf('OMS-INV-%s-%03d', now()->format('Y'), fake()->unique()->numberBetween(1, 999)),
            'quote_id' => null,
            'client_id' => null,
            'client_name' => fake()->company(),
            'client_po' => fake()->optional()->bothify('PO-#####'),
            'vessel' => fake()->optional()->words(2, true),
            'location' => fake()->optional()->city(),
            'project_name' => fake()->optional()->words(3, true),
            'issue_date' => $issueDate,
            'due_date' => fake()->dateTimeBetween($issueDate, '+30 days'),
            'status' => 'Draft',
            'currency' => 'AED',
            'subtotal' => 1000.00,
            'tax_rate' => 0.00,
            'tax_amount' => 0.00,
            'total_amount' => 1000.00,
            'notes' => fake()->optional()->sentence(),
            'payment_instructions' => 'Please pay within 30 days.',
        ];
    }
}
