<?php

use App\Models\Rank;
use App\Models\User;

test('authenticated user can manage ranks', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('ranks.index'))->assertSuccessful();
    $this->get(route('ranks.create'))->assertSuccessful();

    $response = $this->post(route('ranks.store'), [
        'name' => 'Chief Officer',
        'category' => 'Marine',
        'default_basis' => 'Day',
        'default_rate' => 720.00,
        'is_active' => true,
    ]);

    $rank = Rank::query()->firstOrFail();

    $response->assertRedirect(route('ranks.edit', $rank, absolute: false));

    $this->put(route('ranks.update', $rank), [
        'name' => 'Chief Officer',
        'category' => 'Offshore',
        'default_basis' => 'Month',
        'default_rate' => 18000.00,
        'is_active' => false,
    ])->assertRedirect(route('ranks.edit', $rank, absolute: false));

    $rank->refresh();
    expect($rank->category)->toBe('Offshore')
        ->and($rank->default_basis)->toBe('Month')
        ->and((float) $rank->default_rate)->toBe(18000.0)
        ->and($rank->is_active)->toBeFalse();

    $this->delete(route('ranks.destroy', $rank))
        ->assertRedirect(route('ranks.index', absolute: false));
});
