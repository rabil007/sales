<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('database seeder creates default login user', function () {
    $this->seed();

    $user = User::query()->where('email', 'admin@sales.test')->first();

    expect($user)->not->toBeNull()
        ->and($user?->name)->toBe('Sales Admin')
        ->and(Hash::check('password', (string) $user?->password))->toBeTrue();
});
