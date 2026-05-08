<x-layouts::app :title="$isEdit ? __('Edit Quote') : __('New Quote')">
    <div class="space-y-6">
        @if ($errors->any())
            <flux:callout icon="exclamation-triangle" color="red">
                Please review the form fields and try again.
            </flux:callout>
        @endif

        {{-- Page Header --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="space-y-1">
                <flux:heading size="lg">{{ $isEdit ? __('Edit quote') : __('New quote') }}</flux:heading>
                <flux:text class="text-zinc-500">{{ __('Work through Details → Crew lines → Terms, then save. PDF preview is available after the quote exists.') }}</flux:text>
            </div>
            <flux:button variant="ghost" icon="arrow-left" :href="route('quotes.index')" wire:navigate>{{ __('Back to list') }}</flux:button>
        </div>

        @if (! $isEdit)
            <flux:callout icon="information-circle" color="zinc" inline>
                {{ __('Document number is pre-filled; adjust if needed. Choose a client, add at least one crew line with a rank, then review terms before saving.') }}
            </flux:callout>
        @endif

        <form method="POST" action="{{ $isEdit ? route('quotes.update', $quote) : route('quotes.store') }}" class="space-y-0">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif
            <input type="hidden" name="client_id" id="client-id-input" value="{{ old('client_id', $quote->client_id) }}">

            <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                @if ($isLocked)
                    <div class="border-b border-zinc-200 px-4 py-3 dark:border-zinc-700">
                        <flux:callout color="amber" icon="lock-closed">
                            This agreement is locked because its status is {{ $quote->status }}.
                        </flux:callout>
                    </div>
                @endif
                {{-- Tab Navigation --}}
                <div class="overflow-x-auto border-b border-zinc-200 bg-zinc-50/80 dark:border-zinc-700 dark:bg-zinc-900/80">
                    <div class="flex min-w-min gap-1 px-2 pt-2 sm:px-4 text-sm">
                        <button type="button" data-tab-button="details" aria-selected="true" class="tab-button inline-flex shrink-0 items-center gap-2 rounded-t-md px-3 py-2.5 font-medium transition-colors sm:px-4">
                            <flux:icon.document-text class="size-4 opacity-70" />
                            <span class="sm:hidden">{{ __('Details') }}</span>
                            <span class="hidden sm:inline">{{ __('Quote details') }}</span>
                        </button>
                        <button type="button" data-tab-button="crew" aria-selected="false" class="tab-button inline-flex shrink-0 items-center gap-2 rounded-t-md px-3 py-2.5 font-medium transition-colors sm:px-4">
                            <flux:icon.user-group class="size-4 opacity-70" />
                            <span class="sm:hidden">{{ __('Crew') }}</span>
                            <span class="hidden sm:inline">{{ __('Crew lines') }}</span>
                        </button>
                        <button type="button" data-tab-button="terms" aria-selected="false" class="tab-button inline-flex shrink-0 items-center gap-2 rounded-t-md px-3 py-2.5 font-medium transition-colors sm:px-4">
                            <flux:icon.document-duplicate class="size-4 opacity-70" />
                            <span>{{ __('Terms') }}</span>
                        </button>
                    </div>
                </div>

                {{-- Tab: Quote Details --}}
                <fieldset data-tab-content="details" class="tab-content min-w-0 p-6 space-y-6" @disabled($isLocked)>
                    {{-- Document Info --}}
                    <div>
                        <flux:heading size="sm" class="mb-4">{{ __('Document details') }}</flux:heading>
                        <div class="grid gap-4 md:grid-cols-2">
                            <flux:input name="doc_no" label="Document No." placeholder="e.g. OMS-Q-2026-001" :value="old('doc_no', $quote->doc_no)" required />
                            <flux:select name="type" label="Agreement Type" required>
                                @foreach (['Proposal', 'Crew Supply Agreement', 'Rate Contract', 'Purchase Order'] as $typeOption)
                                    <option value="{{ $typeOption }}" @selected(old('type', $quote->type) === $typeOption)>{{ $typeOption }}</option>
                                @endforeach
                            </flux:select>
                            <flux:input name="issue_date" type="date" label="Issue Date" placeholder="Select issue date" :value="old('issue_date', optional($quote->issue_date)->toDateString())" required />
                            <flux:input name="expiry_date" type="date" label="Expiry Date" placeholder="Select expiry date" :value="old('expiry_date', optional($quote->expiry_date)->toDateString())" />
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
                        <flux:heading size="sm" class="mb-4">{{ __('Client & contract') }}</flux:heading>
                        <div class="grid gap-4 md:grid-cols-2">
                            <flux:select name="client_name" label="{{ __('Client name') }}" required id="client-name-select">
                                <option value="">{{ __('Select client') }}</option>
                                @foreach ($clients as $clientOption)
                                    <option value="{{ $clientOption->name }}" data-client-id="{{ $clientOption->id }}" @selected(old('client_name', $quote->client_name) === $clientOption->name)>
                                        {{ $clientOption->name }}
                                    </option>
                                @endforeach
                            </flux:select>
                            <flux:input name="client_po" label="Client PO Reference" placeholder="e.g. PO-2026-0441" :value="old('client_po', $quote->client_po)" />
                            <flux:input name="vessel" label="Vessel / Project" placeholder="e.g. Barge 14" :value="old('vessel', $quote->vessel)" />
                            <flux:input name="location" label="Location / Field" placeholder="e.g. Abu Dhabi Offshore" :value="old('location', $quote->location)" />
                            <flux:input name="start_date" type="date" label="Contract Start Date" placeholder="Select start date" :value="old('start_date', optional($quote->start_date)->toDateString())" />
                            <flux:input name="end_date" type="date" label="Contract End Date" placeholder="Select end date" :value="old('end_date', optional($quote->end_date)->toDateString())" />
                            <flux:input name="duration_text" label="Contract Duration" placeholder="e.g. 30 days / 6 months" :value="old('duration_text', $quote->duration_text)" />
                            <flux:input name="project_name" label="Project Name" placeholder="e.g. Offshore Campaign Crew" :value="old('project_name', $quote->project_name)" />
                        </div>
                    </div>
                </fieldset>

                {{-- Tab: Crew Lines --}}
                <fieldset data-tab-content="crew" class="tab-content hidden min-w-0 space-y-4 p-6" @disabled($isLocked)>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0 space-y-1">
                            <flux:heading size="sm">{{ __('Crew lines') }}</flux:heading>
                            <flux:text class="text-zinc-500">{{ __('Add each position billed on this quote. Scroll horizontally if your screen is narrow.') }}</flux:text>
                        </div>
                        <flux:button type="button" variant="primary" icon="plus" size="sm" id="add-crew-line" class="shrink-0">{{ __('Add row') }}</flux:button>
                    </div>

                    <flux:callout icon="calculator" color="blue" inline>
                        {{ __('Day: qty × daily rate × days. Month: qty × monthly rate × months. Fixed: use Manual total (rate column is unused for that row). Totals update when you save.') }}
                    </flux:callout>

                    <div class="max-h-[60vh] w-full overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700 sm:max-h-none">
                        <table class="min-w-full text-sm" id="crew-lines-table">
                            <thead class="sticky top-0 z-10 bg-zinc-50 shadow-sm dark:bg-zinc-800/95">
                                <tr class="text-left text-xs uppercase tracking-wide text-zinc-500">
                                    <th class="px-3 py-2.5">{{ __('Rank') }}</th>
                                    <th class="px-3 py-2.5">{{ __('Category') }}</th>
                                    <th class="px-3 py-2.5">{{ __('Qty') }}</th>
                                    <th class="px-3 py-2.5">{{ __('Basis') }}</th>
                                    <th class="px-3 py-2.5">{{ __('Rate') }}</th>
                                    <th class="px-3 py-2.5">{{ __('Monthly Rate') }}</th>
                                    <th class="px-3 py-2.5">{{ __('Duration (Days)') }}</th>
                                    <th class="px-3 py-2.5">{{ __('Duration (Months)') }}</th>
                                    <th class="px-3 py-2.5">{{ __('Manual') }}</th>
                                    <th class="px-3 py-2.5">{{ __('OT') }}</th>
                                    <th class="px-3 py-2.5">{{ __('Mob') }}</th>
                                    <th class="px-3 py-2.5">{{ __('Demob') }}</th>
                                    <th class="px-3 py-2.5">{{ __('Remarks') }}</th>
                                    <th class="px-3 py-2.5"></th>
                                </tr>
                            </thead>
                            <tbody id="crew-lines-body">
                                @php
                                    $oldCrewLines = old('crew_lines');
                                    $initialCrewLines = is_array($oldCrewLines) ? $oldCrewLines : ($crewLines ?: [['category' => 'Marine', 'qty' => 1, 'basis' => 'Day', 'rate' => 0, 'duration' => 0, 'ot_rate' => 0]]);
                                @endphp
                                @foreach ($initialCrewLines as $index => $line)
                                    @php
                                        $basisRaw = $line['basis'] ?? 'Day';
                                        $basisValue = in_array($basisRaw, ['Day', 'Month', 'Fixed'], true) ? $basisRaw : 'Day';
                                    @endphp
                                    <tr class="border-t border-zinc-200 dark:border-zinc-700">
                                        <td class="p-2">
                                            <select class="h-9 w-full min-w-[140px] rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="crew_lines[{{ $index }}][rank]" data-rank-select>
                                                <option value="">{{ __('Select rank') }}</option>
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
                                        <td class="p-2"><input class="h-9 w-full min-w-[90px] rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="crew_lines[{{ $index }}][category]" value="{{ $line['category'] ?? 'Marine' }}" placeholder="Marine" autocomplete="off" /></td>
                                        <td class="p-2"><input class="h-9 w-16 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" min="1" name="crew_lines[{{ $index }}][qty]" value="{{ $line['qty'] ?? 1 }}" placeholder="1" /></td>
                                        <td class="p-2">
                                            <select class="h-9 w-26 shrink-0 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="crew_lines[{{ $index }}][basis]" data-basis-select>
                                                @foreach (['Day', 'Month', 'Fixed'] as $basisOption)
                                                    <option value="{{ $basisOption }}" @selected($basisValue === $basisOption)>{{ $basisOption }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="p-2"><input class="h-9 w-24 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" step="0.01" min="0" name="crew_lines[{{ $index }}][rate]" value="{{ $line['rate'] ?? 0 }}" placeholder="0.00" /></td>
                                        <td class="p-2"><input class="h-9 w-24 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" step="0.01" min="0" name="crew_lines[{{ $index }}][monthly_rate]" value="{{ $line['monthly_rate'] ?? 0 }}" placeholder="0.00" /></td>
                                        <td class="p-2"><input class="h-9 w-20 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" min="0" name="crew_lines[{{ $index }}][duration_days]" value="{{ $line['duration_days'] ?? $line['duration'] ?? 0 }}" placeholder="0" /></td>
                                        <td class="p-2"><input class="h-9 w-20 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" min="0" name="crew_lines[{{ $index }}][duration_months]" value="{{ $line['duration_months'] ?? 0 }}" placeholder="0" /></td>
                                        <td class="p-2"><input class="h-9 w-24 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" step="0.01" min="0" name="crew_lines[{{ $index }}][manual_total]" value="{{ $line['manual_total'] ?? 0 }}" placeholder="0.00" /></td>
                                        <td class="p-2"><input class="h-9 w-24 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="number" step="0.01" min="0" name="crew_lines[{{ $index }}][ot_rate]" value="{{ $line['ot_rate'] ?? 0 }}" placeholder="0.00" /></td>
                                        <td class="p-2"><input class="h-9 w-32 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="date" name="crew_lines[{{ $index }}][mob_date]" value="{{ $line['mob_date'] ?? '' }}" placeholder="Select date" /></td>
                                        <td class="p-2"><input class="h-9 w-32 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" type="date" name="crew_lines[{{ $index }}][demob_date]" value="{{ $line['demob_date'] ?? '' }}" placeholder="Select date" /></td>
                                        <td class="p-2"><input class="h-9 w-full min-w-[100px] rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="crew_lines[{{ $index }}][remarks]" value="{{ $line['remarks'] ?? '' }}" placeholder="Optional notes" autocomplete="off" /></td>
                                        <td class="p-2 text-right">
                                            <flux:tooltip content="{{ __('Remove row') }}">
                                                <flux:button type="button" variant="ghost" icon="trash" size="sm" class="size-9!" data-remove-line />
                                            </flux:tooltip>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </fieldset>

                {{-- Tab: Terms --}}
                <fieldset data-tab-content="terms" class="tab-content hidden min-w-0 p-6 space-y-4" @disabled($isLocked)>
                    <flux:select name="payment_terms" label="Payment Terms">
                        @foreach (['30 days from invoice', '45 days from invoice', '60 days from invoice', 'Advance payment', '50% advance, 50% on completion'] as $termsOption)
                            <option value="{{ $termsOption }}" @selected(old('payment_terms', $quote->payment_terms) === $termsOption)>{{ $termsOption }}</option>
                        @endforeach
                    </flux:select>
                    <flux:textarea name="scope" label="Scope of Services" placeholder="Describe services included in this quote..." rows="6">{{ old('scope', $quote->scope) }}</flux:textarea>

                    <flux:textarea name="special_conditions" label="Special Conditions" placeholder="Add any special conditions (optional)..." rows="3">{{ old('special_conditions', $quote->special_conditions) }}</flux:textarea>
                </fieldset>

                <div class="flex items-center justify-between gap-3 border-t border-zinc-200 bg-zinc-50/70 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-900/50 sm:px-6">
                    <flux:button type="button" variant="ghost" icon="arrow-left" id="tab-prev-button">{{ __('Back') }}</flux:button>
                    <p class="text-center text-xs tabular-nums text-zinc-500" id="tab-step-indicator"></p>
                    <flux:button type="button" variant="primary" icon-trailing="arrow-right" id="tab-next-button">{{ __('Next') }}</flux:button>
                </div>
            </div>

            {{-- Sticky Action Bar --}}
            <div class="sticky bottom-0 z-10 mt-px flex justify-end rounded-b-xl border border-t-0 border-zinc-200 bg-white/95 px-4 py-4 backdrop-blur sm:px-6 dark:border-zinc-700 dark:bg-zinc-900/95">
                <flux:button variant="primary" type="submit" icon="check" :disabled="$isLocked">
                    {{ $isEdit ? __('Update quote') : __('Save quote') }}
                </flux:button>
            </div>
        </form>
    </div>

    <script>
        (() => {
            const tabs = ['details', 'crew', 'terms'];
            const tabButtons = document.querySelectorAll('[data-tab-button]');
            const tabContents = document.querySelectorAll('[data-tab-content]');
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
                    button.setAttribute('aria-selected', isActive ? 'true' : 'false');
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
            const selectRankPlaceholder = @json(__('Select rank'));
            const removeRowLabel = @json(__('Remove row'));
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

                return `<select class="h-9 w-full min-w-[140px] rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="crew_lines[${index}][rank]" data-rank-select><option value="">${selectRankPlaceholder}</option>${options}</select>`;
            }

            function buildBasisSelect(index, selected = 'Day') {
                const normalized = ['Day', 'Month', 'Fixed'].includes(selected) ? selected : 'Day';
                const options = ['Day', 'Month', 'Fixed'].map((b) =>
                    `<option value="${b}" ${b === normalized ? 'selected' : ''}>${b}</option>`
                ).join('');

                return `<select class="h-9 w-26 shrink-0 rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100" name="crew_lines[${index}][basis]" data-basis-select>${options}</select>`;
            }

            function createCrewLineRow(index) {
                const row = document.createElement('tr');
                row.className = 'border-t border-zinc-200 dark:border-zinc-700';
                row.innerHTML = `
                    <td class="p-2">${buildRankSelect(index)}</td>
                    <td class="p-2"><input class="${inputClass} min-w-[90px]" name="crew_lines[${index}][category]" value="Marine" placeholder="Marine" /></td>
                    <td class="p-2"><input class="${inputClass} w-16" type="number" min="1" name="crew_lines[${index}][qty]" value="1" placeholder="1" /></td>
                    <td class="p-2">${buildBasisSelect(index)}</td>
                    <td class="p-2"><input class="${inputClass} w-24" type="number" step="0.01" min="0" name="crew_lines[${index}][rate]" value="0" placeholder="0.00" /></td>
                    <td class="p-2"><input class="${inputClass} w-24" type="number" step="0.01" min="0" name="crew_lines[${index}][monthly_rate]" value="0" placeholder="0.00" /></td>
                    <td class="p-2"><input class="${inputClass} w-20" type="number" min="0" name="crew_lines[${index}][duration_days]" value="0" placeholder="0" /></td>
                    <td class="p-2"><input class="${inputClass} w-20" type="number" min="0" name="crew_lines[${index}][duration_months]" value="0" placeholder="0" /></td>
                    <td class="p-2"><input class="${inputClass} w-24" type="number" step="0.01" min="0" name="crew_lines[${index}][manual_total]" value="0" placeholder="0.00" /></td>
                    <td class="p-2"><input class="${inputClass} w-24" type="number" step="0.01" min="0" name="crew_lines[${index}][ot_rate]" value="0" placeholder="0.00" /></td>
                    <td class="p-2"><input class="${inputClass} w-32" type="date" name="crew_lines[${index}][mob_date]" placeholder="Select date" /></td>
                    <td class="p-2"><input class="${inputClass} w-32" type="date" name="crew_lines[${index}][demob_date]" placeholder="Select date" /></td>
                    <td class="p-2"><input class="${inputClass} min-w-[100px]" name="crew_lines[${index}][remarks]" placeholder="Optional notes" /></td>
                    <td class="p-2 text-right">
                        <button type="button" class="inline-flex size-9 items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/60 dark:hover:text-red-400" data-remove-line aria-label="${removeRowLabel}">
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
                const basisSelect = row.querySelector('[data-basis-select]');
                const rateInput = row.querySelector('[name*="[rate]"]');

                if (categoryInput instanceof HTMLInputElement && selectedOption.dataset.category) {
                    categoryInput.value = selectedOption.dataset.category;
                }

                if (basisSelect instanceof HTMLSelectElement && selectedOption.dataset.basis) {
                    const b = selectedOption.dataset.basis;
                    if (['Day', 'Month', 'Fixed'].includes(b)) {
                        basisSelect.value = b;
                    }
                }

                if (rateInput instanceof HTMLInputElement && selectedOption.dataset.rate) {
                    rateInput.value = selectedOption.dataset.rate;
                }
            });
        })();
    </script>
</x-layouts::app>
