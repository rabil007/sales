<?php

use App\Models\Client;
use App\Models\User;

test('authenticated user can manage clients', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('clients.index'))->assertSuccessful();
    $this->get(route('clients.create'))->assertSuccessful();

    $response = $this->post(route('clients.store'), [
        'name' => 'ADNOC Offshore',
        'email' => 'ops@adnoc.test',
        'phone' => '+971501111111',
        'company' => 'ADNOC',
    ]);

    $client = Client::query()->firstOrFail();

    $response->assertRedirect(route('clients.edit', $client, absolute: false));

    $this->put(route('clients.update', $client), [
        'name' => 'ADNOC Offshore Updated',
        'email' => 'ops.updated@adnoc.test',
        'phone' => '+971502222222',
        'company' => 'ADNOC',
    ])->assertRedirect(route('clients.edit', $client, absolute: false));

    $client->refresh();
    expect($client->name)->toBe('ADNOC Offshore Updated');

    $this->delete(route('clients.destroy', $client))
        ->assertRedirect(route('clients.index', absolute: false));

    expect(Client::query()->count())->toBe(0);
});

test('authenticated user can filter clients list', function () {
    $this->actingAs(User::factory()->create());

    Client::query()->create([
        'name' => 'ADNOC Offshore',
        'email' => 'ops@adnoc.test',
        'phone' => '+971500000001',
        'company' => 'ADNOC',
    ]);
    Client::query()->create([
        'name' => 'Demo Catering',
        'email' => null,
        'phone' => null,
        'company' => 'Demo Group',
    ]);

    $this->get(route('clients.index', [
        'q' => 'ADNOC',
        'company' => 'ADNOC',
        'contact' => 'with',
    ]))
        ->assertSuccessful()
        ->assertSee('ADNOC Offshore')
        ->assertDontSee('Demo Catering');
});

test('authenticated user can control clients pagination size', function () {
    $this->actingAs(User::factory()->create());

    collect(range(1, 12))->each(function (int $index): void {
        Client::query()->create([
            'name' => "Client {$index}",
            'email' => "client{$index}@test.local",
            'phone' => null,
            'company' => 'Pagination Co',
        ]);
    });

    $this->get(route('clients.index', ['per_page' => 10]))
        ->assertSuccessful()
        ->assertSee('10 / page')
        ->assertSee('Showing 1-10 of 12 clients');
});
