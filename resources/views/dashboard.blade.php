<x-layouts::app :title="__('Dashboard')">
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-xs text-zinc-500">Total Quotes</p>
                <p class="mt-2 text-2xl font-semibold">{{ number_format($totalQuotes) }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-xs text-zinc-500">Active Agreements</p>
                <p class="mt-2 text-2xl font-semibold">{{ number_format($activeAgreements) }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-xs text-zinc-500">Pending Approval</p>
                <p class="mt-2 text-2xl font-semibold">{{ number_format($pendingApproval) }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-xs text-zinc-500">Total Value</p>
                <p class="mt-2 text-2xl font-semibold">AED {{ number_format((float) $totalValue, 2) }}</p>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between border-b border-zinc-200 px-4 py-3 dark:border-zinc-700">
                <flux:heading size="sm">Recent Quotes & Agreements</flux:heading>
                <flux:button size="sm" variant="filled" :href="route('quotes.index')" wire:navigate>View All</flux:button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr class="text-left text-xs uppercase tracking-wide text-zinc-500">
                            <th class="px-4 py-3">Doc No</th>
                            <th class="px-4 py-3">Client</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Issue Date</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentQuotes as $quote)
                            <tr class="border-t border-zinc-200 dark:border-zinc-700">
                                <td class="px-4 py-3 font-medium">
                                    <a class="hover:underline" href="{{ route('quotes.edit', $quote) }}">{{ $quote->doc_no }}</a>
                                </td>
                                <td class="px-4 py-3">{{ $quote->client_name }}</td>
                                <td class="px-4 py-3">{{ $quote->type }}</td>
                                <td class="px-4 py-3">{{ optional($quote->issue_date)->toDateString() }}</td>
                                <td class="px-4 py-3">{{ $quote->currency }} {{ number_format((float) $quote->total_amount, 2) }}</td>
                                <td class="px-4 py-3">{{ $quote->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-6 text-center text-zinc-500" colspan="6">No quotes created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts::app>
