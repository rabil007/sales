<x-layouts::app :title="$isEdit ? __('Edit Quote') : __('New Quote')">
    <div class="space-y-4">
        @if (session('status'))
            <flux:callout icon="check-circle" color="emerald">{{ session('status') }}</flux:callout>
        @endif

        @if ($errors->any())
            <flux:callout icon="exclamation-triangle" color="red">
                Please review the form fields and try again.
            </flux:callout>
        @endif

        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <flux:heading size="lg">{{ $isEdit ? 'Edit Quote' : 'Create Quote' }}</flux:heading>
            <flux:button variant="ghost" icon="arrow-left" :href="route('quotes.index')" wire:navigate>Back to list</flux:button>
        </div>

        <form method="POST" action="{{ $isEdit ? route('quotes.update', $quote) : route('quotes.store') }}" class="space-y-0">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                {{-- Tab Navigation --}}
                <div class="flex gap-1 border-b border-zinc-200 px-4 pt-3 dark:border-zinc-700 text-sm">
                    <button type="button" data-tab-button="details" class="tab-button rounded-t-md px-4 py-2.5 font-medium transition-colors">Quote Details</button>
                    <button type="button" data-tab-button="crew" class="tab-button rounded-t-md px-4 py-2.5 font-medium transition-colors">Crew Lines</button>
                    <button type="button" data-tab-button="terms" class="tab-button rounded-t-md px-4 py-2.5 font-medium transition-colors">Terms</button>
                </div>

                {{-- Tab: Quote Details --}}
                <div data-tab-content="details" class="tab-content p-6 space-y-6">
                    {{-- Document Info --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500 mb-4">Document Info</h3>
                        <div class="grid gap-4 md:grid-cols-2">
                            <flux:input name="doc_no" label="Document No." :value="old('doc_no', $quote->doc_no)" required />
                            <flux:select name="type" label="Agreement Type" required>
                                @foreach (['Proposal', 'Crew Supply Agreement', 'Rate Contract', 'Purchase Order'] as $typeOption)
                                    <option value="{{ $typeOption }}" @selected(old('type', $quote->type) === $typeOption)>{{ $typeOption }}</option>
                                @endforeach
                            </flux:select>
                            <flux:input name="issue_date" type="date" label="Issue Date" :value="old('issue_date', optional($quote->issue_date)->toDateString())" required />
                            <flux:input name="expiry_date" type="date" label="Expiry Date" :value="old('expiry_date', optional($quote->expiry_date)->toDateString())" />
                            <flux:select name="status" label="Status" required>
                                @foreach (['Draft', 'Sent', 'Approved', 'Active', 'Expired'] as $statusOption)
                                    <option value="{{ $statusOption }}" @selected(old('status', $quote->status) === $statusOption)>{{ $statusOption }}</option>
                                @endforeach
                            </flux:select>
                            <flux:select name="currency" label="Currency" required>
                                @foreach (['AED', 'USD', 'EUR'] as $currencyOption)
                                    <option value="{{ $currencyOption }}" @selected(old('currency', $quote->currency) === $currencyOption)>{{ $currencyOption }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                    </div>

                    <flux:separator />

                    {{-- Client Info --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500 mb-4">Client Info</h3>
                        <div class="grid gap-4 md:grid-cols-2">
                            <flux:input name="client_name" label="Client Name" :value="old('client_name', $quote->client_name)" required />
                            <flux:input name="client_po" label="Client PO Reference" :value="old('client_po', $quote->client_po)" />
                            <flux:input name="vessel" label="Vessel / Project" :value="old('vessel', $quote->vessel)" />
                            <flux:input name="location" label="Location / Field" :value="old('location', $quote->location)" />
                            <flux:input name="start_date" type="date" label="Contract Start Date" :value="old('start_date', optional($quote->start_date)->toDateString())" />
                            <flux:input name="end_date" type="date" label="Contract End Date" :value="old('end_date', optional($quote->end_date)->toDateString())" />
                        </div>
                    </div>
                </div>

                {{-- Tab: Crew Lines --}}
                <div data-tab-content="crew" class="tab-content hidden p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-zinc-500">Add all crew positions included in this quote.</p>
                        <flux:button type="button" variant="filled" icon="plus" size="sm" id="add-crew-line">Add Row</flux:button>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <table class="min-w-full text-sm" id="crew-lines-table">
                            <thead class="bg-zinc-50 dark:bg-zinc-800">
                                <tr class="text-left text-xs uppercase tracking-wide text-zinc-500">
                                    <th class="px-3 py-2.5">Rank</th>
                                    <th class="px-3 py-2.5">Category</th>
                                    <th class="px-3 py-2.5">Qty</th>
                                    <th class="px-3 py-2.5">Basis</th>
                                    <th class="px-3 py-2.5">Rate</th>
                                    <th class="px-3 py-2.5">Duration</th>
                                    <th class="px-3 py-2.5">OT Rate</th>
                                    <th class="px-3 py-2.5">Mob Date</th>
                                    <th class="px-3 py-2.5">Remarks</th>
                                    <th class="px-3 py-2.5"></th>
                                </tr>
                            </thead>
                            <tbody id="crew-lines-body">
                                @php
                                    $oldCrewLines = old('crew_lines');
                                    $initialCrewLines = is_array($oldCrewLines) ? $oldCrewLines : ($crewLines ?: [['category' => 'Marine', 'qty' => 1, 'basis' => 'Day', 'rate' => 0, 'duration' => 0, 'ot_rate' => 0]]);
                                @endphp
                                @foreach ($initialCrewLines as $index => $line)
                                    <tr class="border-t border-zinc-200 dark:border-zinc-700">
                                        <td class="p-2"><input class="h-9 w-full min-w-[100px] rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="crew_lines[{{ $index }}][rank]" value="{{ $line['rank'] ?? '' }}" /></td>
                                        <td class="p-2"><input class="h-9 w-full min-w-[90px] rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="crew_lines[{{ $index }}][category]" value="{{ $line['category'] ?? 'Marine' }}" /></td>
                                        <td class="p-2"><input class="h-9 w-16 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" min="1" name="crew_lines[{{ $index }}][qty]" value="{{ $line['qty'] ?? 1 }}" /></td>
                                        <td class="p-2"><input class="h-9 w-20 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="crew_lines[{{ $index }}][basis]" value="{{ $line['basis'] ?? 'Day' }}" /></td>
                                        <td class="p-2"><input class="h-9 w-24 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" step="0.01" min="0" name="crew_lines[{{ $index }}][rate]" value="{{ $line['rate'] ?? 0 }}" /></td>
                                        <td class="p-2"><input class="h-9 w-20 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" min="0" name="crew_lines[{{ $index }}][duration]" value="{{ $line['duration'] ?? 0 }}" /></td>
                                        <td class="p-2"><input class="h-9 w-24 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" step="0.01" min="0" name="crew_lines[{{ $index }}][ot_rate]" value="{{ $line['ot_rate'] ?? 0 }}" /></td>
                                        <td class="p-2"><input class="h-9 w-32 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="date" name="crew_lines[{{ $index }}][mob_date]" value="{{ $line['mob_date'] ?? '' }}" /></td>
                                        <td class="p-2"><input class="h-9 w-full min-w-[100px] rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="crew_lines[{{ $index }}][remarks]" value="{{ $line['remarks'] ?? '' }}" /></td>
                                        <td class="p-2 text-right">
                                            <button type="button" class="inline-flex items-center justify-center rounded-md p-1.5 text-zinc-400 transition-colors hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-950" data-remove-line title="Remove row">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tab: Terms --}}
                <div data-tab-content="terms" class="tab-content hidden p-6 space-y-4">
                    <flux:select name="payment_terms" label="Payment Terms">
                        @foreach (['30 days from invoice', '45 days from invoice', '60 days from invoice', 'Advance payment', '50% advance, 50% on completion'] as $termsOption)
                            <option value="{{ $termsOption }}" @selected(old('payment_terms', $quote->payment_terms) === $termsOption)>{{ $termsOption }}</option>
                        @endforeach
                    </flux:select>
                    <flux:textarea name="scope" label="Scope of Services" rows="6">{{ old('scope', $quote->scope) }}</flux:textarea>
                </div>
            </div>

            {{-- Sticky Action Bar --}}
            <div class="sticky bottom-0 z-10 flex items-center justify-between rounded-b-xl border border-t-0 border-zinc-200 bg-white/95 px-6 py-4 backdrop-blur dark:border-zinc-700 dark:bg-zinc-900/95">
                <div>
                    @if ($isEdit)
                        <flux:button variant="ghost" icon="eye" :href="route('quotes.show', $quote)" wire:navigate>Preview / Print</flux:button>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    @if ($isEdit)
                        <flux:button
                            variant="danger"
                            icon="trash"
                            type="button"
                            x-data
                            x-on:click="if (confirm('Delete this quote? This action cannot be undone.')) document.getElementById('delete-quote-form').submit()"
                        >
                            Delete
                        </flux:button>
                    @endif
                    <flux:button variant="primary" type="submit" icon="check">
                        {{ $isEdit ? 'Update Quote' : 'Save Quote' }}
                    </flux:button>
                </div>
            </div>
        </form>

        @if ($isEdit)
            <form id="delete-quote-form" method="POST" action="{{ route('quotes.destroy', $quote) }}">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>

    <script>
        (() => {
            const tabs = ['details', 'crew', 'terms'];
            const tabButtons = document.querySelectorAll('[data-tab-button]');
            const tabContents = document.querySelectorAll('[data-tab-content]');

            function setActiveTab(tab) {
                tabButtons.forEach((button) => {
                    const isActive = button.dataset.tabButton === tab;
                    button.classList.toggle('border-b-2', isActive);
                    button.classList.toggle('border-zinc-900', isActive);
                    button.classList.toggle('dark:border-white', isActive);
                    button.classList.toggle('text-zinc-900', isActive);
                    button.classList.toggle('dark:text-white', isActive);
                    button.classList.toggle('text-zinc-500', !isActive);
                    button.classList.toggle('hover:text-zinc-800', !isActive);
                });

                tabContents.forEach((content) => {
                    content.classList.toggle('hidden', content.dataset.tabContent !== tab);
                });
            }

            tabButtons.forEach((button) => {
                button.addEventListener('click', () => setActiveTab(button.dataset.tabButton));
            });
            setActiveTab(tabs[0]);

            const body = document.getElementById('crew-lines-body');
            const addButton = document.getElementById('add-crew-line');

            const inputClass = 'h-9 w-full rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100';
            const trashIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="size-4 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>`;

            function createCrewLineRow(index) {
                const row = document.createElement('tr');
                row.className = 'border-t border-zinc-200 dark:border-zinc-700';
                row.innerHTML = `
                    <td class="p-2"><input class="${inputClass} min-w-[100px]" name="crew_lines[${index}][rank]" /></td>
                    <td class="p-2"><input class="${inputClass} min-w-[90px]" name="crew_lines[${index}][category]" value="Marine" /></td>
                    <td class="p-2"><input class="${inputClass} w-16" type="number" min="1" name="crew_lines[${index}][qty]" value="1" /></td>
                    <td class="p-2"><input class="${inputClass} w-20" name="crew_lines[${index}][basis]" value="Day" /></td>
                    <td class="p-2"><input class="${inputClass} w-24" type="number" step="0.01" min="0" name="crew_lines[${index}][rate]" value="0" /></td>
                    <td class="p-2"><input class="${inputClass} w-20" type="number" min="0" name="crew_lines[${index}][duration]" value="0" /></td>
                    <td class="p-2"><input class="${inputClass} w-24" type="number" step="0.01" min="0" name="crew_lines[${index}][ot_rate]" value="0" /></td>
                    <td class="p-2"><input class="${inputClass} w-32" type="date" name="crew_lines[${index}][mob_date]" /></td>
                    <td class="p-2"><input class="${inputClass} min-w-[100px]" name="crew_lines[${index}][remarks]" /></td>
                    <td class="p-2 text-right">
                        <button type="button" class="inline-flex items-center justify-center rounded-md p-1.5 text-zinc-400 transition-colors hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-950" data-remove-line title="Remove row">
                            ${trashIcon}
                        </button>
                    </td>
                `;
                return row;
            }

            addButton.addEventListener('click', () => {
                const index = body.querySelectorAll('tr').length;
                body.appendChild(createCrewLineRow(index));
            });

            body.addEventListener('click', (event) => {
                if (!(event.target instanceof HTMLElement)) {
                    return;
                }

                if (event.target.matches('[data-remove-line]') || event.target.closest('[data-remove-line]')) {
                    event.target.closest('tr')?.remove();
                }
            });
        })();
    </script>
</x-layouts::app>
