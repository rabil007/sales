<x-layouts::app :title="$isEdit ? __('Edit Client') : __('New Client')">
    <div class="mx-auto max-w-4xl space-y-8">
        @if ($errors->any())
            <flux:callout icon="exclamation-triangle" color="red" class="rounded-2xl">Please review client details and try again.</flux:callout>
        @endif

        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight">{{ $isEdit ? 'Edit Client' : 'Create Client' }}</flux:heading>
                <flux:text class="mt-1 text-zinc-500 dark:text-zinc-400">
                    {{ $isEdit ? 'Update client details used in quotes and agreements.' : 'Create a new client profile for quote assignment.' }}
                </flux:text>
            </div>
            <flux:button variant="ghost" icon="arrow-left" :href="route('clients.index')" wire:navigate class="rounded-full transition-transform hover:-translate-x-0.5">Back to list</flux:button>
        </div>

        <form method="POST" action="{{ $isEdit ? route('clients.update', $client) : route('clients.store') }}" class="space-y-8 rounded-3xl border border-zinc-200/60 bg-white/60 p-8 shadow-sm backdrop-blur-xl dark:border-zinc-700/60 dark:bg-zinc-900/60">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="rounded-2xl border border-blue-200/60 bg-blue-50/50 p-5 text-sm text-blue-800 backdrop-blur-sm dark:border-blue-900/40 dark:bg-blue-900/20 dark:text-blue-300">
                Client details here are reusable across quotes and help auto-fill agreement information.
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <flux:input name="name" label="Client Name" :value="old('name', $client->name)" required />
                <flux:input name="company" label="Company" :value="old('company', $client->company)" />
                <flux:input name="email" type="email" label="Email" :value="old('email', $client->email)" />
                <flux:input name="phone" label="Phone" :value="old('phone', $client->phone)" />
            </div>

            <flux:separator class="border-zinc-200/60 dark:border-zinc-700/60" />

            <div>
                <h3 class="mb-4 text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">Contact &amp; Address</h3>
                <div class="grid gap-6 md:grid-cols-2">
                    <flux:input name="contact_person" label="Contact Person" placeholder="e.g. Mr. Vimal Kumar" :value="old('contact_person', $client->contact_person)" />
                    <flux:input name="contact_designation" label="Designation / Role" placeholder="e.g. Crewing Supervisor" :value="old('contact_designation', $client->contact_designation)" />
                    <flux:input name="address" label="Address" placeholder="e.g. Office # 304, 3rd Floor, Al Salmeen Golden Tower" :value="old('address', $client->address)" class="md:col-span-2" />
                    <flux:input name="city" label="City / Country" placeholder="e.g. Abu Dhabi, UAE" :value="old('city', $client->city)" class="md:col-span-2" />
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <flux:button variant="ghost" :href="route('clients.index')" wire:navigate class="rounded-full px-5">Cancel</flux:button>
                <flux:button variant="primary" type="submit" icon="check" class="rounded-full px-6 transition-transform hover:-translate-y-0.5 hover:shadow-md">
                    {{ $isEdit ? 'Update Client' : 'Save Client' }}
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>
