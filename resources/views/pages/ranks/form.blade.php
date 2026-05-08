<x-layouts::app :title="$isEdit ? __('Edit Rank') : __('New Rank')">
    <div class="mx-auto max-w-4xl space-y-8">
        @if ($errors->any())
            <flux:callout icon="exclamation-triangle" color="red" class="rounded-2xl">Please review rank details and try again.</flux:callout>
        @endif

        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight">{{ $isEdit ? 'Edit Rank' : 'Create Rank' }}</flux:heading>
                <flux:text class="mt-1 text-zinc-500 dark:text-zinc-400">
                    {{ $isEdit ? 'Update default values used while adding crew lines.' : 'Create a reusable rank template for quote crew lines.' }}
                </flux:text>
            </div>
            <flux:button variant="ghost" icon="arrow-left" :href="route('ranks.index')" wire:navigate class="rounded-full transition-transform hover:-translate-x-0.5">Back to list</flux:button>
        </div>

        <form method="POST" action="{{ $isEdit ? route('ranks.update', $rank) : route('ranks.store') }}" class="space-y-8 rounded-3xl border border-zinc-200/60 bg-white/60 p-8 shadow-sm backdrop-blur-xl dark:border-zinc-700/60 dark:bg-zinc-900/60">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="rounded-2xl border border-blue-200/60 bg-blue-50/50 p-5 text-sm text-blue-800 dark:border-blue-900/40 dark:bg-blue-900/20 dark:text-blue-300 backdrop-blur-sm">
                These defaults auto-fill on quote crew lines and can still be adjusted per line when preparing agreements.
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <flux:input name="name" label="Rank Name" :value="old('name', $rank->name)" required />
                <flux:input name="category" label="Category" :value="old('category', $rank->category)" required />
                <flux:select name="default_basis" label="Default Basis" required>
                    @foreach (['Day', 'Month', 'Fixed'] as $basisOption)
                        <option value="{{ $basisOption }}" @selected(old('default_basis', $rank->default_basis) === $basisOption)>{{ $basisOption }}</option>
                    @endforeach
                </flux:select>
                <flux:input name="default_rate" type="number" step="0.01" min="0" label="Default Rate" :value="old('default_rate', $rank->default_rate)" required />
            </div>

            <flux:field variant="inline" class="mt-2">
                <flux:switch name="is_active" value="1" :checked="(bool) old('is_active', $rank->is_active)" />
                <flux:label class="font-medium">Active Status</flux:label>
            </flux:field>

            <flux:separator class="border-zinc-200/60 dark:border-zinc-700/60" />

            <div class="flex justify-end gap-3">
                <flux:button variant="ghost" :href="route('ranks.index')" wire:navigate class="rounded-full px-5">Cancel</flux:button>
                <flux:button variant="primary" type="submit" icon="check" class="rounded-full px-6 transition-transform hover:-translate-y-0.5 hover:shadow-md">
                    {{ $isEdit ? 'Update Rank' : 'Save Rank' }}
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>
