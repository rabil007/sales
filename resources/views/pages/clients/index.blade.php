<x-layouts::app :title="__('Clients')">
    <div class="space-y-4">
        @if (session('status'))
            <flux:callout icon="check-circle" color="emerald">{{ session('status') }}</flux:callout>
        @endif

        <div class="flex items-end justify-between gap-3">
            <div>
                <flux:heading size="lg">Clients</flux:heading>
                <flux:text class="text-zinc-500">Manage client master data for quote selection.</flux:text>
            </div>
            <flux:button variant="primary" :href="route('clients.create')" wire:navigate>New Client</flux:button>
        </div>

        <div class="overflow-x-auto rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <table class="min-w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr class="text-left text-xs uppercase tracking-wide text-zinc-500">
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Phone</th>
                        <th class="px-4 py-3">Company</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clients as $client)
                        <tr class="border-t border-zinc-200 dark:border-zinc-700">
                            <td class="px-4 py-3 font-medium">{{ $client->name }}</td>
                            <td class="px-4 py-3">{{ $client->email ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $client->phone ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $client->company ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <flux:button size="xs" variant="ghost" :href="route('clients.edit', $client)" wire:navigate>Edit</flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-6 text-center text-zinc-500" colspan="5">No clients found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $clients->links() }}
    </div>
</x-layouts::app>
