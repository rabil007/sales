<?php

use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('user can update application name and logo from template settings', function () {
    Storage::fake('public');

    CompanySetting::query()->updateOrCreate(
        ['key' => 'app_name'],
        ['label' => 'Application Name', 'group' => 'application', 'value' => 'OMS Sales'],
    );
    CompanySetting::query()->updateOrCreate(
        ['key' => 'app_logo_path'],
        ['label' => 'Application Logo Path', 'group' => 'application', 'value' => 'overseas-marine logo.png'],
    );

    $user = User::factory()->create(['email_verified_at' => now()]);

    $response = $this->actingAs($user)->put(route('settings.company.update'), [
        'settings' => [
            'app_name' => 'OMS Ops Hub',
        ],
        'app_logo' => UploadedFile::fake()->image('new-logo.png'),
    ]);

    $response->assertRedirect();

    expect(CompanySetting::get('app_name'))->toBe('OMS Ops Hub');
    $logoPath = CompanySetting::get('app_logo_path');
    expect($logoPath)->toStartWith('app-branding/');
    expect(Storage::disk('public')->exists($logoPath))->toBeTrue();
});

test('company template settings reject unknown or forged keys', function () {
    CompanySetting::query()->updateOrCreate(
        ['key' => 'app_name'],
        ['label' => 'Application Name', 'group' => 'application', 'value' => 'OMS Sales'],
    );

    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)->put(route('settings.company.update'), [
        'settings' => [
            'app_name' => 'OK',
            'evil_payload' => 'no',
        ],
    ])->assertSessionHasErrors('settings');

    $this->actingAs($user)->put(route('settings.company.update'), [
        'settings' => [
            'app_name' => 'OK',
            'app_logo_path' => 'public/../../etc/passwd',
        ],
    ])->assertSessionHasErrors('settings');

    expect(CompanySetting::get('app_name'))->toBe('OMS Sales');
});
