<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $qty = fake()->numberBetween(1, 5);
        $rate = fake()->randomFloat(2, 100, 500);
        $duration = fake()->numberBetween(1, 30);

        return [
            'invoice_id' => Invoice::factory(),
            'description' => fake()->jobTitle(),
            'category' => 'Marine',
            'qty' => $qty,
            'basis' => 'Day',
            'rate' => $rate,
            'duration' => $duration,
            'duration_unit' => 'Days',
            'line_total' => round($qty * $rate * $duration, 2),
        ];
    }
}
