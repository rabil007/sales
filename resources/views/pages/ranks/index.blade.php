<x-layouts::app :title="__('Ranks')">
    <div class="space-y-4">
        @if (session('status'))
            <flux:callout icon="check-circle" color="emerald">{{ session('status') }}</flux:callout>
        @endif

        <div class="flex items-end justify-between gap-3">
            <div>
                <flux:heading size="lg">Ranks</flux:heading>
                <flux:text class="text-zinc-500">Manage rank master data for quote crew lines.</flux:text>
            </div>
            <flux:button variant="primary" :href="route('ranks.create')" wire:navigate>New Rank</flux:button>
        </div>

        <div class="overflow-x-auto rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <table class="min-w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr class="text-left text-xs uppercase tracking-wide text-zinc-500">
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="px-4 py-3">Basis</th>
                        <th class="px-4 py-3">Default Rate</th>
                        <th class="px-4 py-3">Active</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ranks as $rank)
                        <tr class="border-t border-zinc-200 dark:border-zinc-700">
                            <td class="px-4 py-3 font-medium">{{ $rank->name }}</td>
                            <td class="px-4 py-3">{{ $rank->category }}</td>
                            <td class="px-4 py-3">{{ $rank->default_basis }}</td>
                            <td class="px-4 py-3">{{ number_format((float) $rank->default_rate, 2) }}</td>
                            <td class="px-4 py-3">{{ $rank->is_active ? 'Yes' : 'No' }}</td>
                            <td class="px-4 py-3">
                                <flux:button size="xs" variant="ghost" :href="route('ranks.edit', $rank)" wire:navigate>Edit</flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-6 text-center text-zinc-500" colspan="6">No ranks found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $ranks->links() }}
    </div>
</x-layouts::app>
