<?php

namespace App\Support\Quotes;

final class CrewLineTotalsCalculator
{
    /**
     * @param  array<int, array<string, mixed>>  $validatedLines
     * @return array<int, array<string, mixed>>
     */
    public function normalize(array $validatedLines): array
    {
        return collect($validatedLines)
            ->filter(fn (array $line) => filled($line['rank'] ?? null))
            ->map(function (array $line): array {
                $lineTotal = $this->lineTotal($line);

                return [
                    ...$line,
                    'duration' => $this->resolveDuration($line),
                    'duration_days' => (int) ($line['duration_days'] ?? 0),
                    'duration_months' => (int) ($line['duration_months'] ?? 0),
                    'line_total' => $lineTotal,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $crewLines
     */
    public function total(array $crewLines): float
    {
        return (float) collect($crewLines)->sum(fn (array $line): float => (float) ($line['line_total'] ?? 0));
    }

    /**
     * @param  array<string, mixed>  $line
     */
    public function lineTotal(array $line): float
    {
        $qty = (float) ($line['qty'] ?? 0);
        $basis = (string) ($line['basis'] ?? 'Day');

        if ($basis === 'Month') {
            return $qty * (float) ($line['duration_months'] ?? 0) * (float) ($line['monthly_rate'] ?? 0);
        }

        if ($basis === 'Fixed') {
            return (float) ($line['manual_total'] ?? 0);
        }

        return $qty * (float) ($line['duration_days'] ?? ($line['duration'] ?? 0)) * (float) ($line['rate'] ?? 0);
    }

    /**
     * @param  array<string, mixed>  $line
     */
    public function resolveDuration(array $line): int
    {
        $basis = (string) ($line['basis'] ?? 'Day');

        return match ($basis) {
            'Month' => (int) ($line['duration_months'] ?? 0),
            'Fixed' => 1,
            default => (int) ($line['duration_days'] ?? ($line['duration'] ?? 0)),
        };
    }
}
