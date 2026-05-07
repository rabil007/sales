<x-layouts::app :title="__('Quotes & Agreements')">
    <div class="space-y-4">
        {{-- Page Header --}}
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <flux:heading size="lg">All Quotes & Agreements</flux:heading>
                <flux:text class="text-zinc-500">Manage proposals, rate contracts, and active agreements.</flux:text>
            </div>
            <flux:button variant="primary" icon="plus" :href="route('quotes.create')" wire:navigate>New Quote</flux:button>
        </div>

        {{-- Filters --}}
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <flux:input
                    name="q"
                    :value="request('q')"
                    placeholder="Search by client, doc no…"
                    icon="magnifying-glass"
                    class="w-full"
                />
            </div>
            <flux:select name="status" class="w-48">
                <option value="">All Status</option>
                @foreach (['Draft', 'Sent', 'Approved', 'Active', 'Expired'] as $statusOption)
                    <option value="{{ $statusOption }}" @selected($status === $statusOption)>{{ $statusOption }}</option>
                @endforeach
            </flux:select>
            <flux:button type="submit" variant="filled" icon="funnel">Filter</flux:button>
            @if(request('q') || request('status'))
                <flux:button :href="route('quotes.index')" variant="ghost" wire:navigate>Clear</flux:button>
            @endif
        </form>

        {{-- Table --}}
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr class="text-left text-xs uppercase tracking-wide text-zinc-500">
                            <th class="px-4 py-3">Doc No</th>
                            <th class="px-4 py-3">Client</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Issue Date</th>
                            <th class="px-4 py-3">Expiry</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($quotes as $quote)
                            <tr
                                onclick="window.location='{{ route('quotes.show', $quote) }}'"
                                class="cursor-pointer border-t border-zinc-200 transition-colors hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800/60"
                            >
                                <td class="px-4 py-3">
                                    <span class="font-mono text-xs font-semibold text-zinc-800 dark:text-zinc-200">
                                        {{ $quote->doc_no }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-medium">{{ $quote->client_name }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ $quote->type }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ optional($quote->issue_date)->toDateString() }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ optional($quote->expiry_date)->toDateString() ?? '—' }}</td>
                                <td class="px-4 py-3 font-medium tabular-nums">{{ $quote->currency }} {{ number_format((float) $quote->total_amount, 2) }}</td>
                                <td class="px-4 py-3">
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
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1.5" onclick="event.stopPropagation()">
                                        <flux:tooltip content="Edit">
                                            <flux:button size="sm" icon="pencil" variant="ghost" :href="route('quotes.edit', $quote)" wire:navigate class="size-8! p-0!" />
                                        </flux:tooltip>
                                        <flux:tooltip content="Preview">
                                            <flux:button size="sm" icon="eye" variant="ghost" :href="route('quotes.show', $quote)" wire:navigate class="size-8! p-0!" />
                                        </flux:tooltip>
                                        <flux:tooltip content="Delete">
                                            <flux:modal.trigger :name="'delete-quote-'.$quote->id">
                                                <flux:button
                                                    size="sm"
                                                    icon="trash"
                                                    variant="ghost"
                                                    class="size-8! p-0! text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/40"
                                                />
                                            </flux:modal.trigger>
                                        </flux:tooltip>
                                    </div>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-12 text-center text-zinc-500" colspan="8">
                                    <div class="flex flex-col items-center gap-2">
                                        <flux:icon.document-text class="size-10 opacity-25" />
                                        <p class="font-medium text-zinc-600 dark:text-zinc-400">No quotes found.</p>
                                        <p class="text-xs text-zinc-400">Try a different filter or create a new quote.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
                            <flux:button variant="filled">Cancel</flux:button>
                        </flux:modal.close>

                        <form method="POST" action="{{ route('quotes.destroy', $quote) }}">
                            @csrf
                            @method('DELETE')
                            <flux:button variant="danger" type="submit">Delete</flux:button>
                        </form>
                    </div>
                </div>
            </flux:modal>
        @endforeach

        <div class="text-sm text-zinc-500">
            {{ $quotes->withQueryString()->links() }}
        </div>
    </div>
</x-layouts::app>
