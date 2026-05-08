<x-layouts::app title="{{ __('Company & template settings') }}">
    <div class="space-y-8">

        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight">{{ __('Company & template settings') }}</flux:heading>
                <flux:text class="mt-1 text-zinc-500 dark:text-zinc-400">
                    {{ __('Application name and logo appear in the shell; other values appear on exported PDF proposals.') }}
                </flux:text>
            </div>
        </div>

        @if (session('status'))
            <flux:callout icon="check-circle" color="green" class="rounded-2xl">{{ session('status') }}</flux:callout>
        @endif

        @php
            $appNameSetting = optional($settings->flatten()->firstWhere('key', 'app_name'));
            $appLogoSetting = optional($settings->flatten()->firstWhere('key', 'app_logo_path'));
        @endphp

        <form method="POST" action="{{ route('settings.company.update') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            {{-- Shell: app name & logo (previously separate “Branding” page) --}}
            <div class="relative overflow-hidden rounded-3xl border border-zinc-200/60 bg-white/60 p-8 shadow-sm backdrop-blur-xl dark:border-zinc-700/60 dark:bg-zinc-900/60">
                <div class="absolute -right-20 -top-20 z-0 h-64 w-64 rounded-full bg-blue-500/10 blur-3xl dark:bg-blue-600/5"></div>
                
                <div class="relative z-10">
                    <h3 class="mb-6 text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                        {{ __('Application Branding') }}
                    </h3>
                    <div class="grid gap-6 md:grid-cols-2">
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
                                class="group flex w-full items-center gap-4 rounded-2xl border border-zinc-200/60 bg-white/40 p-3 text-left transition-all hover:bg-white/80 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-zinc-600/50 dark:bg-zinc-800/40 dark:hover:bg-zinc-800/80"
                            >
                                <div class="flex size-14 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-zinc-200/50 bg-white shadow-sm transition-transform group-hover:scale-105 dark:border-zinc-700/50 dark:bg-zinc-800">
                                    <img src="{{ asset('storage/'.($appLogoSetting->value ?? 'overseas-marine logo.png')) }}" alt="{{ __('Application logo preview') }}" class="size-10 object-contain drop-shadow-sm" />
                                </div>
                                <div class="min-w-0 text-sm text-zinc-500">
                                    <p class="font-medium text-zinc-700 transition-colors group-hover:text-blue-600 dark:text-zinc-300 dark:group-hover:text-blue-400">{{ __('Change application logo') }}</p>
                                    <p class="truncate text-xs text-zinc-400">{{ $appLogoSetting?->value ?? 'overseas-marine logo.png' }}</p>
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
                            <flux:text id="app-logo-picker-name-company" class="text-xs text-zinc-400">{{ __('No new file chosen') }}</flux:text>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($settings as $group => $groupSettings)
                    @php
                        $filteredSettings = $groupSettings->reject(fn ($setting) => in_array($setting->key, ['app_name', 'app_logo_path'], true));
                    @endphp
                    @continue($filteredSettings->isEmpty())
                    <div class="rounded-3xl border border-zinc-200/60 bg-white/60 p-8 shadow-sm backdrop-blur-xl dark:border-zinc-700/60 dark:bg-zinc-900/60">
                        <h3 class="mb-6 text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                            {{ ucfirst($group) }}
                        </h3>
                        <div class="space-y-5">
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

            <div class="fixed bottom-8 right-8 z-50 md:bottom-10 md:right-10">
                <flux:button variant="primary" type="submit" icon="check" class="rounded-full px-8 shadow-[0_8px_30px_rgb(0,0,0,0.12)] transition-all hover:-translate-y-1 hover:shadow-[0_8px_30px_rgb(0,0,0,0.2)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.5)] dark:hover:shadow-[0_8px_30px_rgb(0,0,0,0.7)]">
                    {{ __('Save settings') }}
                </flux:button>
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
                    if (file) {
                        nameSlot.innerHTML = `<span class="text-blue-600 dark:text-blue-400 font-medium">Selected file:</span> ${file.name}`;
                    } else {
                        nameSlot.textContent = @json(__('No new file chosen'));
                    }
                });
            })();
        </script>
    </div>
</x-layouts::app>
