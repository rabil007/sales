<?php

namespace App\Support\Invoices;

final class InvoiceTotalsCalculator
{
    /**
     * @param  array<int, array<string, mixed>>  $validatedItems
     * @return array<int, array<string, mixed>>
     */
    public function normalizeItems(array $validatedItems): array
    {
        return collect($validatedItems)
            ->filter(fn (array $item) => filled($item['description'] ?? null))
            ->map(function (array $item): array {
                $qty = (float) ($item['qty'] ?? 1);
                $rate = (float) ($item['rate'] ?? 0);
                $duration = (float) ($item['duration'] ?? 1);
                $lineTotal = round($qty * $rate * ($duration > 0 ? $duration : 1), 2);

                return [
                    ...$item,
                    'qty' => (int) $qty,
                    'rate' => $rate,
                    'duration' => $duration,
                    'line_total' => $lineTotal,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    public function subtotal(array $items): float
    {
        return (float) collect($items)->sum(fn (array $item): float => (float) ($item['line_total'] ?? 0));
    }

    public function taxAmount(float $subtotal, float $taxRate): float
    {
        return round($subtotal * ($taxRate / 100), 2);
    }

    public function total(float $subtotal, float $taxAmount): float
    {
        return round($subtotal + $taxAmount, 2);
    }
}
