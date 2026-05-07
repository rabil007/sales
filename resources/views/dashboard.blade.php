<x-layouts::app :title="__('Dashboard')">
    <div class="space-y-6">
        {{-- Metric Cards --}}
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="flex items-start gap-4 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="rounded-lg bg-blue-50 p-2.5 dark:bg-blue-950">
                    <flux:icon.document-text class="size-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">Total Quotes</p>
                    <p class="mt-1 text-2xl font-bold tabular-nums">{{ number_format($totalQuotes) }}</p>
                </div>
            </div>
            <div class="flex items-start gap-4 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="rounded-lg bg-emerald-50 p-2.5 dark:bg-emerald-950">
                    <flux:icon.check-circle class="size-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">Active Agreements</p>
                    <p class="mt-1 text-2xl font-bold tabular-nums">{{ number_format($activeAgreements) }}</p>
                </div>
            </div>
            <div class="flex items-start gap-4 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="rounded-lg bg-amber-50 p-2.5 dark:bg-amber-950">
                    <flux:icon.clock class="size-5 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">Pending Approval</p>
                    <p class="mt-1 text-2xl font-bold tabular-nums">{{ number_format($pendingApproval) }}</p>
                </div>
            </div>
            <div class="flex items-start gap-4 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="rounded-lg bg-violet-50 p-2.5 dark:bg-violet-950">
                    <flux:icon.currency-dollar class="size-5 text-violet-600 dark:text-violet-400" />
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">Total Value</p>
                    <p class="mt-1 text-2xl font-bold tabular-nums">AED {{ number_format((float) $totalValue, 2) }}</p>
                </div>
            </div>
        </div>

        {{-- Recent Quotes Table --}}
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
                            <tr class="border-t border-zinc-200 transition-colors hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800/60">
                                <td class="px-4 py-3">
                                    <a href="{{ route('quotes.show', $quote) }}" class="font-mono text-xs font-semibold text-zinc-800 hover:text-blue-600 dark:text-zinc-200 dark:hover:text-blue-400">
                                        {{ $quote->doc_no }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 font-medium">{{ $quote->client_name }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ $quote->type }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ optional($quote->issue_date)->toDateString() }}</td>
                                <td class="px-4 py-3 font-medium">{{ $quote->currency }} {{ number_format((float) $quote->total_amount, 2) }}</td>
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
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-10 text-center text-zinc-500" colspan="6">
                                    <div class="flex flex-col items-center gap-2">
                                        <flux:icon.document-text class="size-8 opacity-30" />
                                        <span>No quotes created yet.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts::app>
