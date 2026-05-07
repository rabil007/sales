<?php

use App\Models\CompanySetting;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Branding settings')] class extends Component {
    use WithFileUploads;

    public string $appName = '';

    public string $appLogoPath = 'overseas-marine logo.png';

    public $appLogo = null;

    public function mount(): void
    {
        $this->appName = CompanySetting::get('app_name', 'OMS Sales');
        $this->appLogoPath = CompanySetting::get('app_logo_path', 'overseas-marine logo.png');
    }

    public function updateApplicationBranding(): void
    {
        $this->validate([
            'appName' => ['required', 'string', 'max:255'],
            'appLogo' => ['nullable', 'image', 'max:2048'],
        ]);

        $logoPath = $this->appLogoPath;
        if ($this->appLogo) {
            $logoPath = $this->appLogo->store('app-branding', 'public');

            if ($this->appLogoPath !== '' && $this->appLogoPath !== 'overseas-marine logo.png' && Storage::disk('public')->exists($this->appLogoPath)) {
                Storage::disk('public')->delete($this->appLogoPath);
            }
        }

        CompanySetting::query()->updateOrCreate(
            ['key' => 'app_name'],
            ['label' => 'Application Name', 'group' => 'application', 'value' => $this->appName],
        );

        CompanySetting::query()->updateOrCreate(
            ['key' => 'app_logo_path'],
            ['label' => 'Application Logo Path', 'group' => 'application', 'value' => $logoPath],
        );

        $this->appLogoPath = $logoPath;
        $this->reset('appLogo');

        Flux::toast(variant: 'success', text: __('Application branding updated.'));
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Branding settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Branding')" :subheading="__('Update application name and logo shown across the app')">
        <form wire:submit="updateApplicationBranding" class="my-6 w-full space-y-6">
            <button type="button" id="app-logo-picker-trigger" class="flex items-center gap-3 rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent">
                <div class="flex size-14 items-center justify-center overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
                    <img src="{{ asset('storage/'.$appLogoPath) }}" alt="Application logo preview" class="size-12 object-contain">
                </div>
                <div class="text-left text-xs text-zinc-500">
                    <p>{{ __('Current logo (click to change)') }}</p>
                    <p class="truncate">{{ $appLogoPath }}</p>
                </div>
            </button>

            <flux:input wire:model="appName" :label="__('Application Name')" type="text" required />
            <div class="space-y-2">
                <flux:label>{{ __('Upload App Logo') }}</flux:label>
                <input
                    id="app-logo-input"
                    wire:model="appLogo"
                    type="file"
                    accept="image/png,image/jpeg,image/webp,image/svg+xml"
                    class="hidden"
                >
                <flux:text id="app-logo-picker-name">{{ __('No file chosen') }}</flux:text>
                @error('appLogo')
                    <flux:text class="text-red-500">{{ $message }}</flux:text>
                @enderror
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">
                    {{ __('Save branding') }}
                </flux:button>
            </div>
        </form>

        <script>
            (() => {
                const input = document.getElementById('app-logo-input');
                const trigger = document.getElementById('app-logo-picker-trigger');
                const name = document.getElementById('app-logo-picker-name');

                if (!input || !trigger || !name) {
                    return;
                }

                trigger.addEventListener('click', () => input.click());
                input.addEventListener('change', () => {
                    name.textContent = input.files?.[0]?.name ?? 'No file chosen';
                });
            })();
        </script>
    </x-pages::settings.layout>
</section>
