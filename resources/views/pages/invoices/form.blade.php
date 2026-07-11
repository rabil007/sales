<x-layouts::app :title="$isEdit ? __('Edit Invoice') : __('New Invoice')">
    <div class="space-y-6">
        @if ($errors->any())
            <flux:callout icon="exclamation-triangle" color="red">
                Please review the form fields and try again.
            </flux:callout>
        @endif

        {{-- Page Header --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="space-y-1">
                <flux:heading size="lg">{{ $isEdit ? __('Edit invoice') : __('New invoice') }}</flux:heading>
                <flux:text class="text-zinc-500">{{ __('Work through Details → Line items → Payment & notes, then save. PDF preview is available after the invoice is saved.') }}</flux:text>
            </div>
            <flux:button variant="ghost" icon="arrow-left" :href="route('invoices.index')" wire:navigate>{{ __('Back to list') }}</flux:button>
        </div>

        @if (! $isEdit)
            <flux:callout icon="information-circle" color="zinc" inline>
                {{ __('Document number is pre-filled; adjust if needed. Choose a client and add your billing line items before saving.') }}
            </flux:callout>
        @endif

        <form method="POST" action="{{ $isEdit ? route('invoices.update', $invoice) : route('invoices.store') }}" class="space-y-0">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif
            <input type="hidden" name="quote_id" value="{{ old('quote_id', $invoice->quote_id) }}">
            <input type="hidden" name="client_id" id="client-id-input" value="{{ old('client_id', $invoice->client_id) }}">

            <x-app-form-card class="!p-0">
                @if ($isLocked)
                    <div class="border-b border-zinc-200 px-4 py-3 dark:border-zinc-700">
                        <flux:callout color="amber" icon="lock-closed">
                            This invoice is locked because its status is {{ $invoice->status }}.
                        </flux:callout>
                    </div>
                @endif
                {{-- Tab Navigation --}}
                <div class="overflow-x-auto border-b border-zinc-200/60 bg-white/40 dark:border-zinc-700/60 dark:bg-zinc-900/40">
                    <div class="flex min-w-min gap-1 px-2 pt-2 sm:px-4 text-sm">
                        <button type="button" data-tab-button="details" aria-selected="true" class="tab-button inline-flex shrink-0 items-center gap-2 rounded-t-md px-3 py-2.5 font-medium transition-colors sm:px-4">
                            <flux:icon.document-text class="size-4 opacity-70" />
                            <span>{{ __('Invoice details') }}</span>
                        </button>
                        <button type="button" data-tab-button="items" aria-selected="false" class="tab-button inline-flex shrink-0 items-center gap-2 rounded-t-md px-3 py-2.5 font-medium transition-colors sm:px-4">
                            <flux:icon.banknotes class="size-4 opacity-70" />
                            <span>{{ __('Line items') }}</span>
                        </button>
                        <button type="button" data-tab-button="notes" aria-selected="false" class="tab-button inline-flex shrink-0 items-center gap-2 rounded-t-md px-3 py-2.5 font-medium transition-colors sm:px-4">
                            <flux:icon.document-duplicate class="size-4 opacity-70" />
                            <span>{{ __('Payment & notes') }}</span>
                        </button>
                    </div>
                </div>

                {{-- Tab: Invoice Details --}}
                <fieldset data-tab-content="details" class="tab-content min-w-0 p-6 space-y-6" @disabled($isLocked)>
                    {{-- Document Info --}}
                    <div>
                        <flux:heading size="sm" class="mb-4">{{ __('Document details') }}</flux:heading>
                        <div class="grid gap-4 md:grid-cols-2">
                            <flux:input name="doc_no" label="Invoice Number" placeholder="e.g. OMS-INV-2026-001" :value="old('doc_no', $invoice->doc_no)" required />
                            <flux:select name="status" label="Status" required>
                                @foreach (['Draft', 'Issued', 'Paid', 'Overdue', 'Cancelled'] as $statusOption)
                                    <option value="{{ $statusOption }}" @selected(old('status', $invoice->status) === $statusOption)>{{ $statusOption }}</option>
                                @endforeach
                            </flux:select>
                            <flux:input name="issue_date" type="date" label="Issue Date" placeholder="Select issue date" :value="old('issue_date', optional($invoice->issue_date)->toDateString())" required />
                            <flux:input name="due_date" type="date" label="Due Date" placeholder="Select due date" :value="old('due_date', optional($invoice->due_date)->toDateString())" />
                            <flux:select name="currency" label="Currency" required>
                                @foreach (['AED', 'USD', 'EUR'] as $currencyOption)
                                    <option value="{{ $currencyOption }}" @selected(old('currency', $invoice->currency) === $currencyOption)>{{ $currencyOption }}</option>
                                @endforeach
                            </flux:select>
                            <flux:input name="tax_rate" type="number" step="0.01" min="0" max="100" label="Tax Rate (%)" placeholder="0.00" :value="old('tax_rate', $invoice->tax_rate ?? 0.00)" />
                        </div>
                    </div>

                    <flux:separator />

                    {{-- Client Info --}}
                    <div>
                        <flux:heading size="sm" class="mb-4">{{ __('Client details') }}</flux:heading>
                        <div class="grid gap-4 md:grid-cols-2">
                            <flux:select name="client_name" label="{{ __('Client name') }}" required id="client-name-select">
                                <option value="">{{ __('Select client') }}</option>
                                @foreach ($clients as $clientOption)
                                    <option value="{{ $clientOption->name }}" data-client-id="{{ $clientOption->id }}" @selected(old('client_name', $invoice->client_name) === $clientOption->name)>
                                        {{ $clientOption->name }}
                                    </option>
                                @endforeach
                            </flux:select>
                            <flux:input name="client_po" label="Client PO / Ref" placeholder="e.g. PO-2026-0441" :value="old('client_po', $invoice->client_po)" />
                            <flux:input name="vessel" label="Vessel / Asset" placeholder="e.g. Barge 14" :value="old('vessel', $invoice->vessel)" />
                            <flux:input name="location" label="Location / Field" placeholder="e.g. Abu Dhabi Offshore" :value="old('location', $invoice->location)" />
                            <flux:input name="project_name" label="Project Name" placeholder="e.g. Offshore Campaign Crew" :value="old('project_name', $invoice->project_name)" class="md:col-span-2" />
                        </div>
                    </div>
                </fieldset>

                {{-- Tab: Line Items --}}
                <fieldset data-tab-content="items" class="tab-content hidden min-w-0 space-y-4 p-6" @disabled($isLocked)>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0 space-y-1">
                            <flux:heading size="sm">{{ __('Billing line items') }}</flux:heading>
                            <flux:text class="text-zinc-500">{{ __('Add each item or service billed on this invoice. Subtotal and total amount will be automatically calculated when you save.') }}</flux:text>
                        </div>
                        <flux:button type="button" variant="primary" icon="plus" size="sm" id="add-item-line" class="shrink-0">{{ __('Add row') }}</flux:button>
                    </div>

                    <div class="w-full overflow-x-auto rounded-xl border border-zinc-200/60 dark:border-zinc-700/60">
                        <table class="min-w-full text-sm" id="items-table">
                            <thead class="bg-white/80 shadow-sm backdrop-blur-md dark:bg-zinc-800/80">
                                <tr class="text-left text-xs uppercase tracking-wide text-zinc-500">
                                    <th class="px-3 py-2.5">{{ __('Description') }}</th>
                                    <th class="px-3 py-2.5">{{ __('Category') }}</th>
                                    <th class="px-3 py-2.5 w-20">{{ __('Qty') }}</th>
                                    <th class="px-3 py-2.5 w-28">{{ __('Basis') }}</th>
                                    <th class="px-3 py-2.5 w-28">{{ __('Rate') }}</th>
                                    <th class="px-3 py-2.5 w-24">{{ __('Duration') }}</th>
                                    <th class="px-3 py-2.5 w-28">{{ __('Unit') }}</th>
                                    <th class="px-3 py-2.5 w-12"></th>
                                </tr>
                            </thead>
                            <tbody id="items-body" class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @php
                                    $oldItems = old('items');
                                    $initialItems = is_array($oldItems) ? $oldItems : ($items ?: [['description' => '', 'category' => 'Marine', 'qty' => 1, 'basis' => 'Day', 'rate' => 0.00, 'duration' => 1, 'duration_unit' => 'Days']]);
                                @endphp
                                @foreach ($initialItems as $index => $item)
                                    <tr>
                                        <td class="p-2">
                                            <input class="h-9 w-full rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="items[{{ $index }}][description]" value="{{ $item['description'] ?? '' }}" placeholder="Service / Position description" required />
                                        </td>
                                        <td class="p-2">
                                            <input class="h-9 w-32 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="items[{{ $index }}][category]" value="{{ $item['category'] ?? 'Marine' }}" placeholder="Category" />
                                        </td>
                                        <td class="p-2">
                                            <input class="h-9 w-20 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" min="1" name="items[{{ $index }}][qty]" value="{{ $item['qty'] ?? 1 }}" required />
                                        </td>
                                        <td class="p-2">
                                            <select class="h-9 w-28 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="items[{{ $index }}][basis]">
                                                @foreach (['Day', 'Month', 'Fixed', 'Lump Sum', 'Hour'] as $basisOpt)
                                                    <option value="{{ $basisOpt }}" @selected(($item['basis'] ?? '') === $basisOpt)>{{ $basisOpt }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="p-2">
                                            <input class="h-9 w-28 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" step="0.01" min="0" name="items[{{ $index }}][rate]" value="{{ $item['rate'] ?? 0.00 }}" required />
                                        </td>
                                        <td class="p-2">
                                            <input class="h-9 w-24 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" step="0.5" min="0" name="items[{{ $index }}][duration]" value="{{ $item['duration'] ?? 1 }}" />
                                        </td>
                                        <td class="p-2">
                                            <select class="h-9 w-28 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="items[{{ $index }}][duration_unit]">
                                                @foreach (['Days', 'Months', 'Hours', 'Lump Sum', 'Fixed'] as $unitOpt)
                                                    <option value="{{ $unitOpt }}" @selected(($item['duration_unit'] ?? '') === $unitOpt)>{{ $unitOpt }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="p-2 text-right">
                                            <button type="button" class="inline-flex size-9 items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/60 dark:hover:text-red-400" data-remove-line>
                                                <flux:icon.trash class="size-4" />
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </fieldset>

                {{-- Tab: Notes & Payment --}}
                <fieldset data-tab-content="notes" class="tab-content hidden min-w-0 p-6 space-y-6" @disabled($isLocked)>
                    <div>
                        <flux:heading size="sm" class="mb-4">{{ __('Payment instructions & bank details') }}</flux:heading>
                        <flux:textarea name="payment_instructions" rows="4" placeholder="Enter bank account details, wire instructions, or payment terms..." :value="old('payment_instructions', $invoice->payment_instructions)" />
                    </div>

                    <flux:separator />

                    <div>
                        <flux:heading size="sm" class="mb-4">{{ __('Notes & remarks') }}</flux:heading>
                        <flux:textarea name="notes" rows="4" placeholder="Enter any additional remarks visible on the invoice..." :value="old('notes', $invoice->notes)" />
                    </div>
                </fieldset>
            </x-app-form-card>

            {{-- Form Actions --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pt-6 border-t border-zinc-200/80 dark:border-zinc-700/80">
                <flux:button variant="ghost" :href="route('invoices.index')" wire:navigate>{{ __('Cancel') }}</flux:button>
                @if (!$isLocked)
                    <flux:button variant="primary" type="submit" icon="check" class="rounded-full px-6 shadow-md transition-transform hover:-translate-y-0.5">
                        {{ $isEdit ? __('Save Changes') : __('Create Invoice') }}
                    </flux:button>
                @endif
            </div>
        </form>
    </div>

    <script>
        (() => {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const target = button.getAttribute('data-tab-button');
                    tabButtons.forEach(btn => {
                        const isCurrent = btn === button;
                        btn.setAttribute('aria-selected', isCurrent ? 'true' : 'false');
                        if (isCurrent) {
                            btn.classList.add('bg-white', 'text-zinc-900', 'shadow-sm', 'dark:bg-zinc-800', 'dark:text-white');
                            btn.classList.remove('text-zinc-600', 'hover:bg-white/60', 'dark:text-zinc-400', 'dark:hover:bg-zinc-800/60');
                        } else {
                            btn.classList.remove('bg-white', 'text-zinc-900', 'shadow-sm', 'dark:bg-zinc-800', 'dark:text-white');
                            btn.classList.add('text-zinc-600', 'hover:bg-white/60', 'dark:text-zinc-400', 'dark:hover:bg-zinc-800/60');
                        }
                    });

                    tabContents.forEach(content => {
                        if (content.getAttribute('data-tab-content') === target) {
                            content.classList.remove('hidden');
                        } else {
                            content.classList.add('hidden');
                        }
                    });
                });
            });

            // Initialize active tab style
            const activeBtn = document.querySelector('.tab-button[aria-selected="true"]');
            if (activeBtn) activeBtn.click();

            const clientSelect = document.getElementById('client-name-select');
            const clientIdInput = document.getElementById('client-id-input');

            if (clientSelect && clientIdInput) {
                clientSelect.addEventListener('change', () => {
                    const selected = clientSelect.selectedOptions[0];
                    if (selected && selected.dataset.clientId) {
                        clientIdInput.value = selected.dataset.clientId;
                    } else {
                        clientIdInput.value = '';
                    }
                });
            }

            // Dynamic item row addition
            const addButton = document.getElementById('add-item-line');
            const body = document.getElementById('items-body');

            if (addButton && body) {
                addButton.addEventListener('click', () => {
                    const index = body.querySelectorAll('tr').length;
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="p-2"><input class="h-9 w-full rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="items[${index}][description]" value="" placeholder="Service / Position description" required /></td>
                        <td class="p-2"><input class="h-9 w-32 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="items[${index}][category]" value="Marine" placeholder="Category" /></td>
                        <td class="p-2"><input class="h-9 w-20 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" min="1" name="items[${index}][qty]" value="1" required /></td>
                        <td class="p-2">
                            <select class="h-9 w-28 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="items[${index}][basis]">
                                <option value="Day">Day</option>
                                <option value="Month">Month</option>
                                <option value="Fixed">Fixed</option>
                                <option value="Lump Sum">Lump Sum</option>
                                <option value="Hour">Hour</option>
                            </select>
                        </td>
                        <td class="p-2"><input class="h-9 w-28 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" step="0.01" min="0" name="items[${index}][rate]" value="0.00" required /></td>
                        <td class="p-2"><input class="h-9 w-24 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" step="0.5" min="0" name="items[${index}][duration]" value="1" /></td>
                        <td class="p-2">
                            <select class="h-9 w-28 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="items[${index}][duration_unit]">
                                <option value="Days">Days</option>
                                <option value="Months">Months</option>
                                <option value="Hours">Hours</option>
                                <option value="Lump Sum">Lump Sum</option>
                                <option value="Fixed">Fixed</option>
                            </select>
                        </td>
                        <td class="p-2 text-right">
                            <button type="button" class="inline-flex size-9 items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/60 dark:hover:text-red-400" data-remove-line>
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                            </button>
                        </td>
                    `;
                    body.appendChild(row);
                });

                body.addEventListener('click', (event) => {
                    const target = event.target;
                    if (target instanceof HTMLElement && (target.matches('[data-remove-line]') || target.closest('[data-remove-line]'))) {
                        target.closest('tr')?.remove();
                    }
                });
            }
        })();
    </script>
</x-layouts::app>
