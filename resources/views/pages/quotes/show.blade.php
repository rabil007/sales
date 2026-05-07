<x-layouts::app :title="__('Quote Preview')">
    <div class="space-y-4">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <flux:heading size="lg">Quote Preview</flux:heading>
                <flux:text class="text-zinc-500">Preview uses the exact PDF export template.</flux:text>
            </div>
            <div class="flex items-center gap-2">
                <flux:button variant="ghost" icon="pencil" :href="route('quotes.edit', $quote)" wire:navigate>Edit Quote</flux:button>
                <flux:button variant="primary" icon="printer" :href="route('quotes.export', $quote)">Download</flux:button>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <iframe
                src="{{ route('quotes.preview-pdf', $quote) }}"
                title="Quote PDF Preview"
                class="h-[calc(100vh-13rem)] min-h-[700px] w-full bg-white"
            ></iframe>
        </div>
    </div>
</x-layouts::app>
