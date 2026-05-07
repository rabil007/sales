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

        <form method="POST" action="{{ route('settings.company.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @php
                $appLogoPath = $settings->flatten()->firstWhere('key', 'app_logo_path')?->value ?: 'overseas-marine logo.png';
            @endphp

            <div class="mb-4 rounded-xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <h3 class="mb-4 text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                    Application Branding
                </h3>
                <div class="grid gap-4 md:grid-cols-[auto,1fr] md:items-end">
                    <div class="flex items-center gap-3">
                        <div class="flex size-16 items-center justify-center overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
                            <img src="{{ asset('storage/'.$appLogoPath) }}" alt="Application logo preview" class="size-14 object-contain">
                        </div>
                        <div class="text-xs text-zinc-500">
                            <p>Current logo</p>
                            <p class="truncate">{{ $appLogoPath }}</p>
                        </div>
                    </div>
                    <flux:input type="file" name="app_logo" label="Upload App Logo" accept="image/png,image/jpeg,image/webp,image/svg+xml" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($settings as $group => $groupSettings)
                    <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                        <h3 class="mb-4 text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                            {{ ucfirst($group) }}
                        </h3>
                        <div class="space-y-4">
                            @foreach ($groupSettings as $setting)
                                @continue($setting->key === 'app_logo_path')
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
