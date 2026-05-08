<?php

use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

test('profile page is displayed', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get(route('profile.edit'))->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Test User');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when email address is unchanged', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('application name and logo can be updated from company template settings', function () {
    CompanySetting::query()->updateOrCreate(
        ['key' => 'app_name'],
        ['label' => 'Application Name', 'group' => 'application', 'value' => 'OMS Sales'],
    );
    CompanySetting::query()->updateOrCreate(
        ['key' => 'app_logo_path'],
        ['label' => 'Application Logo Path', 'group' => 'application', 'value' => 'overseas-marine logo.png'],
    );

    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)->put(route('settings.company.update'), [
        'settings' => [
            'app_name' => 'Sales Control',
        ],
        'app_logo' => UploadedFile::fake()->image('brand.png'),
    ])->assertRedirect();

    expect(CompanySetting::get('app_name'))->toBe('Sales Control');
    $logoPath = CompanySetting::get('app_logo_path');
    expect($logoPath)->toStartWith('app-branding/')
        ->and(is_file(public_path('uploads/'.$logoPath)))->toBeTrue();
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.delete-user-modal')
        ->set('password', 'password')
        ->call('deleteUser');

    $response
        ->assertHasNoErrors()
        ->assertRedirect('/');

    expect($user->fresh())->toBeNull();
    expect(Auth::check())->toBeFalse();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.delete-user-modal')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $response->assertHasErrors(['password']);

    expect($user->fresh())->not->toBeNull();
});
