<x-layouts::app :title="__('Dashboard')">
    <div class="space-y-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight">Dashboard</flux:heading>
                <flux:text class="mt-1 text-zinc-500 dark:text-zinc-400">Monitor quote performance, value trends, and agreement pipeline.</flux:text>
            </div>
        </div>

        {{-- Metric Cards --}}
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            {{-- Total Quotes --}}
            <div class="group relative overflow-hidden rounded-3xl border border-zinc-200/60 bg-gradient-to-b from-white/80 to-white/40 p-6 shadow-sm backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700/60 dark:from-zinc-900/80 dark:to-zinc-900/40">
                <div class="absolute -right-8 -top-8 z-0 h-32 w-32 rounded-full bg-blue-500/10 blur-3xl transition-all duration-500 group-hover:scale-150 group-hover:bg-blue-500/20 dark:bg-blue-400/5"></div>
                <div class="relative z-10 flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50/80 text-blue-600 shadow-inner ring-1 ring-blue-100/50 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20">
                        <flux:icon.document-text class="size-6" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total Quotes</p>
                        <p class="mt-1.5 tabular-nums text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ number_format($totalQuotes) }}</p>
                    </div>
                </div>
            </div>

            {{-- Active Agreements --}}
            <div class="group relative overflow-hidden rounded-3xl border border-zinc-200/60 bg-gradient-to-b from-white/80 to-white/40 p-6 shadow-sm backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700/60 dark:from-zinc-900/80 dark:to-zinc-900/40">
                <div class="absolute -right-8 -top-8 z-0 h-32 w-32 rounded-full bg-emerald-500/10 blur-3xl transition-all duration-500 group-hover:scale-150 group-hover:bg-emerald-500/20 dark:bg-emerald-400/5"></div>
                <div class="relative z-10 flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50/80 text-emerald-600 shadow-inner ring-1 ring-emerald-100/50 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20">
                        <flux:icon.check-circle class="size-6" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Active Agreements</p>
                        <p class="mt-1.5 tabular-nums text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ number_format($activeAgreements) }}</p>
                    </div>
                </div>
            </div>

            {{-- Pending Approval --}}
            <div class="group relative overflow-hidden rounded-3xl border border-zinc-200/60 bg-gradient-to-b from-white/80 to-white/40 p-6 shadow-sm backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700/60 dark:from-zinc-900/80 dark:to-zinc-900/40">
                <div class="absolute -right-8 -top-8 z-0 h-32 w-32 rounded-full bg-amber-500/10 blur-3xl transition-all duration-500 group-hover:scale-150 group-hover:bg-amber-500/20 dark:bg-amber-400/5"></div>
                <div class="relative z-10 flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50/80 text-amber-600 shadow-inner ring-1 ring-amber-100/50 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20">
                        <flux:icon.clock class="size-6" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Pending Approval</p>
                        <p class="mt-1.5 tabular-nums text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ number_format($pendingApproval) }}</p>
                    </div>
                </div>
            </div>

            {{-- Total Value --}}
            <div class="group relative overflow-hidden rounded-3xl border border-zinc-200/60 bg-gradient-to-b from-white/80 to-white/40 p-6 shadow-sm backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700/60 dark:from-zinc-900/80 dark:to-zinc-900/40">
                <div class="absolute -right-8 -top-8 z-0 h-32 w-32 rounded-full bg-violet-500/10 blur-3xl transition-all duration-500 group-hover:scale-150 group-hover:bg-violet-500/20 dark:bg-violet-400/5"></div>
                <div class="relative z-10 flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50/80 text-violet-600 shadow-inner ring-1 ring-violet-100/50 dark:bg-violet-500/10 dark:text-violet-400 dark:ring-violet-500/20">
                        <flux:icon.currency-dollar class="size-6" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total Value</p>
                        <p class="mt-1.5 tabular-nums text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">AED {{ number_format((float) $totalValue, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mid Stats --}}
        <div class="grid gap-6 xl:grid-cols-3">
            <div class="group relative overflow-hidden rounded-3xl border border-zinc-200/60 bg-white/60 p-6 shadow-sm backdrop-blur-xl transition-all duration-300 hover:shadow-md dark:border-zinc-700/60 dark:bg-zinc-900/60">
                <div class="absolute -bottom-10 -right-10 z-0 h-40 w-40 rounded-full bg-zinc-500/5 blur-3xl transition-all duration-500 group-hover:scale-125 dark:bg-zinc-400/5"></div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Average Quote Value</p>
                    <p class="mt-2 tabular-nums text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">AED {{ number_format((float) $averageQuoteValue, 2) }}</p>
                    <p class="mt-2 flex items-center gap-1.5 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        <flux:icon.chart-bar class="size-3.5" /> Based on all quotes
                    </p>
                </div>
            </div>
            
            <div class="group relative overflow-hidden rounded-3xl border border-zinc-200/60 bg-white/60 p-6 shadow-sm backdrop-blur-xl transition-all duration-300 hover:shadow-md dark:border-zinc-700/60 dark:bg-zinc-900/60">
                <div class="absolute -bottom-10 -right-10 z-0 h-40 w-40 rounded-full bg-rose-500/5 blur-3xl transition-all duration-500 group-hover:scale-125 dark:bg-rose-400/5"></div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Expiring in 30 Days</p>
                    <p class="mt-2 tabular-nums text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ number_format($expiringSoon) }}</p>
                    <p class="mt-2 flex items-center gap-1.5 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        <flux:icon.calendar-days class="size-3.5" /> Requires commercial follow-up
                    </p>
                </div>
            </div>
            
            <div class="group relative overflow-hidden rounded-3xl border border-zinc-200/60 bg-white/60 p-6 shadow-sm backdrop-blur-xl transition-all duration-300 hover:shadow-md dark:border-zinc-700/60 dark:bg-zinc-900/60">
                <div class="absolute -bottom-10 -right-10 z-0 h-40 w-40 rounded-full bg-indigo-500/5 blur-3xl transition-all duration-500 group-hover:scale-125 dark:bg-indigo-400/5"></div>
                <div class="relative z-10 flex h-full flex-col">
                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Status Mix</p>
                    <div class="mt-4 flex flex-1 flex-col justify-between space-y-3">
                        <div class="flex items-center justify-between rounded-xl p-1 transition-colors hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                            <div class="flex items-center gap-2.5">
                                <div class="h-2 w-2 rounded-full bg-zinc-400 shadow-[0_0_8px_rgba(161,161,170,0.6)]"></div>
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">Draft</span>
                            </div>
                            <span class="text-base font-semibold tabular-nums text-zinc-900 dark:text-white">{{ $draftCount }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl p-1 transition-colors hover:bg-blue-50/50 dark:hover:bg-blue-900/10">
                            <div class="flex items-center gap-2.5">
                                <div class="h-2 w-2 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.6)]"></div>
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">Sent</span>
                            </div>
                            <span class="text-base font-semibold tabular-nums text-zinc-900 dark:text-white">{{ $sentCount }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl p-1 transition-colors hover:bg-emerald-50/50 dark:hover:bg-emerald-900/10">
                            <div class="flex items-center gap-2.5">
                                <div class="h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]"></div>
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">Approved</span>
                            </div>
                            <span class="text-base font-semibold tabular-nums text-zinc-900 dark:text-white">{{ $approvedCount }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl p-1 transition-colors hover:bg-rose-50/50 dark:hover:bg-rose-900/10">
                            <div class="flex items-center gap-2.5">
                                <div class="h-2 w-2 rounded-full bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.6)]"></div>
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">Expired</span>
                            </div>
                            <span class="text-base font-semibold tabular-nums text-zinc-900 dark:text-white">{{ $expiredCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts / Data Grids --}}
        <div class="grid gap-6 xl:grid-cols-2">
            <div class="flex flex-col rounded-3xl border border-zinc-200/60 bg-white/60 shadow-sm backdrop-blur-xl dark:border-zinc-700/60 dark:bg-zinc-900/60">
                <div class="border-b border-zinc-200/60 px-6 py-5 dark:border-zinc-700/60">
                    <flux:heading size="md" class="font-semibold">Top Clients by Quote Value</flux:heading>
                </div>
                <div class="flex-1 overflow-x-auto p-2">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                <th class="px-4 py-3">Client</th>
                                <th class="px-4 py-3">Quotes</th>
                                <th class="px-4 py-3 text-right">Total Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-700/60">
                            @forelse ($topClients as $client)
                                <tr class="transition-colors hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                                    <td class="px-4 py-3.5 font-medium text-zinc-900 dark:text-white">{{ $client->client_name }}</td>
                                    <td class="px-4 py-3.5 text-zinc-600 dark:text-zinc-300">
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-zinc-100 text-xs font-medium dark:bg-zinc-800">{{ $client->quotes_count }}</span>
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-medium text-zinc-900 dark:text-white">AED {{ number_format((float) $client->total_value, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-8 text-center text-zinc-500" colspan="3">No client analytics available yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex flex-col rounded-3xl border border-zinc-200/60 bg-white/60 shadow-sm backdrop-blur-xl dark:border-zinc-700/60 dark:bg-zinc-900/60">
                <div class="border-b border-zinc-200/60 px-6 py-5 dark:border-zinc-700/60">
                    <flux:heading size="md" class="font-semibold">Monthly Quote Value <span class="font-normal text-zinc-400">(Last 6 Months)</span></flux:heading>
                </div>
                <div class="flex-1 p-6">
                    <div class="space-y-5">
                        @foreach ($monthlyChart as $monthLabel => $monthTotal)
                            @php
                                $maxMonthlyValue = max((float) $monthlyChart->max(), 1);
                                $barWidth = max((int) round(($monthTotal / $maxMonthlyValue) * 100), 2);
                            @endphp
                            <div class="group/bar">
                                <div class="mb-1.5 flex items-center justify-between text-sm">
                                    <span class="font-medium text-zinc-600 dark:text-zinc-400">{{ $monthLabel }}</span>
                                    <span class="tabular-nums font-semibold text-zinc-900 dark:text-white">AED {{ number_format((float) $monthTotal, 2) }}</span>
                                </div>
                                <div class="h-2.5 overflow-hidden rounded-full bg-zinc-100/80 shadow-inner dark:bg-zinc-800/80">
                                    <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 transition-all duration-1000 ease-out group-hover/bar:brightness-110" style="width: {{ $barWidth }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="flex flex-col rounded-3xl border border-zinc-200/60 bg-white/60 shadow-sm backdrop-blur-xl dark:border-zinc-700/60 dark:bg-zinc-900/60">
                <div class="border-b border-zinc-200/60 px-6 py-5 dark:border-zinc-700/60">
                    <flux:heading size="md" class="font-semibold">Monthly Count</flux:heading>
                </div>
                <div class="flex-1 p-6">
                    @php
                        $maxMonthlyCount = max((int) $monthlyCountChart->max(), 1);
                    @endphp
                    <div class="space-y-4">
                        @foreach ($monthlyCountChart as $monthLabel => $monthCount)
                            @php
                                $barWidth = max((int) round(($monthCount / $maxMonthlyCount) * 100), 4);
                            @endphp
                            <div class="group/bar">
                                <div class="mb-1.5 flex items-center justify-between text-sm">
                                    <span class="font-medium text-zinc-600 dark:text-zinc-400">{{ $monthLabel }}</span>
                                    <span class="tabular-nums font-semibold text-zinc-900 dark:text-white">{{ $monthCount }}</span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-zinc-100/80 shadow-inner dark:bg-zinc-800/80">
                                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-emerald-600 transition-all duration-1000 ease-out group-hover/bar:brightness-110" style="width: {{ $barWidth }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex flex-col rounded-3xl border border-zinc-200/60 bg-white/60 shadow-sm backdrop-blur-xl dark:border-zinc-700/60 dark:bg-zinc-900/60">
                <div class="border-b border-zinc-200/60 px-6 py-5 dark:border-zinc-700/60">
                    <flux:heading size="md" class="font-semibold">Status Distribution</flux:heading>
                </div>
                <div class="flex-1 p-6">
                    @php
                        $maxStatusCount = max((int) $statusChart->max(), 1);
                    @endphp
                    <div class="space-y-4">
                        @foreach ($statusChart as $statusLabel => $statusCount)
                            @php
                                $barWidth = max((int) round(($statusCount / $maxStatusCount) * 100), 4);
                            @endphp
                            <div class="group/bar">
                                <div class="mb-1.5 flex items-center justify-between text-sm">
                                    <span class="font-medium text-zinc-600 dark:text-zinc-400">{{ $statusLabel }}</span>
                                    <span class="tabular-nums font-semibold text-zinc-900 dark:text-white">{{ $statusCount }}</span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-zinc-100/80 shadow-inner dark:bg-zinc-800/80">
                                    <div class="h-full rounded-full bg-gradient-to-r from-violet-400 to-violet-600 transition-all duration-1000 ease-out group-hover/bar:brightness-110" style="width: {{ $barWidth }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex flex-col rounded-3xl border border-zinc-200/60 bg-white/60 shadow-sm backdrop-blur-xl dark:border-zinc-700/60 dark:bg-zinc-900/60">
                <div class="border-b border-zinc-200/60 px-6 py-5 dark:border-zinc-700/60">
                    <flux:heading size="md" class="font-semibold">Agreement Type Mix</flux:heading>
                </div>
                <div class="flex-1 p-6">
                    @php
                        $maxTypeCount = max((int) $typeMixChart->max(), 1);
                    @endphp
                    <div class="space-y-4">
                        @forelse ($typeMixChart as $typeLabel => $typeCount)
                            @php
                                $barWidth = max((int) round(($typeCount / $maxTypeCount) * 100), 4);
                            @endphp
                            <div class="group/bar">
                                <div class="mb-1.5 flex items-center justify-between text-sm">
                                    <span class="font-medium text-zinc-600 dark:text-zinc-400">{{ $typeLabel }}</span>
                                    <span class="tabular-nums font-semibold text-zinc-900 dark:text-white">{{ $typeCount }}</span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-zinc-100/80 shadow-inner dark:bg-zinc-800/80">
                                    <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-amber-600 transition-all duration-1000 ease-out group-hover/bar:brightness-110" style="width: {{ $barWidth }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="flex h-full items-center justify-center">
                                <p class="text-sm text-zinc-500">No type analytics available yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Quotes Table --}}
        <div class="overflow-hidden rounded-3xl border border-zinc-200/60 bg-white/60 shadow-sm backdrop-blur-xl dark:border-zinc-700/60 dark:bg-zinc-900/60">
            <div class="flex items-center justify-between border-b border-zinc-200/60 px-6 py-5 dark:border-zinc-700/60">
                <flux:heading size="md" class="font-semibold">Recent Quotes & Agreements</flux:heading>
                <flux:button size="sm" variant="filled" :href="route('quotes.index')" wire:navigate class="rounded-full px-4">View All</flux:button>
            </div>
            <div class="overflow-x-auto p-2">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            <th class="px-4 py-3.5">Doc No</th>
                            <th class="px-4 py-3.5">Client</th>
                            <th class="px-4 py-3.5">Type</th>
                            <th class="px-4 py-3.5">Issue Date</th>
                            <th class="px-4 py-3.5 text-right">Total</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-700/60">
                        @forelse ($recentQuotes as $quote)
                            <tr class="group/row transition-colors hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                                <td class="px-4 py-4">
                                    <a href="{{ route('quotes.show', $quote) }}" class="inline-flex items-center gap-1.5 font-mono text-xs font-semibold text-blue-600 transition-colors hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ $quote->doc_no }}
                                        <flux:icon.arrow-top-right-on-square class="size-3 opacity-0 transition-opacity group-hover/row:opacity-100" />
                                    </a>
                                </td>
                                <td class="px-4 py-4 font-medium text-zinc-900 dark:text-white">{{ $quote->client_name }}</td>
                                <td class="px-4 py-4 text-zinc-600 dark:text-zinc-300">{{ $quote->type }}</td>
                                <td class="px-4 py-4 text-zinc-600 dark:text-zinc-300">{{ optional($quote->issue_date)->format('M d, Y') }}</td>
                                <td class="px-4 py-4 text-right font-medium tabular-nums text-zinc-900 dark:text-white">{{ $quote->currency }} {{ number_format((float) $quote->total_amount, 2) }}</td>
                                <td class="px-4 py-4 text-center">
                                    @php
                                        $color = match($quote->status) {
                                            'Active'   => 'emerald',
                                            'Approved' => 'lime',
                                            'Sent'     => 'blue',
                                            'Expired'  => 'rose',
                                            default    => 'zinc',
                                        };
                                    @endphp
                                    <flux:badge :color="$color" size="sm" class="font-medium shadow-sm">{{ $quote->status }}</flux:badge>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-12 text-center text-zinc-500" colspan="6">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                                            <flux:icon.document-text class="size-6 opacity-40" />
                                        </div>
                                        <span class="text-sm font-medium">No quotes created yet.</span>
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
