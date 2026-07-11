<?php

namespace App\Support\Invoices;

use App\Models\Invoice;

final class InvoiceDocumentNumberGenerator
{
    public function next(): string
    {
        $year = now()->format('Y');
        $latest = Invoice::query()
            ->where('doc_no', 'like', "OMS-INV-{$year}-%")
            ->latest('id')
            ->value('doc_no');

        if (! is_string($latest)) {
            return "OMS-INV-{$year}-001";
        }

        $lastNumber = (int) substr($latest, -3);

        return sprintf('OMS-INV-%s-%03d', $year, $lastNumber + 1);
    }
}
