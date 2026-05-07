<?php

use App\Models\Client;
use App\Models\Quote;
use App\Models\Rank;
use App\Models\User;

test('authenticated user can view quote pages', function () {
    $this->actingAs(User::factory()->create());
    Rank::factory()->create(['name' => 'Master']);

    $this->get(route('dashboard'))->assertSuccessful();
    $this->get(route('quotes.index'))->assertSuccessful();
    $this->get(route('quotes.create'))
        ->assertSuccessful()
        ->assertSee('Master');
});

test('user can create a quote with crew lines', function () {
    $this->actingAs(User::factory()->create());
    $client = Client::factory()->create(['name' => 'Acme Marine']);

    $response = $this->post(route('quotes.store'), [
        'doc_no' => 'OMS-Q-2026-111',
        'type' => 'Proposal',
        'issue_date' => now()->toDateString(),
        'status' => 'Draft',
        'currency' => 'AED',
        'client_id' => $client->id,
        'client_name' => $client->name,
        'crew_lines' => [
            [
                'rank' => 'Master',
                'category' => 'Marine',
                'qty' => 2,
                'basis' => 'Day',
                'rate' => 100.00,
                'duration_days' => 10,
                'monthly_rate' => 0,
                'duration_months' => 0,
                'manual_total' => 0,
                'ot_rate' => 0,
                'mob_date' => now()->toDateString(),
                'demob_date' => null,
                'remarks' => null,
            ],
        ],
    ]);

    $quote = Quote::query()->firstOrFail();

    $response->assertRedirect(route('quotes.index', absolute: false));

    expect((float) $quote->total_amount)->toBe(2000.0)
        ->and($quote->crewLines()->count())->toBe(1);
});

test('user can update quote and filter list', function () {
    $this->actingAs(User::factory()->create());
    $client = Client::factory()->create(['name' => 'Client A']);

    $quote = Quote::factory()->create([
        'doc_no' => 'OMS-Q-2026-201',
        'status' => 'Draft',
        'type' => 'Proposal',
        'issue_date' => now()->toDateString(),
        'client_id' => $client->id,
        'client_name' => 'Client A',
    ]);

    $response = $this->put(route('quotes.update', $quote), [
        'doc_no' => 'OMS-Q-2026-201',
        'type' => 'Rate Contract',
        'issue_date' => now()->toDateString(),
        'status' => 'Sent',
        'currency' => 'AED',
        'client_id' => $client->id,
        'client_name' => 'Client A',
        'crew_lines' => [
            [
                'rank' => 'AB Seaman',
                'category' => 'Marine',
                'qty' => 1,
                'basis' => 'Day',
                'rate' => 300,
                'duration_days' => 5,
                'duration_months' => 0,
                'monthly_rate' => 0,
                'manual_total' => 0,
                'ot_rate' => 0,
                'mob_date' => null,
                'demob_date' => null,
                'remarks' => null,
            ],
        ],
    ]);

    $response->assertRedirect(route('quotes.index', absolute: false));

    $quote->refresh();
    expect($quote->status)->toBe('Sent')
        ->and((float) $quote->total_amount)->toBe(1500.0);

    $this->get(route('quotes.index', ['status' => 'Sent']))->assertSee('OMS-Q-2026-201');
});

test('user can use workflow transitions and renew agreement', function () {
    $this->actingAs(User::factory()->create());

    $quote = Quote::factory()->hasCrewLines(1)->create([
        'status' => 'Draft',
        'expiry_date' => now()->addMonth()->toDateString(),
    ]);

    $this->post(route('quotes.send', $quote))->assertRedirect();
    $this->post(route('quotes.approve', $quote))->assertRedirect();
    $this->post(route('quotes.activate', $quote))->assertRedirect();
    $this->post(route('quotes.expire', $quote))->assertRedirect();

    expect($quote->refresh()->status)->toBe('Expired');

    $this->post(route('quotes.renew', $quote))->assertRedirect();

    expect(Quote::query()->count())->toBe(2);
    $renewedQuote = Quote::query()->whereKeyNot($quote->id)->firstOrFail();
    expect($renewedQuote->status)->toBe('Draft')
        ->and($renewedQuote->crewLines()->count())->toBe(1);
});

test('locked quote cannot be updated', function () {
    $this->actingAs(User::factory()->create());
    $client = Client::factory()->create();
    $quote = Quote::factory()->create([
        'status' => 'Approved',
        'client_id' => $client->id,
        'client_name' => $client->name,
    ]);

    $this->put(route('quotes.update', $quote), [
        'doc_no' => $quote->doc_no,
        'type' => 'Proposal',
        'issue_date' => now()->toDateString(),
        'status' => 'Approved',
        'currency' => 'AED',
        'client_id' => $client->id,
        'client_name' => $client->name,
    ])->assertRedirect(route('quotes.index', absolute: false));

    expect($quote->refresh()->status)->toBe('Approved');
});
