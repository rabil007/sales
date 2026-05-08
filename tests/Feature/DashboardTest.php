<?php

use App\Models\Quote;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    Quote::factory()->create([
        'client_name' => 'ADNOC Offshore',
        'status' => 'Draft',
        'total_amount' => 12000,
        'issue_date' => now()->toDateString(),
    ]);
    Quote::factory()->create([
        'client_name' => 'DP World',
        'status' => 'Active',
        'total_amount' => 25000,
        'issue_date' => now()->subMonth()->toDateString(),
    ]);

    $response = $this->get(route('dashboard'));
    $response
        ->assertOk()
        ->assertSee('Average Quote Value')
        ->assertSee('Top Clients by Quote Value')
        ->assertSee('Monthly Quote Value')
        ->assertSee('Monthly Count')
        ->assertSee('Status Distribution')
        ->assertSee('Agreement Type Mix')
        ->assertSee('ADNOC Offshore')
        ->assertSee('DP World');
});
