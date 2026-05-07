<x-layouts::app :title="$isEdit ? __('Edit Rank') : __('New Rank')">
    <div class="mx-auto max-w-4xl space-y-6">
        @if (session('status'))
            <flux:callout icon="check-circle" color="emerald">{{ session('status') }}</flux:callout>
        @endif

        @if ($errors->any())
            <flux:callout icon="exclamation-triangle" color="red">Please review rank details and try again.</flux:callout>
        @endif

        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <flux:heading size="lg">{{ $isEdit ? 'Edit Rank' : 'Create Rank' }}</flux:heading>
                <flux:text class="text-zinc-500">
                    {{ $isEdit ? 'Update default values used while adding crew lines.' : 'Create a reusable rank template for quote crew lines.' }}
                </flux:text>
            </div>
            <flux:button variant="ghost" icon="arrow-left" :href="route('ranks.index')" wire:navigate>Back to list</flux:button>
        </div>

        <form method="POST" action="{{ $isEdit ? route('ranks.update', $rank) : route('ranks.store') }}" class="space-y-6 rounded-xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 text-sm text-zinc-600 dark:border-zinc-700 dark:bg-zinc-800/50 dark:text-zinc-300">
                These defaults auto-fill on quote crew lines and can still be adjusted per line when preparing agreements.
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input name="name" label="Rank Name" :value="old('name', $rank->name)" required />
                <flux:input name="category" label="Category" :value="old('category', $rank->category)" required />
                <flux:select name="default_basis" label="Default Basis" required>
                    @foreach (['Day', 'Month', 'Fixed'] as $basisOption)
                        <option value="{{ $basisOption }}" @selected(old('default_basis', $rank->default_basis) === $basisOption)>{{ $basisOption }}</option>
                    @endforeach
                </flux:select>
                <flux:input name="default_rate" type="number" step="0.01" min="0" label="Default Rate" :value="old('default_rate', $rank->default_rate)" required />
            </div>

            <flux:field variant="inline">
                <flux:checkbox name="is_active" value="1" :checked="(bool) old('is_active', $rank->is_active)" />
                <flux:label>Active</flux:label>
            </flux:field>

            <flux:separator />

            <div class="flex items-center justify-between">
                @if ($isEdit)
                    <flux:button
                        variant="danger"
                        type="button"
                        icon="trash"
                        x-data
                        x-on:click="if (confirm('Delete this rank? This action cannot be undone.')) document.getElementById('delete-rank-form').submit()"
                    >
                        Delete
                    </flux:button>
                @else
                    <div></div>
                @endif
                <flux:button variant="primary" type="submit" icon="check">
                    {{ $isEdit ? 'Update Rank' : 'Save Rank' }}
                </flux:button>
            </div>
        </form>

        @if ($isEdit)
            <form id="delete-rank-form" method="POST" action="{{ route('ranks.destroy', $rank) }}">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
</x-layouts::app>
