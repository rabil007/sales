<?php

use App\Models\Quote;
use App\Models\User;

test('authenticated user can view quote pages', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('dashboard'))->assertSuccessful();
    $this->get(route('quotes.index'))->assertSuccessful();
    $this->get(route('quotes.create'))->assertSuccessful();
});

test('user can create a quote with crew lines', function () {
    $this->actingAs(User::factory()->create());

    $response = $this->post(route('quotes.store'), [
        'doc_no' => 'OMS-Q-2026-111',
        'type' => 'Proposal',
        'issue_date' => now()->toDateString(),
        'status' => 'Draft',
        'currency' => 'AED',
        'client_name' => 'Acme Marine',
        'crew_lines' => [
            [
                'rank' => 'Master',
                'category' => 'Marine',
                'qty' => 2,
                'basis' => 'Day',
                'rate' => 100.00,
                'duration' => 10,
                'ot_rate' => 0,
                'mob_date' => now()->toDateString(),
                'remarks' => null,
            ],
        ],
    ]);

    $quote = Quote::query()->firstOrFail();

    $response->assertRedirect(route('quotes.edit', $quote, absolute: false));

    expect((float) $quote->total_amount)->toBe(2000.0)
        ->and($quote->crewLines()->count())->toBe(1);
});

test('user can update quote and filter list', function () {
    $this->actingAs(User::factory()->create());

    $quote = Quote::factory()->create([
        'doc_no' => 'OMS-Q-2026-201',
        'status' => 'Draft',
        'type' => 'Proposal',
        'issue_date' => now()->toDateString(),
        'client_name' => 'Client A',
    ]);

    $response = $this->put(route('quotes.update', $quote), [
        'doc_no' => 'OMS-Q-2026-201',
        'type' => 'Rate Contract',
        'issue_date' => now()->toDateString(),
        'status' => 'Sent',
        'currency' => 'AED',
        'client_name' => 'Client A',
        'crew_lines' => [
            [
                'rank' => 'AB Seaman',
                'category' => 'Marine',
                'qty' => 1,
                'basis' => 'Day',
                'rate' => 300,
                'duration' => 5,
                'ot_rate' => 0,
                'mob_date' => null,
                'remarks' => null,
            ],
        ],
    ]);

    $response->assertRedirect(route('quotes.edit', $quote, absolute: false));

    $quote->refresh();
    expect($quote->status)->toBe('Sent')
        ->and((float) $quote->total_amount)->toBe(1500.0);

    $this->get(route('quotes.index', ['status' => 'Sent']))->assertSee('OMS-Q-2026-201');
});
