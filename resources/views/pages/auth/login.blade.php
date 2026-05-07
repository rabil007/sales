<x-layouts::auth :title="__('Log in')" :show-top-logo="false">
    @php
        $appName = \App\Models\CompanySetting::get('app_name', 'OMS Sales');
    @endphp

    <div class="w-full max-w-lg">
        <div class="rounded-2xl border border-zinc-200/70 bg-white/95 p-8 shadow-2xl backdrop-blur-sm dark:border-zinc-700 dark:bg-zinc-900/90">
            <div class="mb-6 flex flex-col items-center text-center">
                <img src="{{ asset('storage/'.\App\Models\CompanySetting::get('app_logo_path', 'overseas-marine logo.png')) }}" alt="{{ $appName }} logo" class="mb-3 size-12 object-contain">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">{{ $appName }}</p>
            </div>

            <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

            <!-- Session Status -->
            <x-auth-session-status class="mt-6 text-center" :status="session('status')" />

            <form method="POST" action="{{ route('login.store') }}" class="mt-6 flex flex-col gap-6">
                @csrf

                <!-- Email Address -->
                <flux:input
                    name="email"
                    :label="__('Email address')"
                    :value="old('email')"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="email@example.com"
                />

                <!-- Password -->
                <div class="relative">
                    <flux:input
                        name="password"
                        :label="__('Password')"
                        type="password"
                        required
                        autocomplete="current-password"
                        :placeholder="__('Password')"
                        viewable
                    />

                    @if (Route::has('password.request'))
                        <flux:link class="absolute top-0 text-sm inset-e-0" :href="route('password.request')" wire:navigate>
                            {{ __('Forgot your password?') }}
                        </flux:link>
                    @endif
                </div>

                <!-- Remember Me -->
                <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                        {{ __('Log in') }}
                    </flux:button>
                </div>
            </form>
        </div>

        <p class="mt-4 text-center text-xs text-zinc-500">Secure access for {{ $appName }} operations portal.</p>
    </div>
</x-layouts::auth>
