<x-layouts::app :title="__('Ranks')">
    <div class="space-y-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight">Ranks</flux:heading>
                <flux:text class="mt-1 text-zinc-500 dark:text-zinc-400">Manage rank master data for quote crew lines.</flux:text>
            </div>
            <flux:button variant="primary" icon="plus" :href="route('ranks.create')" wire:navigate class="rounded-full px-5 transition-transform hover:-translate-y-0.5 hover:shadow-md">New Rank</flux:button>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            <div class="group relative overflow-hidden rounded-3xl border border-zinc-200/60 bg-gradient-to-b from-white/80 to-white/40 p-6 shadow-sm backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700/60 dark:from-zinc-900/80 dark:to-zinc-900/40">
                <div class="absolute -right-8 -top-8 z-0 h-32 w-32 rounded-full bg-blue-500/10 blur-3xl transition-all duration-500 group-hover:scale-150 group-hover:bg-blue-500/20 dark:bg-blue-400/5"></div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total Ranks</p>
                    <p class="mt-2 tabular-nums text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
            <div class="group relative overflow-hidden rounded-3xl border border-zinc-200/60 bg-gradient-to-b from-white/80 to-white/40 p-6 shadow-sm backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700/60 dark:from-zinc-900/80 dark:to-zinc-900/40">
                <div class="absolute -right-8 -top-8 z-0 h-32 w-32 rounded-full bg-emerald-500/10 blur-3xl transition-all duration-500 group-hover:scale-150 group-hover:bg-emerald-500/20 dark:bg-emerald-400/5"></div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Active</p>
                    <p class="mt-2 tabular-nums text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ number_format($stats['active']) }}</p>
                </div>
            </div>
            <div class="group relative overflow-hidden rounded-3xl border border-zinc-200/60 bg-gradient-to-b from-white/80 to-white/40 p-6 shadow-sm backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700/60 dark:from-zinc-900/80 dark:to-zinc-900/40">
                <div class="absolute -right-8 -top-8 z-0 h-32 w-32 rounded-full bg-rose-500/10 blur-3xl transition-all duration-500 group-hover:scale-150 group-hover:bg-rose-500/20 dark:bg-rose-400/5"></div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Inactive</p>
                    <p class="mt-2 tabular-nums text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ number_format($stats['inactive']) }}</p>
                </div>
            </div>
        </div>

        <form method="GET" class="grid gap-4 rounded-3xl border border-zinc-200/60 bg-white/60 p-6 shadow-sm backdrop-blur-xl md:grid-cols-5 dark:border-zinc-700/60 dark:bg-zinc-900/60">
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
                <flux:button type="submit" variant="filled" icon="funnel" class="rounded-full px-4">Filter</flux:button>
                @if ($q !== '' || $category !== '' || $basis !== '' || $status !== '' || $perPage !== 15)
                    <flux:button variant="ghost" :href="route('ranks.index')" wire:navigate class="rounded-full">Clear</flux:button>
                @endif
            </div>
        </form>

        <div class="overflow-hidden rounded-3xl border border-zinc-200/60 bg-white/60 shadow-sm backdrop-blur-xl dark:border-zinc-700/60 dark:bg-zinc-900/60">
            <div class="overflow-x-auto p-2">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            <th class="px-4 py-3.5">Name</th>
                            <th class="px-4 py-3.5">Category</th>
                            <th class="px-4 py-3.5">Basis</th>
                            <th class="px-4 py-3.5">Default Rate</th>
                            <th class="px-4 py-3.5">Status</th>
                            <th class="px-4 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-700/60">
                        @forelse ($ranks as $rank)
                            <tr class="transition-colors hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                                <td class="px-4 py-4 font-medium text-zinc-900 dark:text-white">{{ $rank->name }}</td>
                                <td class="px-4 py-4 text-zinc-600 dark:text-zinc-300">
                                    <span class="inline-flex h-6 items-center justify-center rounded-full bg-zinc-100 px-3 text-xs font-medium dark:bg-zinc-800">{{ $rank->category }}</span>
                                </td>
                                <td class="px-4 py-4 text-zinc-600 dark:text-zinc-300">{{ $rank->default_basis }}</td>
                                <td class="px-4 py-4 font-medium tabular-nums text-zinc-900 dark:text-white">{{ number_format((float) $rank->default_rate, 2) }}</td>
                                <td class="px-4 py-4">
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
                                        <span x-show="submitting" class="text-xs font-medium text-zinc-500">Updating...</span>
                                    </form>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <flux:tooltip content="Edit">
                                            <flux:button size="sm" icon="pencil" variant="ghost" :href="route('ranks.edit', $rank)" wire:navigate class="size-8! rounded-full p-0! hover:bg-zinc-100 dark:hover:bg-zinc-800" />
                                        </flux:tooltip>
                                        <flux:tooltip content="Delete">
                                            <flux:modal.trigger :name="'delete-rank-'.$rank->id">
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
                                <td class="px-4 py-16 text-center text-zinc-500" colspan="6">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                                            <flux:icon.users class="size-6 opacity-40" />
                                        </div>
                                        <p class="font-medium text-zinc-600 dark:text-zinc-400">No ranks found.</p>
                                        <p class="text-sm">Add your first rank to start building quote crew lines.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-zinc-200/60 bg-white/40 px-6 py-4 text-sm text-zinc-500 dark:border-zinc-700/60 dark:bg-zinc-800/20">
                <p>
                    Showing <span class="font-medium text-zinc-900 dark:text-white">{{ $ranks->firstItem() ?? 0 }}</span>-<span class="font-medium text-zinc-900 dark:text-white">{{ $ranks->lastItem() ?? 0 }}</span> of <span class="font-medium text-zinc-900 dark:text-white">{{ $ranks->total() }}</span> ranks
                </p>
                <div class="flex flex-wrap items-center gap-3">
                    <form method="GET" action="{{ route('ranks.index') }}" class="flex items-center gap-2">
                        @if ($q !== '')
                            <input type="hidden" name="q" value="{{ $q }}">
                        @endif
                        @if ($category !== '')
                            <input type="hidden" name="category" value="{{ $category }}">
                        @endif
                        @if ($basis !== '')
                            <input type="hidden" name="basis" value="{{ $basis }}">
                        @endif
                        @if ($status !== '')
                            <input type="hidden" name="status" value="{{ $status }}">
                        @endif
                        <flux:select name="per_page" size="sm" onchange="this.form.submit()" class="rounded-full">
                            @foreach ([10, 15, 25, 50] as $size)
                                <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }} / page</option>
                            @endforeach
                        </flux:select>
                    </form>
                    <span>Page {{ $ranks->currentPage() }} of {{ $ranks->lastPage() }}</span>
                    {{ $ranks->withQueryString()->links() }}
                </div>
            </div>
        </div>

        @foreach ($ranks as $rank)
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
                            <flux:button variant="filled" class="rounded-full">Cancel</flux:button>
                        </flux:modal.close>

                        <form method="POST" action="{{ route('ranks.destroy', $rank) }}">
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
