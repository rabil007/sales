<?php

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\User;

test('authenticated user can view invoice pages', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('invoices.index'))->assertSuccessful();
    $this->get(route('invoices.create'))->assertSuccessful();
});

test('user can create an invoice with line items', function () {
    $this->actingAs(User::factory()->create());
    $client = Client::factory()->create(['name' => 'Gulf Marine LLC']);

    $response = $this->post(route('invoices.store'), [
        'doc_no' => 'OMS-INV-2026-101',
        'client_id' => $client->id,
        'client_name' => $client->name,
        'issue_date' => now()->toDateString(),
        'due_date' => now()->addDays(30)->toDateString(),
        'status' => 'Draft',
        'currency' => 'AED',
        'tax_rate' => 5,
        'items' => [
            [
                'description' => 'Master - Daily Hire',
                'category' => 'Marine',
                'qty' => 1,
                'basis' => 'Day',
                'rate' => 1000.00,
                'duration' => 10,
                'duration_unit' => 'Days',
            ],
        ],
    ]);

    $invoice = Invoice::query()->firstOrFail();

    $response->assertRedirect(route('invoices.index', absolute: false));

    expect((float) $invoice->subtotal)->toBe(10000.0)
        ->and((float) $invoice->tax_amount)->toBe(500.0)
        ->and((float) $invoice->total_amount)->toBe(10500.0)
        ->and($invoice->items()->count())->toBe(1);
});

test('user can convert an approved quote into an invoice', function () {
    $this->actingAs(User::factory()->create());
    $client = Client::factory()->create(['name' => 'Offshore Energy']);

    $quote = Quote::factory()->create([
        'client_id' => $client->id,
        'client_name' => $client->name,
        'status' => 'Approved',
        'total_amount' => 15000.00,
    ]);

    $quote->crewLines()->create([
        'rank' => 'Chief Engineer',
        'category' => 'Marine',
        'qty' => 1,
        'basis' => 'Day',
        'rate' => 1500.00,
        'duration_days' => 10,
        'line_total' => 15000.00,
    ]);

    $response = $this->post(route('quotes.convert-to-invoice', $quote));

    $invoice = Invoice::query()->where('quote_id', $quote->id)->firstOrFail();

    $response->assertRedirect(route('invoices.show', $invoice, absolute: false));

    expect($invoice->client_name)->toBe('Offshore Energy')
        ->and((float) $invoice->total_amount)->toBe(15000.0)
        ->and($invoice->items()->count())->toBe(1)
        ->and($quote->refresh()->status)->toBe('Active');
});

test('user can transition invoice statuses', function () {
    $this->actingAs(User::factory()->create());
    $invoice = Invoice::factory()->create(['status' => 'Draft']);

    $this->post(route('invoices.issue', $invoice))
        ->assertRedirect();
    expect($invoice->refresh()->status)->toBe('Issued');

    $this->post(route('invoices.mark-paid', $invoice))
        ->assertRedirect();
    expect($invoice->refresh()->status)->toBe('Paid');
});

test('locked invoices cannot be edited or deleted', function () {
    $this->actingAs(User::factory()->create());
    $invoice = Invoice::factory()->create(['status' => 'Paid']);

    $this->get(route('invoices.edit', $invoice))
        ->assertRedirect(route('invoices.index', absolute: false));

    $this->delete(route('invoices.destroy', $invoice))
        ->assertRedirect(route('invoices.index', absolute: false));

    expect(Invoice::query()->where('id', $invoice->id)->exists())->toBeTrue();
});
