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
