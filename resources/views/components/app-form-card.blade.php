<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-3xl border border-zinc-200/60 bg-white/60 shadow-sm backdrop-blur-xl dark:border-zinc-700/60 dark:bg-zinc-900/60']) }}>
    {{ $slot }}

    @if (isset($footer))
        <div {{ $footer->attributes->merge(['class' => 'flex items-center gap-3 border-t border-zinc-200/60 bg-white/40 px-4 py-4 dark:border-zinc-700/60 dark:bg-zinc-900/40 sm:px-6']) }}>
            {{ $footer }}
        </div>
    @endif
</div>
