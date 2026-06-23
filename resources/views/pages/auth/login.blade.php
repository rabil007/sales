<x-layouts::auth :title="__('Log in')" :show-top-logo="false">
    @php
        $appName = \App\Models\CompanySetting::get('app_name', 'OMS Sales');
    @endphp

    <div class="relative w-full">
        <!-- Ambient Background Glows -->
        <div class="pointer-events-none absolute -left-10 -top-10 z-0 h-48 w-48 rounded-full bg-blue-500/20 blur-3xl dark:bg-blue-600/10"></div>
        <div class="pointer-events-none absolute -bottom-10 -right-10 z-0 h-48 w-48 rounded-full bg-indigo-500/20 blur-3xl dark:bg-indigo-600/10"></div>

        <div class="relative z-10 overflow-hidden rounded-3xl border border-white/50 bg-white/60 p-8 shadow-2xl backdrop-blur-xl transition-all dark:border-zinc-700/50 dark:bg-zinc-900/60 dark:shadow-xl">
            <!-- Premium top border reflection -->
            <div class="absolute inset-x-0 -top-px h-px bg-gradient-to-r from-transparent via-white/80 to-transparent dark:via-white/20"></div>
            
            <div class="mb-8 flex flex-col items-center text-center">
                <div class="relative mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-sm ring-1 ring-zinc-200/50 transition-transform duration-500 hover:scale-105 dark:bg-zinc-800 dark:ring-zinc-700/50">
                    <img src="{{ \App\Models\CompanySetting::logoUrl() }}" alt="{{ $appName }} logo" class="size-10 object-contain drop-shadow-sm">
                </div>
                <p class="mb-2 bg-gradient-to-br from-zinc-700 to-zinc-900 bg-clip-text text-xs font-bold uppercase tracking-[0.2em] text-transparent dark:from-zinc-100 dark:to-zinc-400">{{ $appName }}</p>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ __('Welcome Back') }}</h1>
                <p class="mt-1.5 text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Enter your credentials to log in') }}</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-6 text-center" :status="session('status')" />

            <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
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
                <div class="space-y-2">
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
                        <div class="flex justify-end">
                            <flux:link class="text-xs font-medium transition-colors hover:text-blue-600 dark:hover:text-blue-400" :href="route('password.request')" wire:navigate>
                                {{ __('Forgot password?') }}
                            </flux:link>
                        </div>
                    @endif
                </div>

                <!-- Remember Me -->
                <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" class="mt-1" />

                <div class="mt-2">
                    <flux:button variant="primary" type="submit" class="w-full transition-all hover:-translate-y-0.5 hover:shadow-md dark:hover:shadow-zinc-800/50" data-test="login-button">
                        {{ __('Log in') }}
                    </flux:button>
                </div>
            </form>
        </div>

        <p class="mt-6 text-center text-xs font-medium uppercase tracking-wider text-zinc-500/80 drop-shadow-sm dark:text-zinc-500">Secure access for {{ $appName }} portal.</p>
    </div>
</x-layouts::auth>
