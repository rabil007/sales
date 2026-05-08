<x-layouts::app :title="__('Clients')">
    <div class="space-y-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight">Clients</flux:heading>
                <flux:text class="mt-1 text-zinc-500 dark:text-zinc-400">Manage client master data for quote selection.</flux:text>
            </div>
            <flux:button variant="primary" icon="plus" :href="route('clients.create')" wire:navigate class="rounded-full px-5 transition-transform hover:-translate-y-0.5 hover:shadow-md">New Client</flux:button>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            <div class="group relative overflow-hidden rounded-3xl border border-zinc-200/60 bg-gradient-to-b from-white/80 to-white/40 p-6 shadow-sm backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700/60 dark:from-zinc-900/80 dark:to-zinc-900/40">
                <div class="absolute -right-8 -top-8 z-0 h-32 w-32 rounded-full bg-blue-500/10 blur-3xl transition-all duration-500 group-hover:scale-150 group-hover:bg-blue-500/20 dark:bg-blue-400/5"></div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total Clients</p>
                    <p class="mt-2 tabular-nums text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
            <div class="group relative overflow-hidden rounded-3xl border border-zinc-200/60 bg-gradient-to-b from-white/80 to-white/40 p-6 shadow-sm backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700/60 dark:from-zinc-900/80 dark:to-zinc-900/40">
                <div class="absolute -right-8 -top-8 z-0 h-32 w-32 rounded-full bg-emerald-500/10 blur-3xl transition-all duration-500 group-hover:scale-150 group-hover:bg-emerald-500/20 dark:bg-emerald-400/5"></div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">With Email</p>
                    <p class="mt-2 tabular-nums text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ number_format($stats['withEmail']) }}</p>
                </div>
            </div>
            <div class="group relative overflow-hidden rounded-3xl border border-zinc-200/60 bg-gradient-to-b from-white/80 to-white/40 p-6 shadow-sm backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700/60 dark:from-zinc-900/80 dark:to-zinc-900/40">
                <div class="absolute -right-8 -top-8 z-0 h-32 w-32 rounded-full bg-violet-500/10 blur-3xl transition-all duration-500 group-hover:scale-150 group-hover:bg-violet-500/20 dark:bg-violet-400/5"></div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">With Phone</p>
                    <p class="mt-2 tabular-nums text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ number_format($stats['withPhone']) }}</p>
                </div>
            </div>
        </div>

        <form method="GET" class="grid gap-4 rounded-3xl border border-zinc-200/60 bg-white/60 p-6 shadow-sm backdrop-blur-xl md:grid-cols-4 dark:border-zinc-700/60 dark:bg-zinc-900/60">
            <flux:input name="q" :value="$q" placeholder="Search name, email, phone..." icon="magnifying-glass" />
            <flux:select name="company">
                <option value="">All Companies</option>
                @foreach ($companies as $companyOption)
                    <option value="{{ $companyOption }}" @selected($company === $companyOption)>{{ $companyOption }}</option>
                @endforeach
            </flux:select>
            <flux:select name="contact">
                <option value="">All Contacts</option>
                <option value="with" @selected($contact === 'with')>With Contact</option>
                <option value="without" @selected($contact === 'without')>Without Contact</option>
            </flux:select>
            <div class="flex items-center gap-2">
                <flux:button type="submit" variant="filled" icon="funnel" class="rounded-full px-4">Filter</flux:button>
                @if ($q !== '' || $company !== '' || $contact !== '' || $perPage !== 15)
                    <flux:button variant="ghost" :href="route('clients.index')" wire:navigate class="rounded-full">Clear</flux:button>
                @endif
            </div>
        </form>

        <div class="overflow-hidden rounded-3xl border border-zinc-200/60 bg-white/60 shadow-sm backdrop-blur-xl dark:border-zinc-700/60 dark:bg-zinc-900/60">
            <div class="overflow-x-auto p-2">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            <th class="px-4 py-3.5">Name</th>
                            <th class="px-4 py-3.5">Email</th>
                            <th class="px-4 py-3.5">Phone</th>
                            <th class="px-4 py-3.5">Company</th>
                            <th class="px-4 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-700/60">
                        @forelse ($clients as $client)
                            <tr class="transition-colors hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                                <td class="px-4 py-4 font-medium text-zinc-900 dark:text-white">{{ $client->name }}</td>
                                <td class="px-4 py-4 text-zinc-600 dark:text-zinc-300">{{ $client->email ?: '-' }}</td>
                                <td class="px-4 py-4 text-zinc-600 dark:text-zinc-300">{{ $client->phone ?: '-' }}</td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex h-6 items-center justify-center rounded-full bg-zinc-100 px-3 text-xs font-medium dark:bg-zinc-800">{{ $client->company ?: '-' }}</span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <flux:tooltip content="Edit">
                                            <flux:button size="sm" icon="pencil" variant="ghost" :href="route('clients.edit', $client)" wire:navigate class="size-8! rounded-full p-0! hover:bg-zinc-100 dark:hover:bg-zinc-800" />
                                        </flux:tooltip>
                                        <flux:tooltip content="Delete">
                                            <flux:modal.trigger :name="'delete-client-'.$client->id">
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
                                <td class="px-4 py-16 text-center text-zinc-500" colspan="5">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                                            <flux:icon.building-office-2 class="size-6 opacity-40" />
                                        </div>
                                        <p class="font-medium text-zinc-600 dark:text-zinc-400">No clients found.</p>
                                        <p class="text-sm">Add your first client to start assigning quotes.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-zinc-200/60 bg-white/40 px-6 py-4 text-sm text-zinc-500 dark:border-zinc-700/60 dark:bg-zinc-800/20">
                <p>
                    Showing <span class="font-medium text-zinc-900 dark:text-white">{{ $clients->firstItem() ?? 0 }}</span>-<span class="font-medium text-zinc-900 dark:text-white">{{ $clients->lastItem() ?? 0 }}</span> of <span class="font-medium text-zinc-900 dark:text-white">{{ $clients->total() }}</span> clients
                </p>
                <div class="flex flex-wrap items-center gap-3">
                    <form method="GET" action="{{ route('clients.index') }}" class="flex items-center gap-2">
                        @if ($q !== '')
                            <input type="hidden" name="q" value="{{ $q }}">
                        @endif
                        @if ($company !== '')
                            <input type="hidden" name="company" value="{{ $company }}">
                        @endif
                        @if ($contact !== '')
                            <input type="hidden" name="contact" value="{{ $contact }}">
                        @endif
                        <flux:select name="per_page" size="sm" onchange="this.form.submit()" class="rounded-full">
                            @foreach ([10, 15, 25, 50] as $size)
                                <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }} / page</option>
                            @endforeach
                        </flux:select>
                    </form>
                    <span>Page {{ $clients->currentPage() }} of {{ $clients->lastPage() }}</span>
                    {{ $clients->withQueryString()->links() }}
                </div>
            </div>
        </div>

        @foreach ($clients as $client)
            <flux:modal :name="'delete-client-'.$client->id" class="max-w-md">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Delete client?</flux:heading>
                        <flux:subheading>
                            This action cannot be undone. Client <span class="font-semibold">{{ $client->name }}</span> will be permanently deleted.
                        </flux:subheading>
                    </div>

                    <div class="flex justify-end gap-2">
                        <flux:modal.close>
                            <flux:button variant="filled" class="rounded-full">Cancel</flux:button>
                        </flux:modal.close>

                        <form method="POST" action="{{ route('clients.destroy', $client) }}">
                            @csrf
                            @method('DELETE')
                            <flux:button variant="danger" type="submit" class="rounded-full">Delete</flux:button>
                        </form>
                    </div>
                </div>
            </flux:modal>
        @endforeach
    </div>
</x-layouts::app>
