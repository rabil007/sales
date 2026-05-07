<x-layouts::app :title="__('Quote Preview')">
    <div class="space-y-4">
        <div class="flex items-center justify-between no-print">
            <flux:heading size="lg">Quote Preview</flux:heading>
            <div class="flex gap-2">
                <flux:button variant="filled" :href="route('quotes.edit', $quote)" wire:navigate>Edit Quote</flux:button>
                <flux:button variant="primary" onclick="window.print()" type="button">Print / Export PDF</flux:button>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 no-print">
            <form method="POST" action="{{ route('quotes.send', $quote) }}">@csrf <flux:button size="sm" type="submit" variant="ghost">Mark Sent</flux:button></form>
            <form method="POST" action="{{ route('quotes.approve', $quote) }}">@csrf <flux:button size="sm" type="submit" variant="ghost">Approve</flux:button></form>
            <form method="POST" action="{{ route('quotes.activate', $quote) }}">@csrf <flux:button size="sm" type="submit" variant="ghost">Activate</flux:button></form>
            <form method="POST" action="{{ route('quotes.expire', $quote) }}">@csrf <flux:button size="sm" type="submit" variant="ghost">Expire</flux:button></form>
            <form method="POST" action="{{ route('quotes.renew', $quote) }}">@csrf <flux:button size="sm" type="submit" variant="filled">Renew</flux:button></form>
        </div>

        <div class="mx-auto max-w-4xl rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-6 flex items-start justify-between">
                <div>
                    <h1 class="text-xl font-semibold">Overseas Marine Services</h1>
                    <p class="text-sm text-zinc-500">{{ in_array($quote->status, ['Draft', 'Sent'], true) ? 'Proposal / Quotation' : 'Crew Supply Agreement / Purchase Order' }}</p>
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
                <p><span class="text-zinc-500">Contract Duration:</span> {{ $quote->duration_text ?: '-' }}</p>
                <p><span class="text-zinc-500">Project Name:</span> {{ $quote->project_name ?: '-' }}</p>
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
                    <p class="font-medium">Terms & Conditions</p>
                    <p class="mt-2 text-zinc-600 dark:text-zinc-300">{{ $quote->terms_conditions ?: '-' }}</p>
                </div>
                <div>
                    <p class="font-medium">Special Conditions</p>
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
