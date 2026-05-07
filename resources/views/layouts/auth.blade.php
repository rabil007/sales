@props([
    'title' => null,
    'showTopLogo' => true,
])

<x-layouts::auth.simple :title="$title" :show-top-logo="$showTopLogo">
    {{ $slot }}
</x-layouts::auth.simple>
