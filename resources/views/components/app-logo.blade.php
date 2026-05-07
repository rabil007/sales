@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="OMS Sales" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center overflow-hidden rounded-md bg-white">
            <x-app-logo-icon class="size-7 object-contain" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="OMS Sales" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center overflow-hidden rounded-md bg-white">
            <x-app-logo-icon class="size-7 object-contain" />
        </x-slot>
    </flux:brand>
@endif
