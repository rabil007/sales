@props([
    'sidebar' => false,
])

@php
    $appName = \App\Models\CompanySetting::get('app_name', config('app.name', 'OMS Sales'));
@endphp

@if($sidebar)
    <flux:sidebar.brand :name="$appName" class="[&_[data-content]]:text-base [&_[data-content]]:font-semibold" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-10 items-center justify-center overflow-hidden rounded-md">
            <x-app-logo-icon class="size-9 object-contain" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand :name="$appName" class="[&_[data-content]]:text-base [&_[data-content]]:font-semibold" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-10 items-center justify-center overflow-hidden rounded-md">
            <x-app-logo-icon class="size-9 object-contain" />
        </x-slot>
    </flux:brand>
@endif
