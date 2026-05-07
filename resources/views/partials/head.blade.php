@php
    $appName = \App\Models\CompanySetting::get('app_name', config('app.name', 'OMS Sales'));
    $appLogoPath = \App\Models\CompanySetting::get('app_logo_path', 'overseas-marine logo.png');
@endphp

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.$appName : $appName }}
</title>

<link rel="icon" href="{{ asset('storage/'.$appLogoPath) }}" sizes="32x32" type="image/png">
<link rel="icon" href="{{ asset('storage/'.$appLogoPath) }}" sizes="192x192" type="image/png">
<link rel="apple-touch-icon" href="{{ asset('storage/'.$appLogoPath) }}">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
