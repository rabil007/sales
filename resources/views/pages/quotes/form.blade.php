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

        <div class="flex items-center justify-between">
            <flux:heading size="lg">{{ $isEdit ? 'Edit Quote' : 'Create Quote' }}</flux:heading>
            <flux:button variant="ghost" :href="route('quotes.index')" wire:navigate>Back to list</flux:button>
        </div>

        <form method="POST" action="{{ $isEdit ? route('quotes.update', $quote) : route('quotes.store') }}" class="space-y-4">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="mb-4 flex gap-2 border-b border-zinc-200 pb-3 text-sm dark:border-zinc-700">
                    <button type="button" data-tab-button="details" class="tab-button rounded-md px-3 py-2 font-medium">Quote Details</button>
                    <button type="button" data-tab-button="crew" class="tab-button rounded-md px-3 py-2 font-medium">Crew Lines</button>
                    <button type="button" data-tab-button="terms" class="tab-button rounded-md px-3 py-2 font-medium">Terms</button>
                </div>

                <div data-tab-content="details" class="tab-content space-y-4">
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

                    <div class="grid gap-4 md:grid-cols-2">
                        <flux:input name="client_name" label="Client Name" :value="old('client_name', $quote->client_name)" required />
                        <flux:input name="client_po" label="Client PO Reference" :value="old('client_po', $quote->client_po)" />
                        <flux:input name="vessel" label="Vessel / Project" :value="old('vessel', $quote->vessel)" />
                        <flux:input name="location" label="Location / Field" :value="old('location', $quote->location)" />
                        <flux:input name="start_date" type="date" label="Contract Start Date" :value="old('start_date', optional($quote->start_date)->toDateString())" />
                        <flux:input name="end_date" type="date" label="Contract End Date" :value="old('end_date', optional($quote->end_date)->toDateString())" />
                    </div>
                </div>

                <div data-tab-content="crew" class="tab-content hidden space-y-3">
                    <div class="flex justify-end">
                        <flux:button type="button" variant="filled" size="sm" id="add-crew-line">Add Row</flux:button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm" id="crew-lines-table">
                            <thead class="bg-zinc-50 dark:bg-zinc-800">
                                <tr class="text-left text-xs uppercase tracking-wide text-zinc-500">
                                    <th class="px-2 py-2">Rank</th>
                                    <th class="px-2 py-2">Category</th>
                                    <th class="px-2 py-2">Qty</th>
                                    <th class="px-2 py-2">Basis</th>
                                    <th class="px-2 py-2">Rate</th>
                                    <th class="px-2 py-2">Duration</th>
                                    <th class="px-2 py-2">OT Rate</th>
                                    <th class="px-2 py-2">Mob Date</th>
                                    <th class="px-2 py-2">Remarks</th>
                                    <th class="px-2 py-2"></th>
                                </tr>
                            </thead>
                            <tbody id="crew-lines-body">
                                @php
                                    $oldCrewLines = old('crew_lines');
                                    $initialCrewLines = is_array($oldCrewLines) ? $oldCrewLines : ($crewLines ?: [['category' => 'Marine', 'qty' => 1, 'basis' => 'Day', 'rate' => 0, 'duration' => 0, 'ot_rate' => 0]]);
                                @endphp
                                @foreach ($initialCrewLines as $index => $line)
                                    <tr class="border-t border-zinc-200 dark:border-zinc-700">
                                        <td class="p-2"><input class="w-full rounded-md border-zinc-300 text-sm dark:border-zinc-700 dark:bg-zinc-800" name="crew_lines[{{ $index }}][rank]" value="{{ $line['rank'] ?? '' }}" /></td>
                                        <td class="p-2"><input class="w-full rounded-md border-zinc-300 text-sm dark:border-zinc-700 dark:bg-zinc-800" name="crew_lines[{{ $index }}][category]" value="{{ $line['category'] ?? 'Marine' }}" /></td>
                                        <td class="p-2"><input class="w-full rounded-md border-zinc-300 text-sm dark:border-zinc-700 dark:bg-zinc-800" type="number" min="1" name="crew_lines[{{ $index }}][qty]" value="{{ $line['qty'] ?? 1 }}" /></td>
                                        <td class="p-2"><input class="w-full rounded-md border-zinc-300 text-sm dark:border-zinc-700 dark:bg-zinc-800" name="crew_lines[{{ $index }}][basis]" value="{{ $line['basis'] ?? 'Day' }}" /></td>
                                        <td class="p-2"><input class="w-full rounded-md border-zinc-300 text-sm dark:border-zinc-700 dark:bg-zinc-800" type="number" step="0.01" min="0" name="crew_lines[{{ $index }}][rate]" value="{{ $line['rate'] ?? 0 }}" /></td>
                                        <td class="p-2"><input class="w-full rounded-md border-zinc-300 text-sm dark:border-zinc-700 dark:bg-zinc-800" type="number" min="0" name="crew_lines[{{ $index }}][duration]" value="{{ $line['duration'] ?? 0 }}" /></td>
                                        <td class="p-2"><input class="w-full rounded-md border-zinc-300 text-sm dark:border-zinc-700 dark:bg-zinc-800" type="number" step="0.01" min="0" name="crew_lines[{{ $index }}][ot_rate]" value="{{ $line['ot_rate'] ?? 0 }}" /></td>
                                        <td class="p-2"><input class="w-full rounded-md border-zinc-300 text-sm dark:border-zinc-700 dark:bg-zinc-800" type="date" name="crew_lines[{{ $index }}][mob_date]" value="{{ $line['mob_date'] ?? '' }}" /></td>
                                        <td class="p-2"><input class="w-full rounded-md border-zinc-300 text-sm dark:border-zinc-700 dark:bg-zinc-800" name="crew_lines[{{ $index }}][remarks]" value="{{ $line['remarks'] ?? '' }}" /></td>
                                        <td class="p-2 text-right"><button type="button" class="text-zinc-500 hover:text-red-500" data-remove-line>Remove</button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div data-tab-content="terms" class="tab-content hidden space-y-4">
                    <flux:select name="payment_terms" label="Payment Terms">
                        @foreach (['30 days from invoice', '45 days from invoice', '60 days from invoice', 'Advance payment', '50% advance, 50% on completion'] as $termsOption)
                            <option value="{{ $termsOption }}" @selected(old('payment_terms', $quote->payment_terms) === $termsOption)>{{ $termsOption }}</option>
                        @endforeach
                    </flux:select>
                    <flux:textarea name="scope" label="Scope of Services" rows="5">{{ old('scope', $quote->scope) }}</flux:textarea>
                </div>
            </div>

            <div class="flex items-center justify-between">
                @if ($isEdit)
                    <div>
                        <flux:button variant="ghost" :href="route('quotes.show', $quote)" wire:navigate>Preview / Print</flux:button>
                    </div>
                @else
                    <div></div>
                @endif
                <div class="flex gap-2">
                    @if ($isEdit)
                        <flux:button variant="danger" type="submit" form="delete-quote-form">Delete</flux:button>
                    @endif
                    <flux:button variant="primary" type="submit">{{ $isEdit ? 'Update Quote' : 'Save Quote' }}</flux:button>
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
                    button.classList.toggle('bg-zinc-900', isActive);
                    button.classList.toggle('text-white', isActive);
                    button.classList.toggle('dark:bg-white', isActive);
                    button.classList.toggle('dark:text-zinc-900', isActive);
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

            function createCrewLineRow(index) {
                const row = document.createElement('tr');
                row.className = 'border-t border-zinc-200 dark:border-zinc-700';
                row.innerHTML = `
                    <td class="p-2"><input class="w-full rounded-md border-zinc-300 text-sm dark:border-zinc-700 dark:bg-zinc-800" name="crew_lines[${index}][rank]" /></td>
                    <td class="p-2"><input class="w-full rounded-md border-zinc-300 text-sm dark:border-zinc-700 dark:bg-zinc-800" name="crew_lines[${index}][category]" value="Marine" /></td>
                    <td class="p-2"><input class="w-full rounded-md border-zinc-300 text-sm dark:border-zinc-700 dark:bg-zinc-800" type="number" min="1" name="crew_lines[${index}][qty]" value="1" /></td>
                    <td class="p-2"><input class="w-full rounded-md border-zinc-300 text-sm dark:border-zinc-700 dark:bg-zinc-800" name="crew_lines[${index}][basis]" value="Day" /></td>
                    <td class="p-2"><input class="w-full rounded-md border-zinc-300 text-sm dark:border-zinc-700 dark:bg-zinc-800" type="number" step="0.01" min="0" name="crew_lines[${index}][rate]" value="0" /></td>
                    <td class="p-2"><input class="w-full rounded-md border-zinc-300 text-sm dark:border-zinc-700 dark:bg-zinc-800" type="number" min="0" name="crew_lines[${index}][duration]" value="0" /></td>
                    <td class="p-2"><input class="w-full rounded-md border-zinc-300 text-sm dark:border-zinc-700 dark:bg-zinc-800" type="number" step="0.01" min="0" name="crew_lines[${index}][ot_rate]" value="0" /></td>
                    <td class="p-2"><input class="w-full rounded-md border-zinc-300 text-sm dark:border-zinc-700 dark:bg-zinc-800" type="date" name="crew_lines[${index}][mob_date]" /></td>
                    <td class="p-2"><input class="w-full rounded-md border-zinc-300 text-sm dark:border-zinc-700 dark:bg-zinc-800" name="crew_lines[${index}][remarks]" /></td>
                    <td class="p-2 text-right"><button type="button" class="text-zinc-500 hover:text-red-500" data-remove-line>Remove</button></td>
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

                if (event.target.matches('[data-remove-line]')) {
                    event.target.closest('tr')?.remove();
                }
            });
        })();
    </script>
</x-layouts::app>
