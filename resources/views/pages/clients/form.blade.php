<x-layouts::app :title="$isEdit ? __('Edit Client') : __('New Client')">
    <div class="mx-auto max-w-4xl space-y-6">
        @if ($errors->any())
            <flux:callout icon="exclamation-triangle" color="red">Please review client details and try again.</flux:callout>
        @endif

        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <flux:heading size="lg">{{ $isEdit ? 'Edit Client' : 'Create Client' }}</flux:heading>
                <flux:text class="text-zinc-500">
                    {{ $isEdit ? 'Update client details used in quotes and agreements.' : 'Create a new client profile for quote assignment.' }}
                </flux:text>
            </div>
            <flux:button variant="ghost" icon="arrow-left" :href="route('clients.index')" wire:navigate>Back to list</flux:button>
        </div>

        <form method="POST" action="{{ $isEdit ? route('clients.update', $client) : route('clients.store') }}" class="space-y-6 rounded-xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 text-sm text-zinc-600 dark:border-zinc-700 dark:bg-zinc-800/50 dark:text-zinc-300">
                Client details here are reusable across quotes and help auto-fill agreement information.
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input name="name" label="Client Name" :value="old('name', $client->name)" required />
                <flux:input name="company" label="Company" :value="old('company', $client->company)" />
                <flux:input name="email" type="email" label="Email" :value="old('email', $client->email)" />
                <flux:input name="phone" label="Phone" :value="old('phone', $client->phone)" />
            </div>

            <flux:separator />

            <div class="flex justify-end">
                <flux:button variant="primary" type="submit" icon="check">{{ $isEdit ? 'Update Client' : 'Save Client' }}</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>
