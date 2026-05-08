@php
    $appLogoUrl = \App\Models\CompanySetting::logoUrl();
    $appName = \App\Models\CompanySetting::get('app_name', config('app.name', 'OMS Sales'));
@endphp

<img src="{{ $appLogoUrl }}" alt="{{ $appName }} logo" {{ $attributes }} />
