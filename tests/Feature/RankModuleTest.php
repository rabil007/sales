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

    $this->patch(route('ranks.toggle-status', $rank))
        ->assertRedirect();

    expect($rank->fresh()?->is_active)->toBeTrue();

    $this->delete(route('ranks.destroy', $rank))
        ->assertRedirect(route('ranks.index', absolute: false));
});

test('authenticated user can filter ranks list', function () {
    $this->actingAs(User::factory()->create());

    Rank::factory()->create([
        'name' => 'Master',
        'category' => 'Marine',
        'default_basis' => 'Day',
        'is_active' => true,
    ]);
    Rank::factory()->create([
        'name' => 'Cook',
        'category' => 'Catering',
        'default_basis' => 'Month',
        'is_active' => false,
    ]);

    $this->get(route('ranks.index', [
        'q' => 'Mas',
        'category' => 'Marine',
        'basis' => 'Day',
        'status' => 'active',
    ]))
        ->assertSuccessful()
        ->assertSee('Master')
        ->assertDontSee('Cook');
});

test('authenticated user can control rank pagination size', function () {
    $this->actingAs(User::factory()->create());

    collect(range(1, 12))->each(function (int $index): void {
        Rank::query()->create([
            'name' => "Rank {$index}",
            'category' => 'Marine',
            'default_basis' => 'Day',
            'default_rate' => 100 + $index,
            'is_active' => true,
        ]);
    });

    $this->get(route('ranks.index', ['per_page' => 10]))
        ->assertSuccessful()
        ->assertSee('10 / page')
        ->assertSee('Showing 1-10 of 12 ranks');
});
