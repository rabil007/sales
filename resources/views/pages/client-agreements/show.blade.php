<x-layouts::app :title="__('Agreement') . ': ' . $agreement->agreement_ref">
    <div class="flex h-full flex-col gap-6 p-1 sm:p-2">

        {{-- Header Row --}}
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-100 pb-5 dark:border-zinc-800">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-linear-to-br from-blue-500 to-indigo-600 shadow-md">
                    <flux:icon.document-text class="size-6 text-white" />
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <flux:heading size="2xl" class="font-extrabold tracking-tight leading-none text-zinc-900 dark:text-white">{{ $agreement->agreement_ref }}</flux:heading>
                        @php $isActive = $agreement->end_date && $agreement->end_date->isFuture(); @endphp
                        <span @class([
                            'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset',
                            'bg-emerald-500/10 text-emerald-600 ring-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/30' => $isActive,
                            'bg-rose-500/10 text-rose-600 ring-rose-500/20 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/30' => !$isActive,
                        ])>
                            <span class="mr-1.5 h-2 w-2 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                            {{ $isActive ? 'Active' : 'Expired' }}
                        </span>
                    </div>
                    <flux:text class="text-sm font-semibold text-zinc-550 dark:text-zinc-400 mt-1">{{ $agreement->client->name ?? 'No Client' }}</flux:text>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <flux:button variant="ghost" icon="pencil" :href="route('client-agreements.edit', $agreement)" wire:navigate class="hover:bg-zinc-150 dark:hover:bg-zinc-800">Edit</flux:button>
                <flux:button variant="primary" icon="arrow-left" :href="route('client-agreements.index')" wire:navigate>Back</flux:button>
            </div>
        </div>

        {{-- Compact Stat Strip --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            {{-- Start Date --}}
            <div class="group rounded-xl border border-zinc-200/50 bg-white/40 p-4.5 shadow-xs backdrop-blur-md transition-all duration-200 hover:-translate-y-0.5 hover:bg-white hover:shadow-sm dark:border-zinc-800/60 dark:bg-zinc-900/40 dark:hover:bg-zinc-900/60">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">
                        <flux:icon.calendar class="size-4.5" />
                    </div>
                    <p class="text-xs font-bold uppercase tracking-wider text-zinc-450 dark:text-zinc-500">Start Date</p>
                </div>
                <p class="mt-3.5 text-base font-extrabold text-zinc-900 dark:text-zinc-100">{{ $agreement->start_date?->format('M d, Y') ?? '—' }}</p>
            </div>

            {{-- End Date --}}
            <div class="group rounded-xl border border-zinc-200/50 bg-white/40 p-4.5 shadow-xs backdrop-blur-md transition-all duration-200 hover:-translate-y-0.5 hover:bg-white hover:shadow-sm dark:border-zinc-800/60 dark:bg-zinc-900/40 dark:hover:bg-zinc-900/60">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400">
                        <flux:icon.calendar class="size-4.5" />
                    </div>
                    <p class="text-xs font-bold uppercase tracking-wider text-zinc-450 dark:text-zinc-500">End Date</p>
                </div>
                <p class="mt-3.5 text-base font-extrabold text-zinc-900 dark:text-zinc-100">{{ $agreement->end_date?->format('M d, Y') ?? '—' }}</p>
            </div>

            {{-- Duration --}}
            <div class="group rounded-xl border border-zinc-200/50 bg-white/40 p-4.5 shadow-xs backdrop-blur-md transition-all duration-200 hover:-translate-y-0.5 hover:bg-white hover:shadow-sm dark:border-zinc-800/60 dark:bg-zinc-900/40 dark:hover:bg-zinc-900/60">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400">
                        <flux:icon.clock class="size-4.5" />
                    </div>
                    <p class="text-xs font-bold uppercase tracking-wider text-zinc-450 dark:text-zinc-500">Duration</p>
                </div>
                <p class="mt-3.5 text-base font-extrabold text-zinc-900 dark:text-zinc-100">{{ $agreement->duration_days }} <span class="text-xs font-normal text-zinc-500">days</span></p>
            </div>

            {{-- Crew Lines Count --}}
            <div class="group rounded-xl border border-zinc-200/50 bg-white/40 p-4.5 shadow-xs backdrop-blur-md transition-all duration-200 hover:-translate-y-0.5 hover:bg-white hover:shadow-sm dark:border-zinc-800/60 dark:bg-zinc-900/40 dark:hover:bg-zinc-900/60">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-50 text-violet-600 dark:bg-violet-900/20 dark:text-violet-400">
                        <flux:icon.user-group class="size-4.5" />
                    </div>
                    <p class="text-xs font-bold uppercase tracking-wider text-zinc-450 dark:text-zinc-500">Crew Lines</p>
                </div>
                <p class="mt-3.5 text-base font-extrabold text-zinc-900 dark:text-zinc-100">{{ $agreement->crewLines->count() }} <span class="text-xs font-normal text-zinc-500">{{ Str::plural('line', $agreement->crewLines->count()) }}</span></p>
            </div>

            {{-- Monthly Invoice Value --}}
            <div class="col-span-2 md:col-span-1 rounded-xl border border-emerald-500/30 bg-emerald-500/5 p-4.5 shadow-xs backdrop-blur-md transition-all duration-200 hover:-translate-y-0.5 hover:shadow-sm dark:border-emerald-500/20 dark:bg-emerald-500/5">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500 text-white shadow-xs">
                        <flux:icon.currency-dollar class="size-4.5 text-white" />
                    </div>
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-450">Monthly Invoice Value</p>
                </div>
                <p class="mt-3.5 text-base font-extrabold text-emerald-600 dark:text-emerald-400">${{ number_format($agreement->monthly_invoice_value, 2) }}</p>
            </div>
        </div>

        {{-- Main Body --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- Left Column: Agreement Details Card --}}
            <div class="lg:col-span-1 flex flex-col gap-6">
                <div class="rounded-xl border border-zinc-200/50 bg-white/40 p-5 shadow-xs backdrop-blur-md dark:border-zinc-800/60 dark:bg-zinc-900/40 flex flex-col gap-6">
                    <div>
                        <p class="mb-4 text-xs font-extrabold uppercase tracking-wider text-zinc-450 dark:text-zinc-500">Agreement Details</p>
                        <dl class="space-y-4 text-sm">
                            <div class="flex justify-between items-center gap-3">
                                <dt class="text-zinc-500 dark:text-zinc-400 shrink-0 font-medium text-xs uppercase tracking-wider">Client</dt>
                                <dd class="font-bold text-zinc-900 dark:text-zinc-100 text-right flex items-center gap-2 text-sm">
                                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-zinc-150 text-xs font-bold text-zinc-650 dark:bg-zinc-850 dark:text-zinc-350">
                                        {{ strtoupper(substr($agreement->client->name ?? 'C', 0, 1)) }}
                                    </span>
                                    {{ $agreement->client->name ?? '—' }}
                                </dd>
                            </div>
                            <div class="flex justify-between items-center gap-3 pt-4 border-t border-zinc-150 dark:border-zinc-800">
                                <dt class="text-zinc-500 dark:text-zinc-400 shrink-0 font-medium text-xs uppercase tracking-wider">Reference</dt>
                                <dd class="font-mono font-bold text-zinc-900 dark:text-zinc-100 text-right text-sm bg-zinc-150 dark:bg-zinc-850 px-2.5 py-1 rounded border border-zinc-200/40 dark:border-zinc-700/40">{{ $agreement->agreement_ref }}</dd>
                            </div>
                            @if($agreement->scope_of_work)
                            <div class="pt-4 border-t border-zinc-150 dark:border-zinc-800">
                                <dt class="text-zinc-500 dark:text-zinc-400 mb-2 font-medium text-xs uppercase tracking-wider">Scope of Work</dt>
                                <dd class="text-sm text-zinc-650 dark:text-zinc-300 leading-relaxed bg-zinc-50/50 dark:bg-zinc-950/40 p-4 rounded-lg border border-zinc-150 dark:border-zinc-800/80 italic max-h-60 overflow-y-auto">
                                    "{{ $agreement->scope_of_work }}"
                                </dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    <div class="pt-5 border-t border-zinc-150 dark:border-zinc-800">
                        <p class="mb-3.5 text-xs font-extrabold uppercase tracking-wider text-zinc-450 dark:text-zinc-500">Signed Document</p>
                        @if($agreement->document_path)
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($agreement->document_path) }}" target="_blank"
                               class="flex items-center gap-3.5 rounded-lg border border-blue-200/50 bg-blue-50/30 p-3 transition-all hover:bg-blue-50/60 dark:border-blue-900/30 dark:bg-blue-950/20 dark:hover:bg-blue-950/40 group">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-white shadow-xs transition-transform group-hover:scale-105">
                                    <flux:icon.document-text class="size-5" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-blue-700 dark:text-blue-300">{{ basename($agreement->document_path) }}</p>
                                    <p class="text-xs text-blue-500 dark:text-blue-450 mt-0.5">Click to View / Download</p>
                                </div>
                                <flux:icon.arrow-top-right-on-square class="size-4 shrink-0 text-blue-400 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                            </a>
                        @else
                            <div class="flex flex-col items-center justify-center rounded-lg border border-dashed border-zinc-200 py-8 text-center dark:border-zinc-800/80">
                                <flux:icon.document class="size-7 text-zinc-400 dark:text-zinc-650" />
                                <p class="mt-2.5 text-sm text-zinc-500 dark:text-zinc-400 font-medium">No document uploaded</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Column: Crew Lines Card --}}
            <div class="lg:col-span-2 flex flex-col min-w-0">
                <div class="flex min-h-0 flex-1 flex-col rounded-xl border border-zinc-200/50 bg-white/40 shadow-xs backdrop-blur-md dark:border-zinc-800/60 dark:bg-zinc-900/40 overflow-hidden">
                    <div class="flex items-center justify-between border-b border-zinc-200/50 px-5 py-4 dark:border-zinc-800/60 shrink-0">
                        <p class="text-xs font-extrabold uppercase tracking-wider text-zinc-450 dark:text-zinc-500">Crew Lines</p>
                        @if($agreement->crewLines->count() > 0)
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-zinc-500 dark:text-zinc-400 font-semibold uppercase tracking-wider">Grand Total:</span>
                            <span class="text-sm font-extrabold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-3 py-1 rounded-md">${{ number_format($agreement->crewLines->sum('line_total'), 2) }}</span>
                        </div>
                        @endif
                    </div>

                    @if($agreement->crewLines->count() > 0)
                    <div class="flex-1 overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="sticky top-0 z-10">
                                <tr class="border-b border-zinc-150 bg-zinc-50/90 backdrop-blur-xs dark:border-zinc-800/80 dark:bg-zinc-800/90">
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-zinc-450 dark:text-zinc-500">Rank / Cat.</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider text-zinc-450 dark:text-zinc-500">Qty</th>
                                    <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-wider text-zinc-450 dark:text-zinc-500">Basis</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold uppercase tracking-wider text-zinc-450 dark:text-zinc-500">Rate</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider text-zinc-450 dark:text-zinc-500">OT</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider text-zinc-450 dark:text-zinc-500">Duration</th>
                                    <th class="px-3 py-3 text-left text-xs font-bold uppercase tracking-wider text-zinc-450 dark:text-zinc-500">Mob / Demob</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-zinc-450 dark:text-zinc-500">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80">
                                @foreach($agreement->crewLines as $line)
                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 transition-colors">
                                    <td class="px-4 py-3.5">
                                        <p class="font-bold text-zinc-900 dark:text-zinc-100 text-sm leading-tight">{{ $line->rank ?: '—' }}</p>
                                        <p class="text-xs text-zinc-450 dark:text-zinc-500 mt-1 font-medium">{{ $line->category }}</p>
                                    </td>
                                    <td class="px-3 py-3.5 text-center">
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-blue-500/10 text-xs font-extrabold text-blue-600 dark:bg-blue-500/15 dark:text-blue-400 shadow-xs">{{ $line->qty }}</span>
                                    </td>
                                    <td class="px-3 py-3.5">
                                        <span @class([
                                            'inline-flex items-center rounded-md px-2 py-0.5 text-xs font-bold uppercase tracking-wider ring-1 ring-inset shadow-xs',
                                            'bg-violet-50 text-violet-700 ring-violet-700/10 dark:bg-violet-900/20 dark:text-violet-300 dark:ring-violet-400/20' => $line->basis === 'Month',
                                            'bg-amber-50 text-amber-700 ring-amber-700/10 dark:bg-amber-900/20 dark:text-amber-300 dark:ring-amber-400/20' => $line->basis === 'Fixed',
                                            'bg-sky-50 text-sky-700 ring-sky-700/10 dark:bg-sky-900/20 dark:text-sky-300 dark:ring-sky-400/20' => $line->basis === 'Day',
                                        ])>{{ $line->basis }}</span>
                                    </td>
                                    <td class="px-3 py-3.5 text-right tabular-nums">
                                        @if($line->basis === 'Month')
                                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">${{ number_format($line->monthly_rate, 2) }}</p>
                                            <p class="text-xs text-zinc-450 font-medium">/mo</p>
                                        @elseif($line->basis === 'Fixed')
                                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">${{ number_format($line->manual_total, 2) }}</p>
                                            <p class="text-xs text-zinc-450 font-medium">fixed</p>
                                        @else
                                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">${{ number_format($line->rate, 2) }}</p>
                                            <p class="text-xs text-zinc-450 font-medium">/day</p>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3.5 text-center tabular-nums">
                                        @if($line->ot_rate > 0)
                                            <span class="text-sm font-bold text-amber-600 dark:text-amber-400">${{ number_format($line->ot_rate, 2) }}</span>
                                        @else
                                            <span class="text-zinc-300 dark:text-zinc-700 font-medium">—</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3.5 text-center tabular-nums">
                                        @if($line->duration_days > 0)
                                            <span class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $line->duration_days }}<span class="text-xs text-zinc-450 font-normal ml-0.5">d</span></span>
                                        @elseif($line->duration_months > 0)
                                            <span class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $line->duration_months }}<span class="text-xs text-zinc-450 font-normal ml-0.5">mo</span></span>
                                        @else
                                            <span class="text-zinc-300 dark:text-zinc-700 font-medium">—</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3.5">
                                        @if($line->mob_date || $line->demob_date)
                                            <div class="flex flex-col gap-1">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                    <p class="text-xs text-zinc-900 dark:text-zinc-100 font-semibold tabular-nums">{{ $line->mob_date?->format('M d, Y') ?? '—' }}</p>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                    <p class="text-xs text-zinc-550 dark:text-zinc-400 font-semibold tabular-nums">{{ $line->demob_date?->format('M d, Y') ?? '—' }}</p>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-zinc-300 dark:text-zinc-700 font-medium">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-right tabular-nums">
                                        <div class="flex items-center justify-end gap-2">
                                            <p class="text-sm font-extrabold text-emerald-600 dark:text-emerald-400">${{ number_format($line->line_total, 2) }}</p>
                                            @if($line->remarks)
                                                <flux:tooltip :content="$line->remarks" position="top" align="end">
                                                    <flux:icon.chat-bubble-left-ellipsis class="size-3.5 text-zinc-400 hover:text-zinc-550 transition-colors cursor-pointer" />
                                                </flux:tooltip>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-t border-zinc-200 bg-zinc-50/50 dark:border-zinc-800/80 dark:bg-zinc-800/40">
                                    <td colspan="7" class="px-4 py-4 text-xs font-bold text-zinc-550 dark:text-zinc-400 uppercase tracking-wider">Grand Total</td>
                                    <td class="px-4 py-4 text-right text-base font-extrabold text-emerald-650 dark:text-emerald-400 tabular-nums">
                                        ${{ number_format($agreement->crewLines->sum('line_total'), 2) }}
                                    </td>
                                </tr>
                            </footer>
                        </table>
                    </div>
                    @else
                        <div class="flex flex-1 flex-col items-center justify-center p-12 text-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-zinc-50 dark:bg-zinc-850">
                                <flux:icon.user-group class="size-6 text-zinc-400 dark:text-zinc-650" />
                            </div>
                            <p class="mt-3 text-sm font-semibold text-zinc-650 dark:text-zinc-450">No crew lines added</p>
                            <flux:button size="sm" variant="ghost" icon="pencil" :href="route('client-agreements.edit', $agreement)" wire:navigate class="mt-4">Add Crew Lines</flux:button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>

