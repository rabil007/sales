<x-layouts::app :title="__('Quote Preview')">
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight">Quote Preview</flux:heading>
                <flux:text class="mt-1 text-zinc-500 dark:text-zinc-400">Preview uses the exact PDF export template.</flux:text>
            </div>
            <div class="flex items-center gap-2">
                <flux:button variant="ghost" icon="pencil" :href="route('quotes.edit', $quote)" wire:navigate class="rounded-full">Edit Quote</flux:button>
                <flux:button variant="primary" icon="printer" :href="route('quotes.export', $quote)" class="rounded-full px-5 transition-transform hover:-translate-y-0.5 hover:shadow-md">Download</flux:button>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-zinc-200/60 bg-white/60 p-2 shadow-sm backdrop-blur-xl dark:border-zinc-700/60 dark:bg-zinc-900/60">
            <div class="overflow-hidden rounded-2xl border border-zinc-200/50 dark:border-zinc-700/50">
                <iframe
                    src="{{ route('quotes.preview-pdf', $quote) }}"
                    title="Quote PDF Preview"
                    class="h-[calc(100vh-13rem)] min-h-[700px] w-full bg-white dark:bg-zinc-900"
                ></iframe>
            </div>
        </div>
    </div>
</x-layouts::app>
