<?php

use App\Models\Quote;
use App\Models\QuoteCrewLine;

it('applies quote defaults from schema', function () {
    $quote = Quote::factory()->create([
        'doc_no' => 'OMS-Q-2026-001',
        'type' => 'Proposal',
        'issue_date' => now()->toDateString(),
        'client_name' => 'Acme Marine LLC',
    ]);

    expect($quote->status)->toBe('Draft')
        ->and($quote->currency)->toBe('AED')
        ->and((float) $quote->total_amount)->toBe(0.0);
});

it('links quotes and crew lines using defined relationships', function () {
    $quote = Quote::factory()->create();

    $crewLine = QuoteCrewLine::factory()->create([
        'quote_id' => $quote->id,
        'rank' => 'Master',
    ]);

    expect($quote->crewLines()->count())->toBe(1)
        ->and($quote->crewLines()->first()?->id)->toBe($crewLine->id)
        ->and($crewLine->quote->id)->toBe($quote->id);
});
