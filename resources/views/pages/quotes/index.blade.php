<x-layouts::app :title="__('Quotes & Agreements')">
    <div class="space-y-4">
        @if (session('status'))
            <flux:callout icon="check-circle" color="emerald">{{ session('status') }}</flux:callout>
        @endif

        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <flux:heading size="lg">All Quotes & Agreements</flux:heading>
                <flux:text class="text-zinc-500">Manage proposals, rate contracts, and active agreements.</flux:text>
            </div>
            <flux:button variant="primary" :href="route('quotes.create')" wire:navigate>New Quote</flux:button>
        </div>

        <form method="GET" class="flex items-end gap-3">
            <flux:select name="status" label="Filter by status" class="max-w-xs">
                <option value="">All Status</option>
                @foreach (['Draft', 'Sent', 'Approved', 'Active', 'Expired'] as $statusOption)
                    <option value="{{ $statusOption }}" @selected($status === $statusOption)>{{ $statusOption }}</option>
                @endforeach
            </flux:select>
            <flux:button type="submit" variant="filled">Apply</flux:button>
        </form>

        <div class="overflow-x-auto rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
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
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quotes as $quote)
                        <tr class="border-t border-zinc-200 dark:border-zinc-700">
                            <td class="px-4 py-3 font-medium">{{ $quote->doc_no }}</td>
                            <td class="px-4 py-3">{{ $quote->client_name }}</td>
                            <td class="px-4 py-3">{{ $quote->type }}</td>
                            <td class="px-4 py-3">{{ optional($quote->issue_date)->toDateString() }}</td>
                            <td class="px-4 py-3">{{ optional($quote->expiry_date)->toDateString() ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $quote->currency }} {{ number_format((float) $quote->total_amount, 2) }}</td>
                            <td class="px-4 py-3">{{ $quote->status }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <flux:button size="xs" variant="ghost" :href="route('quotes.edit', $quote)" wire:navigate>Edit</flux:button>
                                    <flux:button size="xs" variant="ghost" :href="route('quotes.show', $quote)" wire:navigate>Preview</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-6 text-center text-zinc-500" colspan="8">No quotes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $quotes->links() }}
    </div>
</x-layouts::app>
