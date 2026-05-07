<x-layouts::app :title="$isEdit ? __('Edit Quote') : __('New Quote')">
    <div class="space-y-4">
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
            <input type="hidden" name="client_id" id="client-id-input" value="{{ old('client_id', $quote->client_id) }}">

            <div class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                @if ($isLocked)
                    <div class="border-b border-zinc-200 px-4 py-3 dark:border-zinc-700">
                        <flux:callout color="amber" icon="lock-closed">
                            This agreement is locked because its status is {{ $quote->status }}.
                        </flux:callout>
                    </div>
                @endif
                {{-- Tab Navigation --}}
                <div class="flex gap-1 border-b border-zinc-200 px-4 pt-3 dark:border-zinc-700 text-sm">
                    <button type="button" data-tab-button="details" class="tab-button rounded-t-md px-4 py-2.5 font-medium transition-colors">Quote Details</button>
                    <button type="button" data-tab-button="crew" class="tab-button rounded-t-md px-4 py-2.5 font-medium transition-colors">Crew Lines</button>
                    <button type="button" data-tab-button="terms" class="tab-button rounded-t-md px-4 py-2.5 font-medium transition-colors">Terms</button>
                    <button type="button" data-tab-button="preview" class="tab-button rounded-t-md px-4 py-2.5 font-medium transition-colors">Preview / Print</button>
                </div>

                {{-- Tab: Quote Details --}}
                <fieldset data-tab-content="details" class="tab-content p-6 space-y-6" @disabled($isLocked)>
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
                            <flux:select name="client_name" label="Client Name" required id="client-name-select">
                                <option value="">Select client</option>
                                @foreach ($clients as $clientOption)
                                    <option value="{{ $clientOption->name }}" data-client-id="{{ $clientOption->id }}" @selected(old('client_name', $quote->client_name) === $clientOption->name)>
                                        {{ $clientOption->name }}
                                    </option>
                                @endforeach
                            </flux:select>
                            <flux:input name="client_po" label="Client PO Reference" :value="old('client_po', $quote->client_po)" />
                            <flux:input name="vessel" label="Vessel / Project" :value="old('vessel', $quote->vessel)" />
                            <flux:input name="location" label="Location / Field" :value="old('location', $quote->location)" />
                            <flux:input name="start_date" type="date" label="Contract Start Date" :value="old('start_date', optional($quote->start_date)->toDateString())" />
                            <flux:input name="end_date" type="date" label="Contract End Date" :value="old('end_date', optional($quote->end_date)->toDateString())" />
                            <flux:input name="duration_text" label="Contract Duration" :value="old('duration_text', $quote->duration_text)" />
                            <flux:input name="project_name" label="Project Name" :value="old('project_name', $quote->project_name)" />
                            <flux:input name="renewal_notice_days" type="number" min="1" label="Renewal Notice (Days)" :value="old('renewal_notice_days', $quote->renewal_notice_days)" />
                        </div>
                    </div>
                </fieldset>

                {{-- Tab: Crew Lines --}}
                <fieldset data-tab-content="crew" class="tab-content hidden p-6 space-y-3" @disabled($isLocked)>
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
                                    <th class="px-3 py-2.5">Monthly Rate</th>
                                    <th class="px-3 py-2.5">Duration (Days)</th>
                                    <th class="px-3 py-2.5">Duration (Months)</th>
                                    <th class="px-3 py-2.5">Manual Total</th>
                                    <th class="px-3 py-2.5">OT Rate</th>
                                    <th class="px-3 py-2.5">Mob Date</th>
                                    <th class="px-3 py-2.5">Demob Date</th>
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
                                        <td class="p-2">
                                            <select class="h-9 w-full min-w-[140px] rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="crew_lines[{{ $index }}][rank]" data-rank-select>
                                                <option value="">Select rank</option>
                                                @foreach ($ranks as $rankOption)
                                                    <option
                                                        value="{{ $rankOption->name }}"
                                                        data-category="{{ $rankOption->category }}"
                                                        data-basis="{{ $rankOption->default_basis }}"
                                                        data-rate="{{ (float) $rankOption->default_rate }}"
                                                        @selected(($line['rank'] ?? null) === $rankOption->name)
                                                    >
                                                        {{ $rankOption->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="p-2"><input class="h-9 w-full min-w-[90px] rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="crew_lines[{{ $index }}][category]" value="{{ $line['category'] ?? 'Marine' }}" /></td>
                                        <td class="p-2"><input class="h-9 w-16 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" min="1" name="crew_lines[{{ $index }}][qty]" value="{{ $line['qty'] ?? 1 }}" /></td>
                                        <td class="p-2"><input class="h-9 w-20 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="crew_lines[{{ $index }}][basis]" value="{{ $line['basis'] ?? 'Day' }}" /></td>
                                        <td class="p-2"><input class="h-9 w-24 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" step="0.01" min="0" name="crew_lines[{{ $index }}][rate]" value="{{ $line['rate'] ?? 0 }}" /></td>
                                        <td class="p-2"><input class="h-9 w-24 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" step="0.01" min="0" name="crew_lines[{{ $index }}][monthly_rate]" value="{{ $line['monthly_rate'] ?? 0 }}" /></td>
                                        <td class="p-2"><input class="h-9 w-20 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" min="0" name="crew_lines[{{ $index }}][duration_days]" value="{{ $line['duration_days'] ?? $line['duration'] ?? 0 }}" /></td>
                                        <td class="p-2"><input class="h-9 w-20 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" min="0" name="crew_lines[{{ $index }}][duration_months]" value="{{ $line['duration_months'] ?? 0 }}" /></td>
                                        <td class="p-2"><input class="h-9 w-24 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" step="0.01" min="0" name="crew_lines[{{ $index }}][manual_total]" value="{{ $line['manual_total'] ?? 0 }}" /></td>
                                        <td class="p-2"><input class="h-9 w-24 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" step="0.01" min="0" name="crew_lines[{{ $index }}][ot_rate]" value="{{ $line['ot_rate'] ?? 0 }}" /></td>
                                        <td class="p-2"><input class="h-9 w-32 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="date" name="crew_lines[{{ $index }}][mob_date]" value="{{ $line['mob_date'] ?? '' }}" /></td>
                                        <td class="p-2"><input class="h-9 w-32 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="date" name="crew_lines[{{ $index }}][demob_date]" value="{{ $line['demob_date'] ?? '' }}" /></td>
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
                </fieldset>

                {{-- Tab: Terms --}}
                <fieldset data-tab-content="terms" class="tab-content hidden p-6 space-y-4" @disabled($isLocked)>
                    <flux:select name="payment_terms" label="Payment Terms">
                        @foreach (['30 days from invoice', '45 days from invoice', '60 days from invoice', 'Advance payment', '50% advance, 50% on completion'] as $termsOption)
                            <option value="{{ $termsOption }}" @selected(old('payment_terms', $quote->payment_terms) === $termsOption)>{{ $termsOption }}</option>
                        @endforeach
                    </flux:select>
                    <flux:textarea name="scope" label="Scope of Services" rows="6">{{ old('scope', $quote->scope) }}</flux:textarea>

                    <flux:separator />

                    <h3 class="text-sm font-semibold">Terms & Conditions</h3>
                    <flux:textarea name="terms_conditions" label="General Terms & Conditions" rows="4">{{ old('terms_conditions', $quote->terms_conditions) }}</flux:textarea>
                    <flux:textarea name="special_conditions" label="Special Conditions" rows="3">{{ old('special_conditions', $quote->special_conditions) }}</flux:textarea>
                    <div class="grid gap-4 md:grid-cols-2">
                        <flux:textarea name="terms[mobilization_replacement]" label="Mobilization / Replacement" rows="4">{{ old('terms.mobilization_replacement', 'OMS will provide qualified replacements within 48 hours of notification. Mobilization costs are for the client account unless otherwise agreed.') }}</flux:textarea>
                        <flux:textarea name="terms[invoicing_payment]" label="Invoicing & Payment" rows="4">{{ old('terms.invoicing_payment', 'Invoices will be raised monthly based on approved timesheets. Payment is due within the agreed payment terms from invoice date.') }}</flux:textarea>
                        <flux:textarea name="terms[accommodation_transport]" label="Accommodation / Transport" rows="4">{{ old('terms.accommodation_transport', 'Accommodation, meals, and transport from port to vessel/site are for the client account unless otherwise stated in this agreement.') }}</flux:textarea>
                        <flux:textarea name="terms[medical_certification]" label="Medical / Certification" rows="4">{{ old('terms.medical_certification', 'All crew will hold valid STCW, MLC, and role-specific certifications. Medical fitness will comply with ENG1 or equivalent standards.') }}</flux:textarea>
                        <flux:textarea name="terms[termination]" label="Termination" rows="4">{{ old('terms.termination', 'Either party may terminate this agreement with 30 days written notice. Immediate termination applies in cases of gross misconduct or safety breach.') }}</flux:textarea>
                        <flux:textarea name="terms[governing_law]" label="Governing Law" rows="4">{{ old('terms.governing_law', 'This agreement shall be governed by the laws of the United Arab Emirates. Disputes shall be resolved through arbitration in Abu Dhabi, UAE.') }}</flux:textarea>
                    </div>
                </fieldset>

                {{-- Tab: Preview --}}
                <div data-tab-content="preview" class="tab-content hidden p-6 space-y-4">
                    <div class="flex justify-end">
                        @if ($isEdit)
                            <flux:button variant="filled" :href="route('quotes.export', $quote)">Print / Export PDF</flux:button>
                        @endif
                    </div>
                    <div id="preview-pane" class="rounded-lg border border-zinc-200 p-6 dark:border-zinc-700"></div>
                </div>

                <div class="flex items-center justify-between border-t border-zinc-200 px-6 py-4 dark:border-zinc-700">
                    <flux:button type="button" variant="ghost" icon="arrow-left" id="tab-prev-button">Back</flux:button>
                    <p class="text-xs text-zinc-500" id="tab-step-indicator"></p>
                    <flux:button type="button" variant="filled" icon-trailing="arrow-right" id="tab-next-button">Next</flux:button>
                </div>
            </div>

            {{-- Sticky Action Bar --}}
            <div class="sticky bottom-0 z-10 flex items-center justify-between rounded-b-xl border border-t-0 border-zinc-200 bg-white/95 px-6 py-4 backdrop-blur dark:border-zinc-700 dark:bg-zinc-900/95">
                <div>
                    <flux:button variant="ghost" icon="arrow-left" :href="route('quotes.index')" wire:navigate>Back to list</flux:button>
                </div>
                <div class="flex items-center">
                    <flux:button variant="primary" type="submit" icon="check" :disabled="$isLocked">
                        {{ $isEdit ? 'Update Quote' : 'Save Quote' }}
                    </flux:button>
                </div>
            </div>
        </form>
    </div>

    <script>
        (() => {
            const tabs = ['details', 'crew', 'terms', 'preview'];
            const tabButtons = document.querySelectorAll('[data-tab-button]');
            const tabContents = document.querySelectorAll('[data-tab-content]');
            const previewPane = document.getElementById('preview-pane');
            const tabPrevButton = document.getElementById('tab-prev-button');
            const tabNextButton = document.getElementById('tab-next-button');
            const tabStepIndicator = document.getElementById('tab-step-indicator');
            const clientNameSelect = document.getElementById('client-name-select');
            const clientIdInput = document.getElementById('client-id-input');
            let activeTab = tabs[0];

            function setActiveTab(tab) {
                activeTab = tab;
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

                if (tab === 'preview') {
                    renderPreview();
                }

                const currentIndex = tabs.indexOf(tab);
                const isFirst = currentIndex === 0;
                const isLast = currentIndex === tabs.length - 1;

                tabPrevButton.classList.toggle('invisible', isFirst);
                tabNextButton.classList.toggle('invisible', isLast);
                tabStepIndicator.textContent = `Step ${currentIndex + 1} of ${tabs.length}`;
            }

            tabButtons.forEach((button) => {
                button.addEventListener('click', () => setActiveTab(button.dataset.tabButton));
            });
            setActiveTab(tabs[0]);

            tabPrevButton.addEventListener('click', () => {
                const currentIndex = tabs.indexOf(activeTab);
                if (currentIndex > 0) {
                    setActiveTab(tabs[currentIndex - 1]);
                }
            });

            tabNextButton.addEventListener('click', () => {
                const currentIndex = tabs.indexOf(activeTab);
                if (currentIndex < tabs.length - 1) {
                    setActiveTab(tabs[currentIndex + 1]);
                }
            });

            const body = document.getElementById('crew-lines-body');
            const addButton = document.getElementById('add-crew-line');

            const inputClass = 'h-9 w-full rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100';
            const trashIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="size-4 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>`;
            const rankOptions = @json($ranks->values()->all());

            if (clientNameSelect instanceof HTMLSelectElement && clientIdInput instanceof HTMLInputElement) {
                const syncClientId = () => {
                    const selectedOption = clientNameSelect.selectedOptions[0];
                    clientIdInput.value = selectedOption?.dataset.clientId ?? '';
                };

                clientNameSelect.addEventListener('change', syncClientId);
                syncClientId();
            }

            function buildRankSelect(index, selected = '') {
                const options = rankOptions.map((rank) => {
                    const isSelected = rank.name === selected ? 'selected' : '';

                    return `<option value="${rank.name}" data-category="${rank.category}" data-basis="${rank.default_basis}" data-rate="${rank.default_rate}" ${isSelected}>${rank.name}</option>`;
                }).join('');

                return `<select class="h-9 w-full min-w-[140px] rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="crew_lines[${index}][rank]" data-rank-select><option value="">Select rank</option>${options}</select>`;
            }

            function createCrewLineRow(index) {
                const row = document.createElement('tr');
                row.className = 'border-t border-zinc-200 dark:border-zinc-700';
                row.innerHTML = `
                    <td class="p-2">${buildRankSelect(index)}</td>
                    <td class="p-2"><input class="${inputClass} min-w-[90px]" name="crew_lines[${index}][category]" value="Marine" /></td>
                    <td class="p-2"><input class="${inputClass} w-16" type="number" min="1" name="crew_lines[${index}][qty]" value="1" /></td>
                    <td class="p-2"><input class="${inputClass} w-20" name="crew_lines[${index}][basis]" value="Day" /></td>
                    <td class="p-2"><input class="${inputClass} w-24" type="number" step="0.01" min="0" name="crew_lines[${index}][rate]" value="0" /></td>
                    <td class="p-2"><input class="${inputClass} w-24" type="number" step="0.01" min="0" name="crew_lines[${index}][monthly_rate]" value="0" /></td>
                    <td class="p-2"><input class="${inputClass} w-20" type="number" min="0" name="crew_lines[${index}][duration_days]" value="0" /></td>
                    <td class="p-2"><input class="${inputClass} w-20" type="number" min="0" name="crew_lines[${index}][duration_months]" value="0" /></td>
                    <td class="p-2"><input class="${inputClass} w-24" type="number" step="0.01" min="0" name="crew_lines[${index}][manual_total]" value="0" /></td>
                    <td class="p-2"><input class="${inputClass} w-24" type="number" step="0.01" min="0" name="crew_lines[${index}][ot_rate]" value="0" /></td>
                    <td class="p-2"><input class="${inputClass} w-32" type="date" name="crew_lines[${index}][mob_date]" /></td>
                    <td class="p-2"><input class="${inputClass} w-32" type="date" name="crew_lines[${index}][demob_date]" /></td>
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

            body.addEventListener('change', (event) => {
                const target = event.target;
                if (!(target instanceof HTMLSelectElement) || !target.matches('[data-rank-select]')) {
                    return;
                }

                const selectedOption = target.selectedOptions[0];
                if (!selectedOption) {
                    return;
                }

                const row = target.closest('tr');
                if (!row) {
                    return;
                }

                const categoryInput = row.querySelector('[name*="[category]"]');
                const basisInput = row.querySelector('[name*="[basis]"]');
                const rateInput = row.querySelector('[name*="[rate]"]');

                if (categoryInput instanceof HTMLInputElement && selectedOption.dataset.category) {
                    categoryInput.value = selectedOption.dataset.category;
                }

                if (basisInput instanceof HTMLInputElement && selectedOption.dataset.basis) {
                    basisInput.value = selectedOption.dataset.basis;
                }

                if (rateInput instanceof HTMLInputElement && selectedOption.dataset.rate) {
                    rateInput.value = selectedOption.dataset.rate;
                }
            });

            function renderPreview() {
                const data = {
                    docNo: document.querySelector('[name="doc_no"]')?.value ?? '',
                    type: document.querySelector('[name="type"]')?.value ?? '',
                    issueDate: document.querySelector('[name="issue_date"]')?.value ?? '',
                    expiryDate: document.querySelector('[name="expiry_date"]')?.value ?? '',
                    status: document.querySelector('[name="status"]')?.value ?? '',
                    currency: document.querySelector('[name="currency"]')?.value ?? 'AED',
                    clientName: document.querySelector('[name="client_name"]')?.value ?? '',
                    clientPo: document.querySelector('[name="client_po"]')?.value ?? '',
                    vessel: document.querySelector('[name="vessel"]')?.value ?? '',
                    location: document.querySelector('[name="location"]')?.value ?? '',
                    paymentTerms: document.querySelector('[name="payment_terms"]')?.value ?? '',
                    scope: document.querySelector('[name="scope"]')?.value ?? '',
                };

                const rows = [...document.querySelectorAll('#crew-lines-body tr')].map((row, index) => {
                    const get = (name) => row.querySelector(`[name*="[${name}]"]`)?.value ?? '';
                    const qty = Number(get('qty') || 0);
                    const rate = Number(get('rate') || 0);
                    const durationDays = Number(get('duration_days') || get('duration') || 0);
                    const durationMonths = Number(get('duration_months') || 0);
                    const monthlyRate = Number(get('monthly_rate') || 0);
                    const manualTotal = Number(get('manual_total') || 0);
                    const basis = get('basis');
                    const amount = basis === 'Month'
                        ? qty * durationMonths * monthlyRate
                        : (basis === 'Fixed' ? manualTotal : qty * rate * durationDays);

                    return {
                        index: index + 1,
                        rank: get('rank'),
                        category: get('category'),
                        qty,
                        basis,
                        rate,
                        duration: basis === 'Month' ? durationMonths : durationDays,
                        amount,
                    };
                });

                const total = rows.reduce((sum, row) => sum + row.amount, 0);
                const fmt = (value) => Number(value || 0).toLocaleString('en-AE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                previewPane.innerHTML = `
                    <div class="space-y-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-lg font-semibold">Overseas Marine Services</p>
                                <p class="text-sm text-zinc-500">${data.type || '-'}</p>
                            </div>
                            <div class="text-right text-sm">
                                <p class="font-medium">${data.docNo || '-'}</p>
                                <p class="text-zinc-500">Issued: ${data.issueDate || '-'}</p>
                            </div>
                        </div>
                        <div class="grid gap-2 rounded-md bg-zinc-50 p-3 text-sm dark:bg-zinc-800">
                            <p><span class="text-zinc-500">Client:</span> ${data.clientName || '-'}</p>
                            <p><span class="text-zinc-500">Vessel / Project:</span> ${data.vessel || '-'}</p>
                            <p><span class="text-zinc-500">Location:</span> ${data.location || '-'}</p>
                            <p><span class="text-zinc-500">Status:</span> ${data.status || '-'}</p>
                            <p><span class="text-zinc-500">Expiry:</span> ${data.expiryDate || '-'}</p>
                            <p><span class="text-zinc-500">Payment Terms:</span> ${data.paymentTerms || '-'}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-medium">Scope of Services</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-300">${data.scope || '-'}</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-zinc-50 dark:bg-zinc-800">
                                    <tr class="text-left text-xs uppercase text-zinc-500">
                                        <th class="px-2 py-2">#</th><th class="px-2 py-2">Rank</th><th class="px-2 py-2">Category</th><th class="px-2 py-2">Qty</th><th class="px-2 py-2">Basis</th><th class="px-2 py-2">Rate</th><th class="px-2 py-2">Duration</th><th class="px-2 py-2">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${rows.length ? rows.map((row) => `
                                        <tr class="border-t border-zinc-200 dark:border-zinc-700">
                                            <td class="px-2 py-2">${row.index}</td>
                                            <td class="px-2 py-2">${row.rank || '-'}</td>
                                            <td class="px-2 py-2">${row.category || '-'}</td>
                                            <td class="px-2 py-2">${row.qty}</td>
                                            <td class="px-2 py-2">${row.basis || '-'}</td>
                                            <td class="px-2 py-2">${data.currency} ${fmt(row.rate)}</td>
                                            <td class="px-2 py-2">${row.duration}</td>
                                            <td class="px-2 py-2">${data.currency} ${fmt(row.amount)}</td>
                                        </tr>
                                    `).join('') : '<tr><td colspan="8" class="px-2 py-3 text-center text-zinc-500">No crew lines added.</td></tr>'}
                                </tbody>
                                <tfoot>
                                    <tr class="border-t border-zinc-200 dark:border-zinc-700">
                                        <td colspan="7" class="px-2 py-2 text-right font-medium">Total</td>
                                        <td class="px-2 py-2 font-semibold">${data.currency} ${fmt(total)}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="text-xs text-zinc-500">
                            <p><strong>Client PO:</strong> ${data.clientPo || '-'}</p>
                        </div>
                    </div>
                `;
            }
        })();
    </script>
</x-layouts::app>
