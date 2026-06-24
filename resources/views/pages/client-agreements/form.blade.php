<x-layouts::app :title="$isEdit ? __('Edit Client Agreement') : __('New Client Agreement')">
    <div class="space-y-6">
        @if ($errors->any())
            <flux:callout icon="exclamation-triangle" color="red">
                {{ __('Please review agreement details and try again.') }}
            </flux:callout>
        @endif

        {{-- Page Header --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="space-y-1">
                <flux:heading size="lg">{{ $isEdit ? __('Edit Client Agreement') : __('New Client Agreement') }}</flux:heading>
                <flux:text class="text-zinc-500">{{ $isEdit ? __('Update agreement details and contract period.') : __('Work through Details → Crew lines, then save.') }}</flux:text>
            </div>
            <flux:button variant="ghost" icon="arrow-left" :href="route('client-agreements.index')" wire:navigate>{{ __('Back to list') }}</flux:button>
        </div>

        <form method="POST" action="{{ $isEdit ? route('client-agreements.update', $agreement) : route('client-agreements.store') }}" class="space-y-0">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <x-app-form-card class="!p-0">
                {{-- Tab Navigation --}}
                <div class="overflow-x-auto border-b border-zinc-200/60 bg-white/40 dark:border-zinc-700/60 dark:bg-zinc-900/40">
                    <div class="flex min-w-min gap-1 px-2 pt-2 sm:px-4 text-sm">
                        <button type="button" data-tab-button="details" aria-selected="true" class="tab-button inline-flex shrink-0 items-center gap-2 rounded-t-md px-3 py-2.5 font-medium transition-colors sm:px-4">
                            <flux:icon.document-text class="size-4 opacity-70" />
                            <span class="sm:hidden">{{ __('Details') }}</span>
                            <span class="hidden sm:inline">{{ __('Agreement details') }}</span>
                        </button>
                        <button type="button" data-tab-button="crew" aria-selected="false" class="tab-button inline-flex shrink-0 items-center gap-2 rounded-t-md px-3 py-2.5 font-medium transition-colors sm:px-4">
                            <flux:icon.user-group class="size-4 opacity-70" />
                            <span class="sm:hidden">{{ __('Crew') }}</span>
                            <span class="hidden sm:inline">{{ __('Crew lines') }}</span>
                        </button>
                    </div>
                </div>

                {{-- Tab: Agreement Details --}}
                <fieldset data-tab-content="details" class="tab-content min-w-0 p-6 space-y-6">
                    <div>
                        <flux:heading size="sm" class="mb-4">{{ __('Agreement Details') }}</flux:heading>
                        <div class="grid gap-4 md:grid-cols-2">
                            <flux:select name="client_id" label="{{ __('Client Name') }}" required>
                                <option value="">{{ __('Select client') }}</option>
                                @foreach ($clients as $clientOption)
                                    <option value="{{ $clientOption->id }}" @selected((string) old('client_id', $agreement->client_id) === (string) $clientOption->id)>
                                        {{ $clientOption->name }}
                                    </option>
                                @endforeach
                            </flux:select>
                            <flux:input name="agreement_ref" label="{{ __('Agreement Ref.') }}" placeholder="e.g. OMS-AGR-2026-001" :value="old('agreement_ref', $agreement->agreement_ref)" required />
                            <flux:textarea name="scope_of_work" label="{{ __('Scope of Work') }}" placeholder="Describe the scope of work included in this agreement..." rows="4" class="md:col-span-2">{{ old('scope_of_work', $agreement->scope_of_work) }}</flux:textarea>
                        </div>

                        @if ($clients->isEmpty())
                            <flux:text class="mt-3 text-sm text-zinc-500">
                                {{ __('No clients available yet.') }}
                                <flux:link :href="route('clients.create')" wire:navigate class="font-medium">{{ __('Create a client') }}</flux:link>
                                {{ __('before adding an agreement.') }}
                            </flux:text>
                        @endif
                    </div>

                    <flux:separator />

                    {{-- Contract Period --}}
                    <div>
                        <flux:heading size="sm" class="mb-4">{{ __('Contract Period') }}</flux:heading>
                        <div class="grid gap-4 md:grid-cols-3">
                            <flux:input name="duration_days" id="duration-days-input" type="number" min="1" step="1" label="{{ __('Duration (days)') }}" placeholder="e.g. 30" :value="old('duration_days', $agreement->duration_days)" required />
                            <flux:input name="start_date" id="start-date-input" type="date" label="{{ __('Start Date') }}" :value="old('start_date', optional($agreement->start_date)->toDateString())" required />
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <flux:label>{{ __('End Date') }}</flux:label>
                                    <flux:badge size="sm" color="emerald">{{ __('Auto-calculated') }}</flux:badge>
                                </div>
                                <flux:input name="end_date" id="end-date-input" type="date" :value="old('end_date', optional($agreement->end_date)->toDateString())" readonly class="bg-emerald-50/50 dark:bg-emerald-950/20" />
                            </div>
                        </div>
                    </div>

                    <flux:separator />

                    {{-- Billing --}}
                    <div>
                        <flux:heading size="sm" class="mb-4">{{ __('Billing') }}</flux:heading>
                        <div class="grid gap-4 md:grid-cols-2">
                            <flux:input name="monthly_invoice_value" type="number" min="0" step="0.01" label="{{ __('Monthly Invoice Value (USD)') }}" placeholder="e.g. 12500.00" icon="currency-dollar" :value="old('monthly_invoice_value', $agreement->monthly_invoice_value)" required />
                        </div>
                    </div>
                </fieldset>

                {{-- Tab: Crew Lines --}}
                <fieldset data-tab-content="crew" class="tab-content hidden min-w-0 space-y-4 p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0 space-y-1">
                            <flux:heading size="sm">{{ __('Crew lines') }}</flux:heading>
                            <flux:text class="text-zinc-500">{{ __('Add each position billed on this agreement. Scroll horizontally if your screen is narrow.') }}</flux:text>
                        </div>
                        <flux:button type="button" variant="primary" icon="plus" size="sm" id="add-crew-line" class="shrink-0">{{ __('Add row') }}</flux:button>
                    </div>

                    <flux:callout icon="calculator" color="blue" inline>
                        {{ __('Day: qty × daily rate × days. Month: qty × monthly rate × months. Fixed: use Manual total (rate column is unused for that row). Totals update when you save.') }}
                    </flux:callout>

                    <div class="max-h-[60vh] w-full overflow-auto rounded-xl border border-zinc-200/60 dark:border-zinc-700/60 sm:max-h-none">
                        <table class="min-w-full text-sm" id="crew-lines-table">
                            <thead class="sticky top-0 z-10 bg-white/80 shadow-sm backdrop-blur-md dark:bg-zinc-800/80">
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
                                    <tr class="border-t border-zinc-200/60 dark:border-zinc-700/60">
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

                <x-slot:footer class="justify-between">
                    <flux:button type="button" variant="ghost" icon="arrow-left" id="tab-prev-button">{{ __('Back') }}</flux:button>
                    <p class="text-center text-xs tabular-nums text-zinc-500" id="tab-step-indicator"></p>
                    <div class="flex items-center gap-2">
                        <flux:button variant="primary" type="submit" icon="check" id="tab-submit-button" class="hidden">
                            {{ $isEdit ? __('Update Agreement') : __('Save Agreement') }}
                        </flux:button>
                        <flux:button type="button" variant="primary" icon-trailing="arrow-right" id="tab-next-button">{{ __('Next') }}</flux:button>
                    </div>
                </x-slot:footer>
            </x-app-form-card>
        </form>
    </div>

    <script>
        (() => {
            const startDateInput = document.getElementById('start-date-input');
            const durationDaysInput = document.getElementById('duration-days-input');
            const endDateInput = document.getElementById('end-date-input');

            if (startDateInput && durationDaysInput && endDateInput) {
                const calculateEndDate = () => {
                    const startDateValue = startDateInput.value;
                    const durationDays = parseInt(durationDaysInput.value, 10);

                    if (!startDateValue || Number.isNaN(durationDays) || durationDays < 1) {
                        endDateInput.value = '';
                        return;
                    }

                    const startDate = new Date(`${startDateValue}T00:00:00`);
                    startDate.setDate(startDate.getDate() + Math.max(durationDays - 1, 0));

                    const year = startDate.getFullYear();
                    const month = String(startDate.getMonth() + 1).padStart(2, '0');
                    const day = String(startDate.getDate()).padStart(2, '0');

                    endDateInput.value = `${year}-${month}-${day}`;
                };

                startDateInput.addEventListener('change', calculateEndDate);
                durationDaysInput.addEventListener('change', calculateEndDate);
                durationDaysInput.addEventListener('input', calculateEndDate);
                calculateEndDate();
            }

            // Tabs Logic
            const tabs = ['details', 'crew'];
            const tabButtons = document.querySelectorAll('[data-tab-button]');
            const tabContents = document.querySelectorAll('[data-tab-content]');
            const tabPrevButton = document.getElementById('tab-prev-button');
            const tabNextButton = document.getElementById('tab-next-button');
            const tabSubmitButton = document.getElementById('tab-submit-button');
            const tabStepIndicator = document.getElementById('tab-step-indicator');
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

                if (tabPrevButton) tabPrevButton.classList.toggle('invisible', isFirst);
                if (tabNextButton) tabNextButton.classList.toggle('invisible', isLast);
                if (tabSubmitButton) tabSubmitButton.classList.toggle('hidden', !isLast);
                if (tabStepIndicator) tabStepIndicator.textContent = `Step ${currentIndex + 1} of ${tabs.length}`;
            }

            tabButtons.forEach((button) => {
                button.addEventListener('click', () => setActiveTab(button.dataset.tabButton));
            });
            setActiveTab(tabs[0]);

            if (tabPrevButton) {
                tabPrevButton.addEventListener('click', () => {
                    const currentIndex = tabs.indexOf(activeTab);
                    if (currentIndex > 0) setActiveTab(tabs[currentIndex - 1]);
                });
            }

            if (tabNextButton) {
                tabNextButton.addEventListener('click', () => {
                    const currentIndex = tabs.indexOf(activeTab);
                    if (currentIndex < tabs.length - 1) setActiveTab(tabs[currentIndex + 1]);
                });
            }

            // Crew Lines Logic
            const body = document.getElementById('crew-lines-body');
            const addButton = document.getElementById('add-crew-line');

            const inputClass = 'h-9 w-full rounded-md border border-zinc-300 px-2 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100';
            const selectRankPlaceholder = @json(__('Select rank'));
            const removeRowLabel = @json(__('Remove row'));
            const trashIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="size-4 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>`;
            const rankOptions = @json($ranks->values()->all());

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

            if (addButton && body) {
                addButton.addEventListener('click', () => {
                    const index = body.querySelectorAll('tr').length;
                    body.appendChild(createCrewLineRow(index));
                });

                body.addEventListener('click', (event) => {
                    if (!(event.target instanceof HTMLElement)) return;
                    if (event.target.matches('[data-remove-line]') || event.target.closest('[data-remove-line]')) {
                        event.target.closest('tr')?.remove();
                    }
                });

                body.addEventListener('change', (event) => {
                    const target = event.target;
                    if (!(target instanceof HTMLSelectElement) || !target.matches('[data-rank-select]')) return;

                    const selectedOption = target.selectedOptions[0];
                    if (!selectedOption) return;

                    const row = target.closest('tr');
                    if (!row) return;

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
            }
        })();
    </script>
</x-layouts::app>
