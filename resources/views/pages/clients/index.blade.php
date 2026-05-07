<x-layouts::app :title="__('Clients')">
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <flux:heading size="lg">Clients</flux:heading>
                <flux:text class="text-zinc-500">Manage client master data for quote selection.</flux:text>
            </div>
            <flux:button variant="primary" icon="plus" :href="route('clients.create')" wire:navigate>New Client</flux:button>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500">Total Clients</flux:text>
                <flux:heading size="lg" class="mt-2">{{ number_format($stats['total']) }}</flux:heading>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500">With Email</flux:text>
                <flux:heading size="lg" class="mt-2">{{ number_format($stats['withEmail']) }}</flux:heading>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500">With Phone</flux:text>
                <flux:heading size="lg" class="mt-2">{{ number_format($stats['withPhone']) }}</flux:heading>
            </div>
        </div>

        <form method="GET" class="grid gap-3 rounded-xl border border-zinc-200 bg-white p-4 md:grid-cols-4 dark:border-zinc-700 dark:bg-zinc-900">
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
                <flux:button type="submit" variant="filled" icon="funnel">Filter</flux:button>
                @if ($q !== '' || $company !== '' || $contact !== '' || $perPage !== 15)
                    <flux:button variant="ghost" :href="route('clients.index')" wire:navigate>Clear</flux:button>
                @endif
            </div>
        </form>

        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/80">
                        <tr class="text-left text-xs uppercase tracking-wide text-zinc-500">
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Phone</th>
                            <th class="px-4 py-3">Company</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clients as $client)
                            <tr class="border-t border-zinc-200 transition-colors hover:bg-zinc-50/80 dark:border-zinc-700 dark:hover:bg-zinc-800/40">
                                <td class="px-4 py-3 font-medium">{{ $client->name }}</td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $client->email ?: '-' }}</td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $client->phone ?: '-' }}</td>
                                <td class="px-4 py-3">{{ $client->company ?: '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <flux:tooltip content="Edit">
                                            <flux:button size="sm" icon="pencil" variant="ghost" :href="route('clients.edit', $client)" wire:navigate class="size-8! p-0!" />
                                        </flux:tooltip>
                                        <flux:tooltip content="Delete">
                                            <flux:modal.trigger :name="'delete-client-'.$client->id">
                                                <flux:button
                                                    size="sm"
                                                    icon="trash"
                                                    variant="ghost"
                                                    class="size-8! p-0! text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/40"
                                                />
                                            </flux:modal.trigger>
                                        </flux:tooltip>
                                    </div>

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
                                                    <flux:button variant="filled">Cancel</flux:button>
                                                </flux:modal.close>

                                                <form method="POST" action="{{ route('clients.destroy', $client) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <flux:button variant="danger" type="submit">Delete</flux:button>
                                                </form>
                                            </div>
                                        </div>
                                    </flux:modal>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-12 text-center text-zinc-500" colspan="5">
                                    <div class="flex flex-col items-center gap-2">
                                        <flux:icon.building-office-2 class="size-8 opacity-30" />
                                        <p class="font-medium text-zinc-600 dark:text-zinc-400">No clients found.</p>
                                        <p class="text-xs text-zinc-400">Add your first client to start assigning quotes.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-zinc-200 bg-white p-4 text-sm text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900">
            <p>
                Showing {{ $clients->firstItem() ?? 0 }}-{{ $clients->lastItem() ?? 0 }} of {{ $clients->total() }} clients
            </p>
            <div class="flex flex-wrap items-center gap-3">
                <form method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="q" value="{{ $q }}">
                    <input type="hidden" name="company" value="{{ $company }}">
                    <input type="hidden" name="contact" value="{{ $contact }}">
                    <flux:select name="per_page" size="sm" onchange="this.form.submit()">
                        @foreach ([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }} / page</option>
                        @endforeach
                    </flux:select>
                </form>
                <span>Page {{ $clients->currentPage() }} of {{ $clients->lastPage() }}</span>
                {{ $clients->links() }}
            </div>
        </div>
    </div>
</x-layouts::app>
