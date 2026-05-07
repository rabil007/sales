<x-layouts::app title="Template Settings">
    <div class="space-y-6">

        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <flux:heading size="lg">Template Settings</flux:heading>
                <flux:text class="text-zinc-500">
                    These values appear on all exported PDF proposals (footer, sign-off, etc.).
                </flux:text>
            </div>
        </div>

        @if (session('status'))
            <flux:callout icon="check-circle" color="green">{{ session('status') }}</flux:callout>
        @endif

        <form method="POST" action="{{ route('settings.company.update') }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($settings as $group => $groupSettings)
                    <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                        <h3 class="mb-4 text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                            {{ ucfirst($group) }}
                        </h3>
                        <div class="space-y-4">
                            @foreach ($groupSettings as $setting)
                                <flux:input
                                    name="settings[{{ $setting->key }}]"
                                    label="{{ $setting->label }}"
                                    :value="old('settings.' . $setting->key, $setting->value)"
                                />
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 flex justify-end">
                <flux:button variant="primary" type="submit" icon="check">Save Settings</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>
