<x-layouts::app :title="$isEdit ? __('Edit Rank') : __('New Rank')">
    <div class="mx-auto max-w-3xl space-y-4">
        @if (session('status'))
            <flux:callout icon="check-circle" color="emerald">{{ session('status') }}</flux:callout>
        @endif

        @if ($errors->any())
            <flux:callout icon="exclamation-triangle" color="red">Please review rank details and try again.</flux:callout>
        @endif

        <div class="flex items-center justify-between">
            <flux:heading size="lg">{{ $isEdit ? 'Edit Rank' : 'Create Rank' }}</flux:heading>
            <flux:button variant="ghost" icon="arrow-left" :href="route('ranks.index')" wire:navigate>Back to list</flux:button>
        </div>

        <form method="POST" action="{{ $isEdit ? route('ranks.update', $rank) : route('ranks.store') }}" class="space-y-4 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

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

            <div class="flex items-center justify-between">
                @if ($isEdit)
                    <button form="delete-rank-form" class="inline-flex items-center rounded-md px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-950">Delete</button>
                @else
                    <div></div>
                @endif
                <flux:button variant="primary" type="submit">{{ $isEdit ? 'Update Rank' : 'Save Rank' }}</flux:button>
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
