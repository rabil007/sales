<x-layouts::app :title="__('Ranks')">
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <flux:heading size="lg">Ranks</flux:heading>
                <flux:text class="text-zinc-500">Manage rank master data for quote crew lines.</flux:text>
            </div>
            <flux:button variant="primary" icon="plus" :href="route('ranks.create')" wire:navigate>New Rank</flux:button>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500">Total Ranks</flux:text>
                <flux:heading size="lg" class="mt-2">{{ number_format($stats['total']) }}</flux:heading>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500">Active</flux:text>
                <flux:heading size="lg" class="mt-2">{{ number_format($stats['active']) }}</flux:heading>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500">Inactive</flux:text>
                <flux:heading size="lg" class="mt-2">{{ number_format($stats['inactive']) }}</flux:heading>
            </div>
        </div>

        <form method="GET" class="grid gap-3 rounded-xl border border-zinc-200 bg-white p-4 md:grid-cols-5 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:input name="q" :value="$q" placeholder="Search rank name..." icon="magnifying-glass" />
            <flux:select name="category">
                <option value="">All Categories</option>
                @foreach ($categories as $categoryOption)
                    <option value="{{ $categoryOption }}" @selected($category === $categoryOption)>{{ $categoryOption }}</option>
                @endforeach
            </flux:select>
            <flux:select name="basis">
                <option value="">All Basis</option>
                @foreach ($bases as $basisOption)
                    <option value="{{ $basisOption }}" @selected($basis === $basisOption)>{{ $basisOption }}</option>
                @endforeach
            </flux:select>
            <flux:select name="status">
                <option value="">All Status</option>
                <option value="active" @selected($status === 'active')>Active</option>
                <option value="inactive" @selected($status === 'inactive')>Inactive</option>
            </flux:select>
            <div class="flex items-center gap-2">
                <flux:button type="submit" variant="filled" icon="funnel">Filter</flux:button>
                @if ($q !== '' || $category !== '' || $basis !== '' || $status !== '' || $perPage !== 15)
                    <flux:button variant="ghost" :href="route('ranks.index')" wire:navigate>Clear</flux:button>
                @endif
            </div>
        </form>

        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/80">
                        <tr class="text-left text-xs uppercase tracking-wide text-zinc-500">
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Basis</th>
                            <th class="px-4 py-3">Default Rate</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ranks as $rank)
                            <tr class="border-t border-zinc-200 transition-colors hover:bg-zinc-50/80 dark:border-zinc-700 dark:hover:bg-zinc-800/40">
                                <td class="px-4 py-3 font-medium">{{ $rank->name }}</td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $rank->category }}</td>
                                <td class="px-4 py-3">{{ $rank->default_basis }}</td>
                                <td class="px-4 py-3 font-medium tabular-nums">{{ number_format((float) $rank->default_rate, 2) }}</td>
                                <td class="px-4 py-3">
                                    <form
                                        method="POST"
                                        action="{{ route('ranks.toggle-status', $rank) }}"
                                        class="inline-flex items-center gap-2"
                                        x-data="{ submitting: false }"
                                        x-on:submit="submitting = true"
                                    >
                                        @csrf
                                        @method('PATCH')
                                        <flux:switch
                                            :checked="$rank->is_active"
                                            x-bind:disabled="submitting"
                                            x-on:click.prevent="if (submitting) return; submitting = true; $el.closest('form').submit()"
                                        />
                                        <span x-show="submitting" class="text-xs text-zinc-500">Updating...</span>
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <flux:tooltip content="Edit">
                                            <flux:button size="sm" icon="pencil" variant="ghost" :href="route('ranks.edit', $rank)" wire:navigate class="size-8! p-0!" />
                                        </flux:tooltip>
                                        <flux:tooltip content="Delete">
                                            <flux:modal.trigger :name="'delete-rank-'.$rank->id">
                                                <flux:button
                                                    size="sm"
                                                    icon="trash"
                                                    variant="ghost"
                                                    class="size-8! p-0! text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/40"
                                                />
                                            </flux:modal.trigger>
                                        </flux:tooltip>
                                    </div>

                                    <flux:modal :name="'delete-rank-'.$rank->id" class="max-w-md">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">Delete rank?</flux:heading>
                                                <flux:subheading>
                                                    This action cannot be undone. Rank <span class="font-semibold">{{ $rank->name }}</span> will be permanently deleted.
                                                </flux:subheading>
                                            </div>

                                            <div class="flex justify-end gap-2">
                                                <flux:modal.close>
                                                    <flux:button variant="filled">Cancel</flux:button>
                                                </flux:modal.close>

                                                <form method="POST" action="{{ route('ranks.destroy', $rank) }}">
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
                                <td class="px-4 py-12 text-center text-zinc-500" colspan="6">
                                    <div class="flex flex-col items-center gap-2">
                                        <flux:icon.users class="size-8 opacity-30" />
                                        <p class="font-medium text-zinc-600 dark:text-zinc-400">No ranks found.</p>
                                        <p class="text-xs text-zinc-400">Add your first rank to start building quote crew lines.</p>
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
                Showing {{ $ranks->firstItem() ?? 0 }}-{{ $ranks->lastItem() ?? 0 }} of {{ $ranks->total() }} ranks
            </p>
            <div class="flex flex-wrap items-center gap-3">
                <form method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="q" value="{{ $q }}">
                    <input type="hidden" name="category" value="{{ $category }}">
                    <input type="hidden" name="basis" value="{{ $basis }}">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <flux:select name="per_page" size="sm" onchange="this.form.submit()">
                        @foreach ([10, 15, 25, 50] as $size)
                            <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }} / page</option>
                        @endforeach
                    </flux:select>
                </form>
                <span>Page {{ $ranks->currentPage() }} of {{ $ranks->lastPage() }}</span>
                {{ $ranks->links() }}
            </div>
        </div>
    </div>
</x-layouts::app>
