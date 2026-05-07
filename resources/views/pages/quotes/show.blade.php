<x-layouts::app :title="__('Quote Preview')">
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4 no-print">
            <div>
                <flux:heading size="lg">Quote Preview</flux:heading>
                <flux:text class="text-zinc-500">Commercial document view ready for sharing and print export.</flux:text>
            </div>
            <div class="flex items-center gap-2">
                <flux:button variant="ghost" icon="pencil" :href="route('quotes.edit', $quote)" wire:navigate>Edit Quote</flux:button>
                <flux:button variant="primary" icon="printer" onclick="window.print()" type="button">Print / Export PDF</flux:button>
            </div>
        </div>

        <div class="mx-auto max-w-5xl rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-8 flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Overseas Marine Services</h1>
                    <p class="text-sm text-zinc-500">{{ in_array($quote->status, ['Draft', 'Sent'], true) ? 'Proposal / Quotation' : 'Crew Supply Agreement / Purchase Order' }}</p>
                </div>
                <div class="text-right space-y-1">
                    <p class="font-mono text-sm font-semibold">{{ $quote->doc_no }}</p>
                    <p class="text-sm text-zinc-500">Issued: {{ optional($quote->issue_date)->toDateString() }}</p>
                    <flux:badge :color="match($quote->status) {
                        'Active' => 'green',
                        'Approved' => 'lime',
                        'Sent' => 'blue',
                        'Expired' => 'red',
                        default => 'zinc'
                    }" size="sm">
                        {{ $quote->status }}
                    </flux:badge>
                </div>
            </div>

            <div class="mb-8 rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/60">
                <div class="grid gap-3 text-sm md:grid-cols-2">
                    <p><span class="text-zinc-500">Client:</span> <span class="font-medium">{{ $quote->client_name }}</span></p>
                    <p><span class="text-zinc-500">Payment Terms:</span> <span class="font-medium">{{ $quote->payment_terms ?: '-' }}</span></p>
                    <p><span class="text-zinc-500">Vessel / Project:</span> <span class="font-medium">{{ $quote->vessel ?: '-' }}</span></p>
                    <p><span class="text-zinc-500">Project Name:</span> <span class="font-medium">{{ $quote->project_name ?: '-' }}</span></p>
                    <p><span class="text-zinc-500">Location:</span> <span class="font-medium">{{ $quote->location ?: '-' }}</span></p>
                    <p><span class="text-zinc-500">Contract Duration:</span> <span class="font-medium">{{ $quote->duration_text ?: '-' }}</span></p>
                </div>
            </div>

            <div class="mb-8 text-sm leading-6">
                <p class="mb-2 font-semibold">Scope of Services</p>
                <p class="text-zinc-600 dark:text-zinc-300">{{ $quote->scope ?: '-' }}</p>
            </div>

            <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700">
                <table class="min-w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/80">
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
                                <td class="px-3 py-2">
                                    @if ($line->basis === 'Month')
                                        {{ $quote->currency }} {{ number_format((float) $line->monthly_rate, 2) }}
                                    @elseif ($line->basis === 'Fixed')
                                        {{ $quote->currency }} {{ number_format((float) $line->manual_total, 2) }}
                                    @else
                                        {{ $quote->currency }} {{ number_format((float) $line->rate, 2) }}
                                    @endif
                                </td>
                                <td class="px-3 py-2">{{ $line->basis === 'Month' ? $line->duration_months : $line->duration_days }}</td>
                                <td class="px-3 py-2">
                                    {{ $quote->currency }} {{ number_format((float) $line->line_total, 2) }}
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

            <div class="mt-8 grid gap-6 text-sm md:grid-cols-2">
                <div>
                    <p class="font-semibold">Terms & Conditions</p>
                    <p class="mt-2 text-zinc-600 dark:text-zinc-300">{{ $quote->terms_conditions ?: '-' }}</p>
                </div>
                <div>
                    <p class="font-semibold">Special Conditions</p>
                    <p class="mt-2 text-zinc-600 dark:text-zinc-300">{{ $quote->special_conditions ?: '-' }}</p>
                </div>
            </div>

            <div class="mt-10 grid gap-8 text-sm md:grid-cols-2">
                <div class="border-t border-zinc-300 pt-3 dark:border-zinc-600">
                    <p class="font-medium">Authorized Signatory (Client)</p>
                    <p class="text-zinc-500">Name / Signature / Date</p>
                </div>
                <div class="border-t border-zinc-300 pt-3 dark:border-zinc-600">
                    <p class="font-medium">Authorized Signatory (OMS)</p>
                    <p class="text-zinc-500">Name / Signature / Date</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
