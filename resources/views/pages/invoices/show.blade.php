<x-layouts::app :title="__('Invoice Preview')">
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <flux:heading size="xl" class="font-bold tracking-tight">{{ $invoice->doc_no }}</flux:heading>
                    @php
                        $color = match($invoice->status) {
                            'Paid'      => 'green',
                            'Issued'    => 'blue',
                            'Overdue'   => 'red',
                            'Cancelled' => 'zinc',
                            default     => 'amber',
                        };
                    @endphp
                    <flux:badge :color="$color" size="sm">{{ $invoice->status }}</flux:badge>
                </div>
                <flux:text class="mt-1 text-zinc-500 dark:text-zinc-400">
                    Client: <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $invoice->client_name }}</span>
                    @if ($invoice->quote)
                        &bull; From Quote: <a href="{{ route('quotes.show', $invoice->quote) }}" class="text-blue-600 hover:underline dark:text-blue-400">{{ $invoice->quote->doc_no }}</a>
                    @endif
                </flux:text>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if ($invoice->status === 'Draft')
                    <form method="POST" action="{{ route('invoices.issue', $invoice) }}">
                        @csrf
                        <flux:button type="submit" variant="filled" icon="paper-airplane" class="rounded-full">Issue Invoice</flux:button>
                    </form>
                @endif

                @if (in_array($invoice->status, ['Issued', 'Overdue'], true))
                    <form method="POST" action="{{ route('invoices.mark-paid', $invoice) }}">
                        @csrf
                        <flux:button type="submit" variant="filled" icon="check-circle" class="rounded-full text-green-700 bg-green-50 hover:bg-green-100 dark:bg-green-900/30 dark:text-green-400">Mark Paid</flux:button>
                    </form>
                @endif

                @if (!in_array($invoice->status, ['Paid', 'Cancelled'], true))
                    <form method="POST" action="{{ route('invoices.cancel', $invoice) }}">
                        @csrf
                        <flux:button type="submit" variant="ghost" icon="x-circle" class="rounded-full text-red-600 hover:bg-red-50 dark:text-red-400">Cancel</flux:button>
                    </form>
                @endif

                @if (!$isLocked)
                    <flux:button variant="ghost" icon="pencil" :href="route('invoices.edit', $invoice)" wire:navigate class="rounded-full">Edit Invoice</flux:button>
                @endif

                <flux:button variant="primary" icon="printer" :href="route('invoices.export-pdf', $invoice)" class="rounded-full px-5 transition-transform hover:-translate-y-0.5 hover:shadow-md">Download PDF</flux:button>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-zinc-200/60 bg-white/60 p-2 shadow-sm backdrop-blur-xl dark:border-zinc-700/60 dark:bg-zinc-900/60">
            <div class="overflow-hidden rounded-2xl border border-zinc-200/50 dark:border-zinc-700/50">
                <iframe
                    src="{{ route('invoices.preview-pdf', $invoice) }}"
                    title="Invoice PDF Preview"
                    class="h-[calc(100vh-13rem)] min-h-[700px] w-full bg-white dark:bg-zinc-900"
                ></iframe>
            </div>
        </div>
    </div>
</x-layouts::app>
