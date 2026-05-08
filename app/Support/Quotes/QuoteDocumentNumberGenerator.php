<?php

namespace App\Support\Quotes;

use App\Models\Quote;

final class QuoteDocumentNumberGenerator
{
    public function next(): string
    {
        $year = now()->format('Y');
        $latest = Quote::query()
            ->where('doc_no', 'like', "OMS-Q-{$year}-%")
            ->latest('id')
            ->value('doc_no');

        if (! is_string($latest)) {
            return "OMS-Q-{$year}-001";
        }

        $lastNumber = (int) substr($latest, -3);

        return sprintf('OMS-Q-%s-%03d', $year, $lastNumber + 1);
    }
}
