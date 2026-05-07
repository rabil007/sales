@php
    $appLogoPath = \App\Models\CompanySetting::get('app_logo_path', 'overseas-marine logo.png');
    $appName = \App\Models\CompanySetting::get('app_name', config('app.name', 'OMS Sales'));
@endphp

<img src="{{ asset('storage/'.$appLogoPath) }}" alt="{{ $appName }} logo" {{ $attributes }} />
