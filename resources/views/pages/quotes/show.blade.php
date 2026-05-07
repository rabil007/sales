<x-layouts::app :title="__('Quote Preview')">
    <div class="space-y-4">
        <div class="flex items-center justify-between no-print">
            <flux:heading size="lg">Quote Preview</flux:heading>
            <div class="flex gap-2">
                <flux:button variant="filled" :href="route('quotes.edit', $quote)" wire:navigate>Edit Quote</flux:button>
                <flux:button variant="primary" onclick="window.print()" type="button">Print / Export PDF</flux:button>
            </div>
        </div>

        <div class="mx-auto max-w-4xl rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-6 flex items-start justify-between">
                <div>
                    <h1 class="text-xl font-semibold">Overseas Marine Services</h1>
                    <p class="text-sm text-zinc-500">{{ $quote->type }}</p>
                </div>
                <div class="text-right">
                    <p class="font-medium">{{ $quote->doc_no }}</p>
                    <p class="text-sm text-zinc-500">Issued: {{ optional($quote->issue_date)->toDateString() }}</p>
                </div>
            </div>

            <div class="mb-6 grid gap-4 rounded-lg bg-zinc-50 p-4 text-sm dark:bg-zinc-800">
                <p><span class="text-zinc-500">Client:</span> {{ $quote->client_name }}</p>
                <p><span class="text-zinc-500">Vessel / Project:</span> {{ $quote->vessel ?: '-' }}</p>
                <p><span class="text-zinc-500">Location:</span> {{ $quote->location ?: '-' }}</p>
                <p><span class="text-zinc-500">Payment Terms:</span> {{ $quote->payment_terms ?: '-' }}</p>
            </div>

            <div class="mb-6 text-sm leading-6">
                <p class="mb-2 font-medium">Scope of Services</p>
                <p class="text-zinc-600 dark:text-zinc-300">{{ $quote->scope ?: '-' }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr class="text-left text-xs uppercase tracking-wide text-zinc-500">
                            <th class="px-3 py-2">#</th>
                            <th class="px-3 py-2">Rank</th>
                            <th class="px-3 py-2">Category</th>
                            <th class="px-3 py-2">Qty</th>
                            <th class="px-3 py-2">Basis</th>
                            <th class="px-3 py-2">Rate</th>
                            <th class="px-3 py-2">Duration</th>
                            <th class="px-3 py-2">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($quote->crewLines as $line)
                            <tr class="border-t border-zinc-200 dark:border-zinc-700">
                                <td class="px-3 py-2">{{ $loop->iteration }}</td>
                                <td class="px-3 py-2">{{ $line->rank ?: '-' }}</td>
                                <td class="px-3 py-2">{{ $line->category }}</td>
                                <td class="px-3 py-2">{{ $line->qty }}</td>
                                <td class="px-3 py-2">{{ $line->basis }}</td>
                                <td class="px-3 py-2">{{ $quote->currency }} {{ number_format((float) $line->rate, 2) }}</td>
                                <td class="px-3 py-2">{{ $line->duration }}</td>
                                <td class="px-3 py-2">
                                    {{ $quote->currency }} {{ number_format((float) $line->qty * (float) $line->rate * (float) $line->duration, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-3 py-4 text-center text-zinc-500" colspan="8">No crew lines added.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-zinc-200 dark:border-zinc-700">
                            <td class="px-3 py-3 text-right font-medium" colspan="7">Total</td>
                            <td class="px-3 py-3 font-semibold">{{ $quote->currency }} {{ number_format((float) $quote->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-layouts::app>
