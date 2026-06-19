@php
    $groups = [
        [
            'heading' => __('Overview'),
            'items' => [
                [
                    'route' => route('dashboard'),
                    'current' => request()->routeIs('dashboard'),
                    'icon' => 'squares-2x2',
                    'label' => __('Dashboard'),
                ],
            ],
        ],
        [
            'heading' => __('Sales'),
            'items' => [
                [
                    'route' => route('quotes.index'),
                    'current' => request()->routeIs('quotes.*'),
                    'icon' => 'document-text',
                    'label' => __('Quotes & Agreements'),
                ],
                [
                    'route' => route('client-agreements.index'),
                    'current' => request()->routeIs('client-agreements.*'),
                    'icon' => 'clipboard-document-list',
                    'label' => __('Client Agreements'),
                ],
            ],
        ],
        [
            'heading' => __('Master Data'),
            'items' => [
                [
                    'route' => route('clients.index'),
                    'current' => request()->routeIs('clients.*'),
                    'icon' => 'building-office-2',
                    'label' => __('Clients'),
                ],
                [
                    'route' => route('ranks.index'),
                    'current' => request()->routeIs('ranks.*'),
                    'icon' => 'user-group',
                    'label' => __('Ranks'),
                ],
            ],
        ],
        [
            'heading' => __('Configuration'),
            'items' => [
                [
                    'route' => route('settings.company.edit'),
                    'current' => request()->routeIs('settings.company.*'),
                    'icon' => 'swatch',
                    'label' => __('Template Settings'),
                ],
            ],
        ],
    ];
@endphp

<div class="space-y-6">
    <div class="px-1 in-data-flux-sidebar-collapsed-desktop:hidden">
        <flux:button
            variant="primary"
            icon="plus"
            :href="route('quotes.create')"
            wire:navigate
            class="app-sidebar-cta w-full rounded-xl shadow-lg shadow-blue-500/20 transition-transform hover:-translate-y-0.5"
        >
            {{ __('New Quote') }}
        </flux:button>
    </div>

    @foreach ($groups as $group)
        <flux:sidebar.group :heading="$group['heading']" class="app-sidebar-group grid gap-1">
            @foreach ($group['items'] as $item)
                <flux:sidebar.item
                    :icon="$item['icon']"
                    :href="$item['route']"
                    :current="$item['current']"
                    wire:navigate
                    class="app-sidebar-item"
                >
                    {{ $item['label'] }}
                </flux:sidebar.item>
            @endforeach
        </flux:sidebar.group>
    @endforeach
</div>
