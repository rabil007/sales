<?php

use App\Models\Client;
use App\Models\ClientAgreement;
use App\Models\User;

test('authenticated user can manage client agreements', function () {
    $this->actingAs(User::factory()->create());

    $client = Client::query()->create([
        'name' => 'ADNOC Offshore',
        'email' => 'ops@adnoc.test',
        'phone' => '+971501111111',
        'company' => 'ADNOC',
    ]);

    $this->get(route('client-agreements.index'))->assertSuccessful();
    $this->get(route('client-agreements.create'))->assertSuccessful();

    $response = $this->post(route('client-agreements.store'), [
        'client_id' => $client->id,
        'agreement_ref' => 'OMS-AGR-2026-001',
        'scope_of_work' => 'Offshore crew supply services.',
        'duration_days' => 30,
        'start_date' => '2026-06-01',
        'monthly_invoice_value' => 12500.50,
    ]);

    $agreement = ClientAgreement::query()->firstOrFail();

    $response->assertRedirect(route('client-agreements.index', absolute: false));

    expect($agreement->client_id)->toBe($client->id)
        ->and($agreement->agreement_ref)->toBe('OMS-AGR-2026-001')
        ->and($agreement->start_date->toDateString())->toBe('2026-06-01')
        ->and($agreement->end_date->toDateString())->toBe('2026-06-30')
        ->and((float) $agreement->monthly_invoice_value)->toBe(12500.50);

    $this->put(route('client-agreements.update', $agreement), [
        'client_id' => $client->id,
        'agreement_ref' => 'OMS-AGR-2026-001',
        'scope_of_work' => 'Updated offshore crew supply services.',
        'duration_days' => 60,
        'start_date' => '2026-06-01',
        'monthly_invoice_value' => 15000,
    ])->assertRedirect(route('client-agreements.index', absolute: false));

    $agreement->refresh();

    expect($agreement->scope_of_work)->toBe('Updated offshore crew supply services.')
        ->and($agreement->duration_days)->toBe(60)
        ->and($agreement->end_date->toDateString())->toBe('2026-07-30');

    $this->delete(route('client-agreements.destroy', $agreement))
        ->assertRedirect(route('client-agreements.index', absolute: false));

    expect(ClientAgreement::query()->count())->toBe(0);
});

test('client agreement store requires client and unique agreement ref', function () {
    $this->actingAs(User::factory()->create());

    $client = Client::query()->create([
        'name' => 'DP World',
        'email' => 'ops@dpworld.test',
        'phone' => '+971502222222',
        'company' => 'DP World',
    ]);

    ClientAgreement::query()->create([
        'client_id' => $client->id,
        'agreement_ref' => 'OMS-AGR-2026-002',
        'scope_of_work' => 'Existing agreement.',
        'duration_days' => 30,
        'start_date' => '2026-01-01',
        'end_date' => '2026-01-30',
        'monthly_invoice_value' => 5000,
    ]);

    $this->post(route('client-agreements.store'), [
        'agreement_ref' => 'OMS-AGR-2026-002',
        'scope_of_work' => 'Duplicate ref.',
        'duration_days' => 30,
        'start_date' => '2026-02-01',
        'monthly_invoice_value' => 6000,
    ])->assertSessionHasErrors(['client_id', 'agreement_ref']);

    expect(ClientAgreement::query()->count())->toBe(1);
});

test('authenticated user can filter client agreements list', function () {
    $this->actingAs(User::factory()->create());

    $adnoc = Client::query()->create([
        'name' => 'ADNOC Offshore',
        'email' => 'ops@adnoc.test',
        'phone' => '+971500000001',
        'company' => 'ADNOC',
    ]);

    $dpWorld = Client::query()->create([
        'name' => 'DP World',
        'email' => 'ops@dpworld.test',
        'phone' => '+971500000002',
        'company' => 'DP World',
    ]);

    ClientAgreement::query()->create([
        'client_id' => $adnoc->id,
        'agreement_ref' => 'OMS-AGR-2026-010',
        'scope_of_work' => 'ADNOC offshore support.',
        'duration_days' => 30,
        'start_date' => '2026-01-01',
        'end_date' => '2026-01-30',
        'monthly_invoice_value' => 8000,
    ]);

    ClientAgreement::query()->create([
        'client_id' => $dpWorld->id,
        'agreement_ref' => 'OMS-AGR-2026-011',
        'scope_of_work' => 'DP World logistics support.',
        'duration_days' => 45,
        'start_date' => '2026-02-01',
        'end_date' => '2026-03-17',
        'monthly_invoice_value' => 9000,
    ]);

    $this->get(route('client-agreements.index', [
        'q' => 'ADNOC',
        'client_id' => $adnoc->id,
    ]))
        ->assertSuccessful()
        ->assertSee('OMS-AGR-2026-010')
        ->assertDontSee('OMS-AGR-2026-011');
});

test('authenticated user can control client agreements pagination size', function () {
    $this->actingAs(User::factory()->create());

    $client = Client::query()->create([
        'name' => 'Pagination Client',
        'email' => 'ops@pagination.test',
        'phone' => null,
        'company' => 'Pagination Co',
    ]);

    collect(range(1, 12))->each(function (int $index) use ($client): void {
        ClientAgreement::query()->create([
            'client_id' => $client->id,
            'agreement_ref' => "OMS-AGR-2026-{$index}",
            'scope_of_work' => "Agreement {$index}",
            'duration_days' => 30,
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-30',
            'monthly_invoice_value' => 1000 + $index,
        ]);
    });

    $this->get(route('client-agreements.index', ['per_page' => 10]))
        ->assertSuccessful()
        ->assertSee('Showing')
        ->assertSee('1')
        ->assertSee('10');
});
