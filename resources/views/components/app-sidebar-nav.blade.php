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
                    'route' => route('invoices.index'),
                    'current' => request()->routeIs('invoices.*'),
                    'icon' => 'banknotes',
                    'label' => __('Invoices'),
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
