@props([
    'sidebar' => false,
])

@php
    $appName = \App\Models\CompanySetting::get('app_name', config('app.name', 'OMS Sales'));
@endphp

@if($sidebar)
    <flux:sidebar.brand :name="$appName" class="app-sidebar-brand [&_[data-content]]:text-base [&_[data-content]]:font-semibold [&_[data-content]]:tracking-tight" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-10 items-center justify-center overflow-hidden rounded-xl bg-linear-to-br from-blue-500/20 to-violet-500/20 ring-1 ring-white/10 shadow-lg shadow-blue-500/10">
            <x-app-logo-icon class="size-8 object-contain" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand :name="$appName" class="[&_[data-content]]:text-base [&_[data-content]]:font-semibold" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-10 items-center justify-center overflow-hidden rounded-md">
            <x-app-logo-icon class="size-9 object-contain" />
        </x-slot>
    </flux:brand>
@endif
