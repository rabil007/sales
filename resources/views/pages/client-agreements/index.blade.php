<x-layouts::app :title="__('Client Agreements')">
    <div class="space-y-8">
        @if (session('import_errors'))
            <flux:callout icon="exclamation-triangle" color="amber" class="rounded-2xl">
                <div class="space-y-2">
                    <p class="font-medium">Some rows could not be imported:</p>
                    <ul class="list-inside list-disc space-y-1 text-sm">
                        @foreach (session('import_errors') as $importError)
                            <li>{{ $importError }}</li>
                        @endforeach
                    </ul>
                </div>
            </flux:callout>
        @endif

        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight">Client Agreements</flux:heading>
                <flux:text class="mt-1 text-zinc-500 dark:text-zinc-400">Track client agreements, contract periods, and monthly invoice values.</flux:text>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <flux:modal.trigger name="import-client-agreements">
                    <flux:button variant="ghost" icon="arrow-up-tray" class="rounded-full px-4">Import</flux:button>
                </flux:modal.trigger>

                <flux:dropdown position="bottom" align="end">
                    <flux:button variant="ghost" icon="arrow-down-tray" class="rounded-full px-4">Export</flux:button>

                    <flux:menu>
                        <flux:menu.item
                            icon="table-cells"
                            :href="route('client-agreements.export.excel', request()->only(['q', 'client_id']))"
                        >
                            Export Excel
                        </flux:menu.item>
                        <flux:menu.item
                            icon="document-arrow-down"
                            :href="route('client-agreements.export.pdf', request()->only(['q', 'client_id']))"
                        >
                            Export PDF
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>

                <flux:button variant="primary" icon="plus" :href="route('client-agreements.create')" wire:navigate class="rounded-full px-5 transition-transform hover:-translate-y-0.5 hover:shadow-md">New Agreement</flux:button>
            </div>
        </div>

        <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Total Agreements --}}
            <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" 
                @class([
                    'group relative overflow-hidden rounded-3xl border p-6 shadow-sm backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg',
                    'border-blue-500 ring-2 ring-blue-500/25 bg-blue-50/5 dark:border-blue-500 dark:ring-blue-500/35' => $status === '',
                    'border-zinc-200/60 bg-gradient-to-b from-white/80 to-white/40 dark:border-zinc-700/60 dark:from-zinc-900/80 dark:to-zinc-900/40' => $status !== ''
                ])>
                <div class="absolute -right-8 -top-8 z-0 h-32 w-32 rounded-full bg-blue-500/10 blur-3xl transition-all duration-500 group-hover:scale-150 group-hover:bg-blue-500/20 dark:bg-blue-400/5"></div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-550 dark:text-zinc-400">Total Agreements</p>
                    <p class="mt-2 tabular-nums text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                </div>
            </a>

            {{-- Active Agreements --}}
            <a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}" 
                @class([
                    'group relative overflow-hidden rounded-3xl border p-6 shadow-sm backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg',
                    'border-emerald-500 ring-2 ring-emerald-500/25 bg-emerald-50/5 dark:border-emerald-500 dark:ring-emerald-500/35' => $status === 'active',
                    'border-zinc-200/60 bg-gradient-to-b from-white/80 to-white/40 dark:border-zinc-700/60 dark:from-zinc-900/80 dark:to-zinc-900/40' => $status !== 'active'
                ])>
                <div class="absolute -right-8 -top-8 z-0 h-32 w-32 rounded-full bg-emerald-500/10 blur-3xl transition-all duration-500 group-hover:scale-150 group-hover:bg-emerald-500/20 dark:bg-emerald-400/5"></div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-555 dark:text-zinc-400">Active Agreements</p>
                    <p class="mt-2 tabular-nums text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ number_format($stats['active']) }}</p>
                </div>
            </a>

            {{-- Expired Agreements --}}
            <a href="{{ request()->fullUrlWithQuery(['status' => 'expired']) }}" 
                @class([
                    'group relative overflow-hidden rounded-3xl border p-6 shadow-sm backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg',
                    'border-rose-500 ring-2 ring-rose-500/25 bg-rose-50/5 dark:border-rose-500 dark:ring-rose-500/35' => $status === 'expired',
                    'border-zinc-200/60 bg-gradient-to-b from-white/80 to-white/40 dark:border-zinc-700/60 dark:from-zinc-900/80 dark:to-zinc-900/40' => $status !== 'expired'
                ])>
                <div class="absolute -right-8 -top-8 z-0 h-32 w-32 rounded-full bg-rose-500/10 blur-3xl transition-all duration-500 group-hover:scale-150 group-hover:bg-rose-500/20 dark:bg-rose-400/5"></div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-555 dark:text-zinc-400">Expired Agreements</p>
                    <p class="mt-2 tabular-nums text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ number_format($stats['expired']) }}</p>
                </div>
            </a>

            {{-- Total Monthly Value (USD) --}}
            <div class="group relative overflow-hidden rounded-3xl border border-zinc-200/60 bg-gradient-to-b from-white/80 to-white/40 p-6 shadow-sm backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700/60 dark:from-zinc-900/80 dark:to-zinc-900/40">
                <div class="absolute -right-8 -top-8 z-0 h-32 w-32 rounded-full bg-violet-500/10 blur-3xl transition-all duration-500 group-hover:scale-150 group-hover:bg-violet-500/20 dark:bg-violet-400/5"></div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total Monthly Value (USD)</p>
                    <p class="mt-2 tabular-nums text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ number_format($stats['totalMonthlyValue'], 2) }}</p>
                </div>
            </div>
        </div>

        <form method="GET" class="grid gap-4 rounded-3xl border border-zinc-200/60 bg-white/60 p-6 shadow-sm backdrop-blur-xl md:grid-cols-3 dark:border-zinc-700/60 dark:bg-zinc-900/60">
            @if ($status !== '')
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <flux:input name="q" :value="$q" placeholder="Search client, ref, scope..." icon="magnifying-glass" />
            <flux:select name="client_id">
                <option value="">All Clients</option>
                @foreach ($clients as $clientOption)
                    <option value="{{ $clientOption->id }}" @selected($clientId === (string) $clientOption->id)>{{ $clientOption->name }}</option>
                @endforeach
            </flux:select>
            <div class="flex items-center gap-2">
                <flux:button type="submit" variant="filled" icon="funnel" class="rounded-full px-4">Filter</flux:button>
                @if ($q !== '' || $clientId !== '' || $status !== '' || $perPage !== 15)
                    <flux:button variant="ghost" :href="route('client-agreements.index')" wire:navigate class="rounded-full">Clear</flux:button>
                @endif
            </div>
        </form>

        <div class="overflow-hidden rounded-3xl border border-zinc-200/60 bg-white/60 shadow-sm backdrop-blur-xl dark:border-zinc-700/60 dark:bg-zinc-900/60">
            <div class="overflow-x-auto p-2">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            <th class="px-4 py-3.5">Sl. No.</th>
                            <th class="px-4 py-3.5">Client Name</th>
                            <th class="px-4 py-3.5">Agreement Ref.</th>
                            <th class="px-4 py-3.5">Scope of Work</th>
                            <th class="px-4 py-3.5">Duration (days)</th>
                            <th class="px-4 py-3.5">Start Date</th>
                            <th class="px-4 py-3.5">End Date</th>
                            <th class="px-4 py-3.5">Status</th>
                            <th class="px-4 py-3.5">Monthly Invoice Value (USD)</th>
                            <th class="px-4 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-700/60">
                        @forelse ($agreements as $agreement)
                            <tr onclick="window.location='{{ route('client-agreements.show', $agreement) }}'" class="group cursor-pointer transition-colors hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                                <td class="px-4 py-4 tabular-nums text-zinc-600 dark:text-zinc-300">{{ ($agreements->firstItem() ?? 0) + $loop->index }}</td>
                                <td class="px-4 py-4 font-medium text-zinc-900 dark:text-white">{{ $agreement->client->name }}</td>
                                <td class="px-4 py-4 text-zinc-600 dark:text-zinc-300">{{ $agreement->agreement_ref }}</td>
                                <td class="max-w-xs truncate px-4 py-4 text-zinc-600 dark:text-zinc-300" title="{{ $agreement->scope_of_work }}">{{ $agreement->scope_of_work ?: '-' }}</td>
                                <td class="px-4 py-4 tabular-nums text-zinc-600 dark:text-zinc-300">{{ number_format($agreement->duration_days) }}</td>
                                <td class="px-4 py-4 tabular-nums text-zinc-600 dark:text-zinc-300">{{ $agreement->start_date->format('d M Y') }}</td>
                                <td class="px-4 py-4 tabular-nums text-zinc-600 dark:text-zinc-300">{{ $agreement->end_date->format('d M Y') }}</td>
                                <td class="px-4 py-4">
                                    @php
                                        $isExpired = $agreement->end_date->isBefore(today());
                                    @endphp
                                    <span @class([
                                        'inline-flex items-center rounded-md px-2 py-0.5 text-xs font-bold uppercase tracking-wider ring-1 ring-inset shadow-xs',
                                        'bg-emerald-50 text-emerald-700 ring-emerald-600/10 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20' => ! $isExpired,
                                        'bg-rose-50 text-rose-700 ring-rose-600/10 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20' => $isExpired,
                                    ])>
                                        {{ $isExpired ? 'Expired' : 'Active' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 tabular-nums text-zinc-600 dark:text-zinc-300">{{ number_format($agreement->monthly_invoice_value, 2) }}</td>
                                <td class="px-4 py-4 text-right" onclick="event.stopPropagation()">
                                    <div class="flex items-center justify-end gap-1">
                                        <flux:tooltip content="View Details">
                                            <flux:button size="sm" icon="eye" variant="ghost" :href="route('client-agreements.show', $agreement)" wire:navigate class="size-8! rounded-full p-0! hover:bg-zinc-100 dark:hover:bg-zinc-800" />
                                        </flux:tooltip>
                                        <flux:tooltip content="Edit">
                                            <flux:button size="sm" icon="pencil" variant="ghost" :href="route('client-agreements.edit', $agreement)" wire:navigate class="size-8! rounded-full p-0! hover:bg-zinc-100 dark:hover:bg-zinc-800" />
                                        </flux:tooltip>
                                        <flux:tooltip content="Delete">
                                            <flux:modal.trigger :name="'delete-agreement-'.$agreement->id">
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
                                <td class="px-4 py-16 text-center text-zinc-500" colspan="10">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                                            <flux:icon.clipboard-document-list class="size-6 opacity-40" />
                                        </div>
                                        <p class="font-medium text-zinc-600 dark:text-zinc-400">No client agreements found.</p>
                                        <p class="text-sm">Add your first agreement to start tracking contract periods.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-zinc-200/60 bg-white/40 px-6 py-4 text-sm text-zinc-500 dark:border-zinc-700/60 dark:bg-zinc-800/20">
                <p>
                    Showing <span class="font-medium text-zinc-900 dark:text-white">{{ $agreements->firstItem() ?? 0 }}</span>-<span class="font-medium text-zinc-900 dark:text-white">{{ $agreements->lastItem() ?? 0 }}</span> of <span class="font-medium text-zinc-900 dark:text-white">{{ $agreements->total() }}</span> agreements
                </p>
                <div class="flex flex-wrap items-center gap-3">
                    <form method="GET" action="{{ route('client-agreements.index') }}" class="flex items-center gap-2">
                        @if ($q !== '')
                            <input type="hidden" name="q" value="{{ $q }}">
                        @endif
                        @if ($clientId !== '')
                            <input type="hidden" name="client_id" value="{{ $clientId }}">
                        @endif
                        <flux:select name="per_page" size="sm" onchange="this.form.submit()" class="rounded-full">
                            @foreach ([10, 15, 25, 50] as $size)
                                <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }} / page</option>
                            @endforeach
                        </flux:select>
                    </form>
                    <span>Page {{ $agreements->currentPage() }} of {{ $agreements->lastPage() }}</span>
                    {{ $agreements->withQueryString()->links() }}
                </div>
            </div>
        </div>

        @foreach ($agreements as $agreement)
            <flux:modal :name="'delete-agreement-'.$agreement->id" class="max-w-md">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Delete agreement?</flux:heading>
                        <flux:subheading>
                            This action cannot be undone. Agreement <span class="font-semibold">{{ $agreement->agreement_ref }}</span> will be permanently deleted.
                        </flux:subheading>
                    </div>

                    <div class="flex justify-end gap-2">
                        <flux:modal.close>
                            <flux:button variant="filled" class="rounded-full">Cancel</flux:button>
                        </flux:modal.close>

                        <form method="POST" action="{{ route('client-agreements.destroy', $agreement) }}">
                            @csrf
                            @method('DELETE')
                            <flux:button variant="danger" type="submit" class="rounded-full">Delete</flux:button>
                        </form>
                    </div>
                </div>
            </flux:modal>
        @endforeach

        <flux:modal name="import-client-agreements" class="max-w-lg">
            <form
                method="POST"
                action="{{ route('client-agreements.import') }}"
                enctype="multipart/form-data"
                class="space-y-6"
                x-data="{
                    dragging: false,
                    fileName: '',
                    setFile(file) {
                        if (! file) {
                            return;
                        }

                        const input = this.$refs.fileInput;
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        input.files = dataTransfer.files;
                        this.fileName = file.name;
                    },
                }"
            >
                @csrf

                <div>
                    <flux:heading size="lg">Import Client Agreements</flux:heading>
                    <flux:subheading>Upload an Excel file using the import template. Client names must already exist in the system.</flux:subheading>
                </div>

                <flux:callout icon="information-circle" color="blue" class="rounded-2xl">
                    Download the template, fill in your agreement rows, then upload the `.xlsx` file. End dates are calculated automatically from start date and duration.
                </flux:callout>

                <div class="flex justify-start">
                    <flux:button
                        variant="ghost"
                        icon="arrow-down-tray"
                        :href="route('client-agreements.import.template')"
                        class="rounded-full"
                    >
                        Download template
                    </flux:button>
                </div>

                <div
                    class="rounded-2xl border-2 border-dashed p-8 text-center transition-colors"
                    :class="dragging ? 'border-blue-400 bg-blue-50/50 dark:border-blue-500 dark:bg-blue-950/20' : 'border-zinc-300 bg-zinc-50/50 dark:border-zinc-600 dark:bg-zinc-800/20'"
                    @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false"
                    @drop.prevent="dragging = false; setFile($event.dataTransfer.files[0])"
                    @click="$refs.fileInput.click()"
                >
                    <input
                        x-ref="fileInput"
                        type="file"
                        name="file"
                        accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                        class="hidden"
                        @change="fileName = $event.target.files[0]?.name ?? ''"
                    />

                    <div class="flex flex-col items-center gap-3">
                        <div class="flex size-12 items-center justify-center rounded-full bg-white shadow-sm dark:bg-zinc-800">
                            <flux:icon.arrow-up-tray class="size-6 text-zinc-400" />
                        </div>
                        <div>
                            <p class="font-medium text-zinc-700 dark:text-zinc-200">Drag and drop your Excel file here</p>
                            <p class="mt-1 text-sm text-zinc-500">or click to browse · `.xlsx` only</p>
                        </div>
                        <p x-show="fileName" x-text="fileName" class="text-sm font-medium text-blue-600 dark:text-blue-400"></p>
                    </div>
                </div>

                @error('file')
                    <flux:text class="text-red-500">{{ $message }}</flux:text>
                @enderror

                <div class="flex justify-end gap-2">
                    <flux:modal.close>
                        <flux:button variant="filled" type="button" class="rounded-full">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button variant="primary" type="submit" icon="arrow-up-tray" class="rounded-full">Import agreements</flux:button>
                </div>
            </form>
        </flux:modal>
    </div>
</x-layouts::app>
