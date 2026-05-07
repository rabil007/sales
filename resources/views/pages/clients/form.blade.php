<x-layouts::app :title="$isEdit ? __('Edit Client') : __('New Client')">
    <div class="mx-auto max-w-3xl space-y-4">
        @if (session('status'))
            <flux:callout icon="check-circle" color="emerald">{{ session('status') }}</flux:callout>
        @endif

        @if ($errors->any())
            <flux:callout icon="exclamation-triangle" color="red">Please review client details and try again.</flux:callout>
        @endif

        <div class="flex items-center justify-between">
            <flux:heading size="lg">{{ $isEdit ? 'Edit Client' : 'Create Client' }}</flux:heading>
            <flux:button variant="ghost" icon="arrow-left" :href="route('clients.index')" wire:navigate>Back to list</flux:button>
        </div>

        <form method="POST" action="{{ $isEdit ? route('clients.update', $client) : route('clients.store') }}" class="space-y-4 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input name="name" label="Client Name" :value="old('name', $client->name)" required />
                <flux:input name="company" label="Company" :value="old('company', $client->company)" />
                <flux:input name="email" type="email" label="Email" :value="old('email', $client->email)" />
                <flux:input name="phone" label="Phone" :value="old('phone', $client->phone)" />
            </div>

            <div class="flex items-center justify-between">
                @if ($isEdit)
                    <button form="delete-client-form" class="inline-flex items-center rounded-md px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-950">Delete</button>
                @else
                    <div></div>
                @endif
                <flux:button variant="primary" type="submit">{{ $isEdit ? 'Update Client' : 'Save Client' }}</flux:button>
            </div>
        </form>

        @if ($isEdit)
            <form id="delete-client-form" method="POST" action="{{ route('clients.destroy', $client) }}">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
</x-layouts::app>
