<x-layouts::app title="{{ __('Company & template settings') }}">
    <div class="space-y-6">

        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <flux:heading size="lg">{{ __('Company & template settings') }}</flux:heading>
                <flux:text class="text-zinc-500">
                    {{ __('Application name and logo appear in the shell; other values appear on exported PDF proposals (footer, sign-off, Annex II rates).') }}
                </flux:text>
            </div>
        </div>

        @if (session('status'))
            <flux:callout icon="check-circle" color="green">{{ session('status') }}</flux:callout>
        @endif

        @php
            $appNameSetting = optional($settings->flatten()->firstWhere('key', 'app_name'));
            $appLogoSetting = optional($settings->flatten()->firstWhere('key', 'app_logo_path'));
        @endphp

        <form method="POST" action="{{ route('settings.company.update') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            {{-- Shell: app name & logo (previously separate “Branding” page) --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <h3 class="mb-4 text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                    {{ __('Application') }}
                </h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <flux:input
                        name="settings[app_name]"
                        label="{{ __('Application name') }}"
                        placeholder="OMS Sales"
                        :value="old('settings.app_name', $appNameSetting?->value ?? 'OMS Sales')"
                        required
                    />
                    <div class="space-y-2">
                        <flux:label>{{ __('Application logo') }}</flux:label>
                        <button
                            type="button"
                            id="app-logo-picker-trigger-company"
                            class="flex w-full items-center gap-3 rounded-lg border border-zinc-200 p-3 text-left focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent dark:border-zinc-600"
                        >
                            <div class="flex size-14 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
                                <img src="{{ asset('storage/'.($appLogoSetting->value ?? 'overseas-marine logo.png')) }}" alt="{{ __('Application logo preview') }}" class="size-12 object-contain" />
                            </div>
                            <div class="min-w-0 text-xs text-zinc-500">
                                <p class="font-medium text-zinc-700 dark:text-zinc-300">{{ __('Current logo — click to change') }}</p>
                                <p class="truncate">{{ $appLogoSetting?->value ?? 'overseas-marine logo.png' }}</p>
                            </div>
                        </button>
                        <input
                            id="app-logo-input-company"
                            type="file"
                            name="app_logo"
                            accept="image/png,image/jpeg,image/webp,image/svg+xml"
                            class="hidden"
                        />
                        @error('app_logo')
                            <flux:text class="text-red-500">{{ $message }}</flux:text>
                        @enderror
                        <flux:text id="app-logo-picker-name-company" class="text-zinc-500">{{ __('No new file chosen') }}</flux:text>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($settings as $group => $groupSettings)
                    @php
                        $filteredSettings = $groupSettings->reject(fn ($setting) => in_array($setting->key, ['app_name', 'app_logo_path'], true));
                    @endphp
                    @continue($filteredSettings->isEmpty())
                    <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                        <h3 class="mb-4 text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                            {{ ucfirst($group) }}
                        </h3>
                        <div class="space-y-4">
                            @foreach ($filteredSettings as $setting)
                                <flux:input
                                    name="settings[{{ $setting->key }}]"
                                    label="{{ $setting->label }}"
                                    :value="old('settings.'.$setting->key, $setting->value)"
                                />
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end">
                <flux:button variant="primary" type="submit" icon="check">{{ __('Save settings') }}</flux:button>
            </div>
        </form>

        <script>
            (() => {
                const trigger = document.getElementById('app-logo-picker-trigger-company');
                const input = document.getElementById('app-logo-input-company');
                const nameSlot = document.getElementById('app-logo-picker-name-company');
                if (!trigger || !input || !nameSlot) {
                    return;
                }
                trigger.addEventListener('click', () => input.click());
                input.addEventListener('change', () => {
                    const file = input.files?.[0];
                    nameSlot.textContent = file ? file.name : @json(__('No new file chosen'));
                });
            })();
        </script>
    </div>
</x-layouts::app>
