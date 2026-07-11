<x-layouts::app :title="__('Quotes & Agreements')">
    <div class="space-y-8">
        {{-- Page Header --}}
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight">All Quotes & Agreements</flux:heading>
                <flux:text class="mt-1 text-zinc-500 dark:text-zinc-400">Manage proposals, rate contracts, and active agreements.</flux:text>
            </div>
            <flux:button variant="primary" icon="plus" :href="route('quotes.create')" wire:navigate class="rounded-full px-5 transition-transform hover:-translate-y-0.5 hover:shadow-md">New Quote</flux:button>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            <div class="group relative overflow-hidden rounded-3xl border border-zinc-200/60 bg-gradient-to-b from-white/80 to-white/40 p-6 shadow-sm backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700/60 dark:from-zinc-900/80 dark:to-zinc-900/40">
                <div class="absolute -right-8 -top-8 z-0 h-32 w-32 rounded-full bg-blue-500/10 blur-3xl transition-all duration-500 group-hover:scale-150 group-hover:bg-blue-500/20 dark:bg-blue-400/5"></div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total Quotes</p>
                    <p class="mt-2 tabular-nums text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
            <div class="group relative overflow-hidden rounded-3xl border border-zinc-200/60 bg-gradient-to-b from-white/80 to-white/40 p-6 shadow-sm backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700/60 dark:from-zinc-900/80 dark:to-zinc-900/40">
                <div class="absolute -right-8 -top-8 z-0 h-32 w-32 rounded-full bg-amber-500/10 blur-3xl transition-all duration-500 group-hover:scale-150 group-hover:bg-amber-500/20 dark:bg-amber-400/5"></div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Draft</p>
                    <p class="mt-2 tabular-nums text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ number_format($stats['draft']) }}</p>
                </div>
            </div>
            <div class="group relative overflow-hidden rounded-3xl border border-zinc-200/60 bg-gradient-to-b from-white/80 to-white/40 p-6 shadow-sm backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700/60 dark:from-zinc-900/80 dark:to-zinc-900/40">
                <div class="absolute -right-8 -top-8 z-0 h-32 w-32 rounded-full bg-emerald-500/10 blur-3xl transition-all duration-500 group-hover:scale-150 group-hover:bg-emerald-500/20 dark:bg-emerald-400/5"></div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Active</p>
                    <p class="mt-2 tabular-nums text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ number_format($stats['active']) }}</p>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" class="grid gap-4 rounded-3xl border border-zinc-200/60 bg-white/60 p-6 shadow-sm backdrop-blur-xl md:grid-cols-3 dark:border-zinc-700/60 dark:bg-zinc-900/60">
            <flux:input
                name="q"
                :value="$q"
                placeholder="Search by client, doc no..."
                icon="magnifying-glass"
            />
            <flux:select name="status">
                <option value="">All Status</option>
                @foreach (['Draft', 'Sent', 'Approved', 'Active', 'Expired'] as $statusOption)
                    <option value="{{ $statusOption }}" @selected($status === $statusOption)>{{ $statusOption }}</option>
                @endforeach
            </flux:select>
            <div class="flex items-center gap-2">
                <flux:button type="submit" variant="filled" icon="funnel" class="rounded-full px-4">Filter</flux:button>
                @if ($q !== '' || $status !== '' || $perPage !== 15)
                    <flux:button :href="route('quotes.index')" variant="ghost" wire:navigate class="rounded-full">Clear</flux:button>
                @endif
            </div>
        </form>

        {{-- Table --}}
        <div class="overflow-hidden rounded-3xl border border-zinc-200/60 bg-white/60 shadow-sm backdrop-blur-xl dark:border-zinc-700/60 dark:bg-zinc-900/60">
            <div class="overflow-x-auto p-2">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            <th class="px-4 py-3.5">Doc No</th>
                            <th class="px-4 py-3.5">Client</th>
                            <th class="px-4 py-3.5">Type</th>
                            <th class="px-4 py-3.5">Issue Date</th>
                            <th class="px-4 py-3.5">Expiry</th>
                            <th class="px-4 py-3.5">Total</th>
                            <th class="px-4 py-3.5">Status</th>
                            <th class="px-4 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-700/60">
                        @forelse ($quotes as $quote)
                            <tr
                                onclick="window.location='{{ route('quotes.show', $quote) }}'"
                                class="cursor-pointer transition-colors hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30"
                            >
                                <td class="px-4 py-4">
                                    <span class="font-mono text-xs font-semibold text-zinc-800 dark:text-zinc-200">
                                        {{ $quote->doc_no }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 font-medium text-zinc-900 dark:text-white">{{ $quote->client_name }}</td>
                                <td class="px-4 py-4 text-zinc-600 dark:text-zinc-300">{{ $quote->type }}</td>
                                <td class="px-4 py-4 text-zinc-600 dark:text-zinc-300">{{ optional($quote->issue_date)->toDateString() }}</td>
                                <td class="px-4 py-4 text-zinc-600 dark:text-zinc-300">{{ optional($quote->expiry_date)->toDateString() ?? '—' }}</td>
                                <td class="px-4 py-4 font-medium tabular-nums text-zinc-900 dark:text-white">{{ $quote->currency }} {{ number_format((float) $quote->total_amount, 2) }}</td>
                                <td class="px-4 py-4">
                                    @php
                                        $color = match($quote->status) {
                                            'Active'   => 'green',
                                            'Approved' => 'lime',
                                            'Sent'     => 'blue',
                                            'Expired'  => 'red',
                                            default    => 'zinc',
                                        };
                                    @endphp
                                    <flux:badge :color="$color" size="sm">{{ $quote->status }}</flux:badge>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1" onclick="event.stopPropagation()">
                                        @if (in_array($quote->status, ['Sent', 'Approved', 'Active'], true))
                                            <flux:tooltip content="Convert to Invoice">
                                                <form method="POST" action="{{ route('quotes.convert-to-invoice', $quote) }}" class="inline">
                                                    @csrf
                                                    <flux:button type="submit" size="sm" icon="banknotes" variant="ghost" class="size-8! rounded-full p-0! text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/40" />
                                                </form>
                                            </flux:tooltip>
                                        @endif
                                        <flux:tooltip content="Edit">
                                            <flux:button size="sm" icon="pencil" variant="ghost" :href="route('quotes.edit', $quote)" wire:navigate class="size-8! rounded-full p-0! hover:bg-zinc-100 dark:hover:bg-zinc-800" />
                                        </flux:tooltip>
                                        <flux:tooltip content="Preview">
                                            <flux:button size="sm" icon="eye" variant="ghost" :href="route('quotes.show', $quote)" wire:navigate class="size-8! rounded-full p-0! hover:bg-zinc-100 dark:hover:bg-zinc-800" />
                                        </flux:tooltip>
                                        <flux:tooltip content="Delete">
                                            <flux:modal.trigger :name="'delete-quote-'.$quote->id">
                                                <flux:button
                                                    size="sm"
                                                    icon="trash"
                                                    variant="ghost"
                                                    class="size-8! rounded-full p-0! text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/40"
                                                />
                                            </flux:modal.trigger>
                                        </flux:tooltip>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-16 text-center text-zinc-500" colspan="8">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                                            <flux:icon.document-text class="size-6 opacity-40" />
                                        </div>
                                        <p class="font-medium text-zinc-600 dark:text-zinc-400">No quotes found.</p>
                                        <p class="text-sm">Try a different filter or create a new quote.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-zinc-200/60 bg-white/40 px-6 py-4 text-sm text-zinc-500 dark:border-zinc-700/60 dark:bg-zinc-800/20">
                <p>
                    Showing <span class="font-medium text-zinc-900 dark:text-white">{{ $quotes->firstItem() ?? 0 }}</span>-<span class="font-medium text-zinc-900 dark:text-white">{{ $quotes->lastItem() ?? 0 }}</span> of <span class="font-medium text-zinc-900 dark:text-white">{{ $quotes->total() }}</span> quotes
                </p>
                <div class="flex flex-wrap items-center gap-3">
                    <form method="GET" action="{{ route('quotes.index') }}" class="flex items-center gap-2">
                        @if ($q !== '')
                            <input type="hidden" name="q" value="{{ $q }}">
                        @endif
                        @if ($status !== '')
                            <input type="hidden" name="status" value="{{ $status }}">
                        @endif
                        <flux:select name="per_page" size="sm" onchange="this.form.submit()" class="rounded-full">
                            @foreach ([10, 15, 25, 50] as $size)
                                <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }} / page</option>
                            @endforeach
                        </flux:select>
                    </form>
                    <span>Page {{ $quotes->currentPage() }} of {{ $quotes->lastPage() }}</span>
                    {{ $quotes->withQueryString()->links() }}
                </div>
            </div>
        </div>

        @foreach ($quotes as $quote)
            <flux:modal :name="'delete-quote-'.$quote->id" class="max-w-md">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Delete quote?</flux:heading>
                        <flux:subheading>
                            This action cannot be undone. Quote <span class="font-semibold">{{ $quote->doc_no }}</span> will be permanently deleted.
                        </flux:subheading>
                    </div>

                    <div class="flex justify-end gap-2">
                        <flux:modal.close>
                            <flux:button variant="filled" class="rounded-full">Cancel</flux:button>
                        </flux:modal.close>

                        <form method="POST" action="{{ route('quotes.destroy', $quote) }}">
                            @csrf
                            @method('DELETE')
                            <flux:button variant="danger" type="submit" class="rounded-full">Delete</flux:button>
                        </form>
                    </div>
                </div>
            </flux:modal>
        @endforeach
    </div>
</x-layouts::app>
