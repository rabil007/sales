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

        <form method="POST" action="{{ $isEdit ? route('client-agreements.update', $agreement) : route('client-agreements.store') }}" class="space-y-0" enctype="multipart/form-data">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <x-app-form-card class="!p-0">
                {{-- Tab Navigation --}}
                <div class="overflow-x-auto border-b border-zinc-200/50 bg-white/40 dark:border-zinc-800/60 dark:bg-zinc-900/40">
                    <div class="flex min-w-min gap-2 px-3 pt-2.5 sm:px-6 text-sm">
                        <button type="button" data-tab-button="details" aria-selected="true" class="tab-button inline-flex shrink-0 items-center gap-2 rounded-t-xl px-4 py-3 font-semibold transition-all duration-200 border-b-2 border-transparent">
                            <flux:icon.document-text class="size-4.5" />
                            <span class="sm:hidden">{{ __('Details') }}</span>
                            <span class="hidden sm:inline">{{ __('Agreement Details') }}</span>
                        </button>
                        <button type="button" data-tab-button="crew" aria-selected="false" class="tab-button inline-flex shrink-0 items-center gap-2 rounded-t-xl px-4 py-3 font-semibold transition-all duration-200 border-b-2 border-transparent">
                            <flux:icon.user-group class="size-4.5" />
                            <span class="sm:hidden">{{ __('Crew') }}</span>
                            <span class="hidden sm:inline">{{ __('Crew Lines') }}</span>
                        </button>
                    </div>
                </div>

                {{-- Tab: Agreement Details --}}
                <fieldset data-tab-content="details" class="tab-content min-w-0 p-6 space-y-6">
                    <div>
                        <flux:heading size="md" class="mb-4 font-bold text-zinc-850 dark:text-zinc-150">{{ __('Agreement Details') }}</flux:heading>
                        <div class="grid gap-5 md:grid-cols-2">
                            <flux:field>
                                <flux:label class="font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Client Name') }} <span class="text-red-500 dark:text-red-400">*</span></flux:label>
                                <flux:select name="client_id" required class="font-semibold">
                                    <option value="">{{ __('Select client') }}</option>
                                    @foreach ($clients as $clientOption)
                                        <option value="{{ $clientOption->id }}" @selected((string) old('client_id', $agreement->client_id) === (string) $clientOption->id)>
                                            {{ $clientOption->name }}
                                        </option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="client_id" />
                            </flux:field>
                            <flux:field>
                                <flux:label class="font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Agreement Ref.') }} <span class="text-red-500 dark:text-red-400">*</span></flux:label>
                                <flux:input name="agreement_ref" placeholder="e.g. OMS-AGR-2026-001" :value="old('agreement_ref', $agreement->agreement_ref)" required class="font-semibold" />
                                <flux:error name="agreement_ref" />
                            </flux:field>
                            <flux:textarea name="scope_of_work" label="{{ __('Scope of Work') }}" placeholder="Describe the scope of work included in this agreement..." rows="4" class="md:col-span-2 font-medium" id="scope_of_work">{{ old('scope_of_work', $agreement->scope_of_work) }}</flux:textarea>
                        </div>

                        @if ($clients->isEmpty())
                            <flux:text class="mt-3.5 text-sm text-zinc-550">
                                {{ __('No clients available yet.') }}
                                <flux:link :href="route('clients.create')" wire:navigate class="font-bold text-blue-600 dark:text-blue-400">{{ __('Create a client') }}</flux:link>
                                {{ __('before adding an agreement.') }}
                            </flux:text>
                        @endif
                    </div>

                    <flux:separator class="dark:bg-zinc-800/80" />

                    {{-- Contract Period --}}
                    <div>
                        <flux:heading size="md" class="mb-4 font-bold text-zinc-855 dark:text-zinc-150">{{ __('Contract Period') }}</flux:heading>
                        <div class="grid gap-5 md:grid-cols-3">
                            <flux:field>
                                <flux:label class="font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Duration (days)') }} <span class="text-red-500 dark:text-red-400">*</span></flux:label>
                                <flux:input name="duration_days" id="duration-days-input" type="number" min="1" step="1" placeholder="e.g. 30" :value="old('duration_days', $agreement->duration_days)" required class="font-bold" />
                                <flux:error name="duration_days" />
                            </flux:field>
                            <flux:field>
                                <flux:label class="font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Start Date') }} <span class="text-red-500 dark:text-red-400">*</span></flux:label>
                                <flux:input name="start_date" id="start-date-input" type="date" :value="old('start_date', optional($agreement->start_date)->toDateString())" required class="font-semibold" />
                                <flux:error name="start_date" />
                            </flux:field>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <flux:label class="font-semibold text-zinc-700 dark:text-zinc-300">{{ __('End Date') }}</flux:label>
                                    <flux:badge size="sm" color="emerald" class="font-bold tracking-wide uppercase text-[9px]">{{ __('Auto-calculated') }}</flux:badge>
                                </div>
                                <flux:input name="end_date" id="end-date-input" type="date" :value="old('end_date', optional($agreement->end_date)->toDateString())" readonly class="font-bold bg-emerald-500/5 dark:bg-emerald-500/5 border-emerald-500/20 text-emerald-600 dark:text-emerald-400" />
                            </div>
                        </div>
                    </div>

                    <flux:separator class="dark:bg-zinc-800/80" />

                    {{-- Billing --}}
                    <div>
                        <flux:heading size="md" class="mb-4 font-bold text-zinc-855 dark:text-zinc-150">{{ __('Billing') }}</flux:heading>
                        <div class="grid gap-5 md:grid-cols-2">
                            <flux:field>
                                <flux:label class="font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Monthly Invoice Value (USD)') }} <span class="text-red-500 dark:text-red-400">*</span></flux:label>
                                <flux:input name="monthly_invoice_value" type="number" min="0" step="0.01" placeholder="e.g. 12500.00" icon="currency-dollar" :value="old('monthly_invoice_value', $agreement->monthly_invoice_value)" required class="font-bold text-emerald-600 dark:text-emerald-400" />
                                <flux:error name="monthly_invoice_value" />
                            </flux:field>
                        </div>
                    </div>

                    <flux:separator class="dark:bg-zinc-800/80" />

                    {{-- Document Upload --}}
                    <div>
                        <flux:heading size="md" class="mb-4 font-bold text-zinc-855 dark:text-zinc-150">{{ __('Signed Agreement (Optional)') }}</flux:heading>
                        <flux:text class="mb-4 text-sm text-zinc-550 dark:text-zinc-400 leading-relaxed">
                            {{ __('Upload a scanned copy or digital version of the signed agreement. Acceptable formats include PDF, JPG, or PNG. Maximum file size is 10MB.') }}
                        </flux:text>
                        
                        <div 
                            id="document-dropzone"
                            class="group relative flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-zinc-200 bg-zinc-50/30 py-12 transition-all duration-200 hover:border-blue-500 hover:bg-blue-50/20 dark:border-zinc-800 dark:bg-zinc-900/10 dark:hover:border-blue-400 dark:hover:bg-blue-900/10"
                        >
                            <input 
                                type="file" 
                                name="document" 
                                id="document-input" 
                                class="absolute inset-0 z-50 h-full w-full cursor-pointer opacity-0"
                                accept=".pdf,image/jpeg,image/png,image/jpg"
                            />
                            
                            <div class="pointer-events-none flex flex-col items-center gap-4 text-center">
                                <div class="rounded-xl bg-white p-4 shadow-xs ring-1 ring-zinc-200/50 transition-all duration-200 group-hover:-translate-y-1 group-hover:scale-105 group-hover:bg-blue-50/50 group-hover:shadow-md group-hover:ring-blue-200/50 dark:bg-zinc-900 dark:ring-zinc-800 dark:group-hover:bg-zinc-850 dark:group-hover:ring-blue-500/30">
                                    <flux:icon.arrow-up-tray class="size-8 text-zinc-400 transition-colors group-hover:text-blue-600 dark:text-zinc-500 dark:group-hover:text-blue-400" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                                        <span class="text-blue-600 dark:text-blue-400">{{ __('Click to upload') }}</span> {{ __('or drag and drop') }}
                                    </p>
                                    <p class="mt-1.5 text-xs text-zinc-500 dark:text-zinc-400 font-medium" id="document-filename">
                                        {{ __('PDF, PNG, JPG up to 10MB') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if ($agreement->document_path)
                            <div class="mt-4 flex items-center justify-between rounded-xl border border-zinc-200/50 bg-white/40 p-4 dark:border-zinc-800/80 dark:bg-zinc-900/40">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                        <flux:icon.document-check class="size-6" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ __('Current Document Uploaded') }}</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-450 mt-0.5 truncate max-w-xs sm:max-w-md">{{ basename($agreement->document_path) }}</p>
                                    </div>
                                </div>
                                <flux:button size="sm" variant="ghost" icon="arrow-top-right-on-square" :href="\Illuminate\Support\Facades\Storage::disk('public')->url($agreement->document_path)" target="_blank" class="hover:bg-zinc-150 dark:hover:bg-zinc-800">{{ __('View File') }}</flux:button>
                            </div>
                        @endif
                    </div>
                </fieldset>

                {{-- Tab: Crew Lines --}}
                <fieldset data-tab-content="crew" class="tab-content hidden min-w-0 space-y-4 p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0 space-y-1">
                            <flux:heading size="md" class="font-bold text-zinc-850 dark:text-zinc-150">{{ __('Crew Lines') }}</flux:heading>
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Add each position billed on this agreement. Scroll horizontally if your screen is narrow.') }}</flux:text>
                        </div>
                        <flux:button type="button" variant="primary" icon="plus" size="sm" id="add-crew-line" class="shrink-0">{{ __('Add row') }}</flux:button>
                    </div>

                    <flux:callout icon="calculator" color="blue" inline class="text-sm font-medium">
                        {{ __('Day: qty × daily rate × days. Month: qty × monthly rate × months. Fixed: use Manual total (rate column is unused for that row). Totals update when you save.') }}
                    </flux:callout>

                    <div class="max-h-[60vh] w-full overflow-auto rounded-xl border border-zinc-200/50 bg-white/30 dark:border-zinc-800/80 dark:bg-zinc-900/20 sm:max-h-none">
                        <table class="min-w-full text-sm" id="crew-lines-table">
                            <thead class="sticky top-0 z-10 bg-zinc-50/80 dark:bg-zinc-900/80 shadow-xs backdrop-blur-xs">
                                <tr class="text-left text-xs font-bold uppercase tracking-wider text-zinc-450 dark:text-zinc-550 border-b border-zinc-200/50 dark:border-zinc-800/80">
                                    <th class="px-3 py-3">{{ __('Rank') }}</th>
                                    <th class="px-3 py-3">{{ __('Category') }}</th>
                                    <th class="px-3 py-3 text-center">{{ __('Qty') }}</th>
                                    <th class="px-3 py-3">{{ __('Basis') }}</th>
                                    <th class="px-3 py-3 text-right">{{ __('Rate') }}</th>
                                    <th class="px-3 py-3 text-right">{{ __('Monthly Rate') }}</th>
                                    <th class="px-3 py-3 text-center">{{ __('Days') }}</th>
                                    <th class="px-3 py-3 text-center">{{ __('Months') }}</th>
                                    <th class="px-3 py-3 text-right">{{ __('Manual') }}</th>
                                    <th class="px-3 py-3 text-right">{{ __('OT') }}</th>
                                    <th class="px-3 py-3">{{ __('Mob') }}</th>
                                    <th class="px-3 py-3">{{ __('Demob') }}</th>
                                    <th class="px-3 py-3">{{ __('Remarks') }}</th>
                                    <th class="px-3 py-3"></th>
                                </tr>
                            </thead>
                            <tbody id="crew-lines-body" class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                                @php
                                    $oldCrewLines = old('crew_lines');
                                    $initialCrewLines = is_array($oldCrewLines) ? $oldCrewLines : ($crewLines ?: [['category' => 'Marine', 'qty' => 1, 'basis' => 'Day', 'rate' => 0, 'duration' => 0, 'ot_rate' => 0]]);
                                @endphp
                                @foreach ($initialCrewLines as $index => $line)
                                    @php
                                        $basisRaw = $line['basis'] ?? 'Day';
                                        $basisValue = in_array($basisRaw, ['Day', 'Month', 'Fixed'], true) ? $basisRaw : 'Day';
                                    @endphp
                                    <tr class="hover:bg-zinc-50/30 dark:hover:bg-zinc-950/10">
                                        <td class="p-2">
                                            <select class="h-9.5 w-full min-w-[150px] rounded-lg border border-zinc-200 bg-zinc-50/50 px-2.5 text-sm font-semibold transition-all duration-150 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 focus:outline-hidden dark:border-zinc-800/80 dark:bg-zinc-900/60 dark:text-zinc-100 dark:focus:border-blue-500 dark:focus:bg-zinc-950/60" name="crew_lines[{{ $index }}][rank]" data-rank-select>
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
                                        <td class="p-2"><input class="h-9.5 w-full min-w-[100px] rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm font-semibold transition-all duration-150 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 focus:outline-hidden dark:border-zinc-800/80 dark:bg-zinc-900/60 dark:text-zinc-100 dark:focus:border-blue-500 dark:focus:bg-zinc-950/60" name="crew_lines[{{ $index }}][category]" value="{{ $line['category'] ?? 'Marine' }}" placeholder="Marine" autocomplete="off" /></td>
                                        <td class="p-2"><input class="h-9.5 w-18 text-center rounded-lg border border-zinc-200 bg-zinc-50/50 px-2 text-sm font-bold transition-all duration-150 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 focus:outline-hidden dark:border-zinc-800/80 dark:bg-zinc-900/60 dark:text-zinc-100 dark:focus:border-blue-500 dark:focus:bg-zinc-950/60" type="number" min="1" name="crew_lines[{{ $index }}][qty]" value="{{ $line['qty'] ?? 1 }}" placeholder="1" /></td>
                                        <td class="p-2">
                                            <select class="h-9.5 w-26 shrink-0 rounded-lg border border-zinc-200 bg-zinc-50/50 px-2.5 text-sm font-bold transition-all duration-150 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 focus:outline-hidden dark:border-zinc-800/80 dark:bg-zinc-900/60 dark:text-zinc-100 dark:focus:border-blue-500 dark:focus:bg-zinc-950/60" name="crew_lines[{{ $index }}][basis]" data-basis-select>
                                                @foreach (['Day', 'Month', 'Fixed'] as $basisOption)
                                                    <option value="{{ $basisOption }}" @selected($basisValue === $basisOption)>{{ $basisOption }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="p-2"><input class="h-9.5 w-26 text-right tabular-nums rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm font-semibold transition-all duration-150 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 focus:outline-hidden dark:border-zinc-800/80 dark:bg-zinc-900/60 dark:text-zinc-100 dark:focus:border-blue-500 dark:focus:bg-zinc-950/60" type="number" step="0.01" min="0" name="crew_lines[{{ $index }}][rate]" value="{{ $line['rate'] ?? 0 }}" placeholder="0.00" /></td>
                                        <td class="p-2"><input class="h-9.5 w-26 text-right tabular-nums rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm font-semibold transition-all duration-150 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 focus:outline-hidden dark:border-zinc-800/80 dark:bg-zinc-900/60 dark:text-zinc-100 dark:focus:border-blue-500 dark:focus:bg-zinc-950/60" type="number" step="0.01" min="0" name="crew_lines[{{ $index }}][monthly_rate]" value="{{ $line['monthly_rate'] ?? 0 }}" placeholder="0.00" /></td>
                                        <td class="p-2"><input class="h-9.5 w-20 text-center rounded-lg border border-zinc-200 bg-zinc-50/50 px-2 text-sm font-bold transition-all duration-150 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 focus:outline-hidden dark:border-zinc-800/80 dark:bg-zinc-900/60 dark:text-zinc-100 dark:focus:border-blue-500 dark:focus:bg-zinc-950/60" type="number" min="0" name="crew_lines[{{ $index }}][duration_days]" value="{{ $line['duration_days'] ?? $line['duration'] ?? 0 }}" placeholder="0" /></td>
                                        <td class="p-2"><input class="h-9.5 w-20 text-center rounded-lg border border-zinc-200 bg-zinc-50/50 px-2 text-sm font-bold transition-all duration-150 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 focus:outline-hidden dark:border-zinc-800/80 dark:bg-zinc-900/60 dark:text-zinc-100 dark:focus:border-blue-500 dark:focus:bg-zinc-950/60" type="number" min="0" name="crew_lines[{{ $index }}][duration_months]" value="{{ $line['duration_months'] ?? 0 }}" placeholder="0" /></td>
                                        <td class="p-2"><input class="h-9.5 w-26 text-right tabular-nums rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm font-semibold transition-all duration-150 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 focus:outline-hidden dark:border-zinc-800/80 dark:bg-zinc-900/60 dark:text-zinc-100 dark:focus:border-blue-500 dark:focus:bg-zinc-950/60" type="number" step="0.01" min="0" name="crew_lines[{{ $index }}][manual_total]" value="{{ $line['manual_total'] ?? 0 }}" placeholder="0.00" /></td>
                                        <td class="p-2"><input class="h-9.5 w-26 text-right tabular-nums rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm font-semibold transition-all duration-150 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 focus:outline-hidden dark:border-zinc-800/80 dark:bg-zinc-900/60 dark:text-zinc-100 dark:focus:border-blue-500 dark:focus:bg-zinc-950/60" type="number" step="0.01" min="0" name="crew_lines[{{ $index }}][ot_rate]" value="{{ $line['ot_rate'] ?? 0 }}" placeholder="0.00" /></td>
                                        <td class="p-2"><input class="h-9.5 w-34 rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm font-semibold transition-all duration-150 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 focus:outline-hidden dark:border-zinc-800/80 dark:bg-zinc-900/60 dark:text-zinc-100 dark:focus:border-blue-500 dark:focus:bg-zinc-950/60" type="date" name="crew_lines[{{ $index }}][mob_date]" value="{{ !empty($line['mob_date']) ? \Illuminate\Support\Carbon::parse($line['mob_date'])->format('Y-m-d') : '' }}" placeholder="Select date" /></td>
                                        <td class="p-2"><input class="h-9.5 w-34 rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm font-semibold transition-all duration-150 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 focus:outline-hidden dark:border-zinc-800/80 dark:bg-zinc-900/60 dark:text-zinc-100 dark:focus:border-blue-500 dark:focus:bg-zinc-950/60" type="date" name="crew_lines[{{ $index }}][demob_date]" value="{{ !empty($line['demob_date']) ? \Illuminate\Support\Carbon::parse($line['demob_date'])->format('Y-m-d') : '' }}" placeholder="Select date" /></td>
                                        <td class="p-2"><input class="h-9.5 w-full min-w-[120px] rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm font-medium transition-all duration-150 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 focus:outline-hidden dark:border-zinc-800/80 dark:bg-zinc-900/60 dark:text-zinc-100 dark:focus:border-blue-500 dark:focus:bg-zinc-950/60" name="crew_lines[{{ $index }}][remarks]" value="{{ $line['remarks'] ?? '' }}" placeholder="Optional notes" autocomplete="off" /></td>
                                        <td class="p-2 text-right">
                                            <flux:tooltip content="{{ __('Remove row') }}">
                                                <flux:button type="button" variant="ghost" icon="trash" size="sm" class="size-9.5! hover:bg-rose-50/50 hover:text-rose-600 dark:hover:bg-rose-950/20 dark:hover:text-rose-400" data-remove-line />
                                            </flux:tooltip>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </fieldset>

                <x-slot:footer class="justify-between">
                    <flux:button type="button" variant="ghost" icon="arrow-left" id="tab-prev-button" class="hover:bg-zinc-150 dark:hover:bg-zinc-800">{{ __('Back') }}</flux:button>
                    <p class="text-center text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400" id="tab-step-indicator"></p>
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
                    button.classList.toggle('border-blue-500', isActive);
                    button.classList.toggle('dark:border-blue-400', isActive);
                    button.classList.toggle('text-blue-600', isActive);
                    button.classList.toggle('dark:text-blue-400', isActive);
                    button.classList.toggle('text-zinc-550', !isActive);
                    button.classList.toggle('dark:text-zinc-400', !isActive);
                    button.classList.toggle('hover:text-zinc-800', !isActive);
                    button.classList.toggle('dark:hover:text-zinc-200', !isActive);
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

            const inputClass = 'h-9.5 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 text-sm font-semibold transition-all duration-150 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 focus:outline-hidden dark:border-zinc-800/80 dark:bg-zinc-900/60 dark:text-zinc-100 dark:focus:border-blue-500 dark:focus:bg-zinc-950/60';
            const selectClass = 'h-9.5 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-2.5 text-sm font-semibold transition-all duration-150 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 focus:outline-hidden dark:border-zinc-800/80 dark:bg-zinc-900/60 dark:text-zinc-100 dark:focus:border-blue-500 dark:focus:bg-zinc-950/60';
            const selectRankPlaceholder = @json(__('Select rank'));
            const removeRowLabel = @json(__('Remove row'));
            const trashIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="size-4 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>`;
            const rankOptions = @json($ranks->values()->all());

            function buildRankSelect(index, selected = '') {
                const options = rankOptions.map((rank) => {
                    const isSelected = rank.name === selected ? 'selected' : '';
                    return `<option value="${rank.name}" data-category="${rank.category}" data-basis="${rank.default_basis}" data-rate="${rank.default_rate}" ${isSelected}>${rank.name}</option>`;
                }).join('');

                return `<select class="${selectClass} min-w-[150px]" name="crew_lines[${index}][rank]" data-rank-select><option value="">${selectRankPlaceholder}</option>${options}</select>`;
            }

            function buildBasisSelect(index, selected = 'Day') {
                const normalized = ['Day', 'Month', 'Fixed'].includes(selected) ? selected : 'Day';
                const options = ['Day', 'Month', 'Fixed'].map((b) =>
                    `<option value="${b}" ${b === normalized ? 'selected' : ''}>${b}</option>`
                ).join('');

                return `<select class="${selectClass} w-26 shrink-0" name="crew_lines[${index}][basis]" data-basis-select>${options}</select>`;
            }

            function createCrewLineRow(index) {
                const row = document.createElement('tr');
                row.className = 'border-t border-zinc-200 dark:border-zinc-700/50';
                row.innerHTML = `
                    <td class="p-2">${buildRankSelect(index)}</td>
                    <td class="p-2"><input class="${inputClass} min-w-[100px]" name="crew_lines[${index}][category]" value="Marine" placeholder="Marine" /></td>
                    <td class="p-2"><input class="${inputClass} w-18 text-center font-bold" type="number" min="1" name="crew_lines[${index}][qty]" value="1" placeholder="1" /></td>
                    <td class="p-2">${buildBasisSelect(index)}</td>
                    <td class="p-2"><input class="${inputClass} w-26 text-right tabular-nums" type="number" step="0.01" min="0" name="crew_lines[${index}][rate]" value="0" placeholder="0.00" /></td>
                    <td class="p-2"><input class="${inputClass} w-26 text-right tabular-nums" type="number" step="0.01" min="0" name="crew_lines[${index}][monthly_rate]" value="0" placeholder="0.00" /></td>
                    <td class="p-2"><input class="${inputClass} w-20 text-center font-bold" type="number" min="0" name="crew_lines[${index}][duration_days]" value="0" placeholder="0" /></td>
                    <td class="p-2"><input class="${inputClass} w-20 text-center font-bold" type="number" min="0" name="crew_lines[${index}][duration_months]" value="0" placeholder="0" /></td>
                    <td class="p-2"><input class="${inputClass} w-26 text-right tabular-nums" type="number" step="0.01" min="0" name="crew_lines[${index}][manual_total]" value="0" placeholder="0.00" /></td>
                    <td class="p-2"><input class="${inputClass} w-26 text-right tabular-nums" type="number" step="0.01" min="0" name="crew_lines[${index}][ot_rate]" value="0" placeholder="0.00" /></td>
                    <td class="p-2"><input class="${inputClass} w-34" type="date" name="crew_lines[${index}][mob_date]" placeholder="Select date" /></td>
                    <td class="p-2"><input class="${inputClass} w-34" type="date" name="crew_lines[${index}][demob_date]" placeholder="Select date" /></td>
                    <td class="p-2"><input class="${inputClass} min-w-[120px]" name="crew_lines[${index}][remarks]" placeholder="Optional notes" /></td>
                    <td class="p-2 text-right">
                        <button type="button" class="inline-flex size-9.5 items-center justify-center rounded-lg text-zinc-500 transition-colors hover:bg-rose-50/50 hover:text-rose-600 dark:hover:bg-rose-950/20 dark:hover:text-rose-400" data-remove-line aria-label="${removeRowLabel}">
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

            // File Upload Drag & Drop Logic
            const documentDropzone = document.getElementById('document-dropzone');
            const documentInput = document.getElementById('document-input');
            const documentFilename = document.getElementById('document-filename');

            if (documentDropzone && documentInput && documentFilename) {
                const updateFilename = (file) => {
                    if (file) {
                        documentFilename.innerHTML = `<span class="font-medium text-emerald-600 dark:text-emerald-400">${file.name}</span>`;
                        documentDropzone.classList.add('border-emerald-500', 'dark:border-emerald-500');
                        documentDropzone.classList.remove('border-zinc-300', 'dark:border-zinc-700');
                    } else {
                        documentFilename.textContent = @json(__('PDF, PNG, JPG up to 10MB'));
                        documentDropzone.classList.remove('border-emerald-500', 'dark:border-emerald-500');
                        documentDropzone.classList.add('border-zinc-300', 'dark:border-zinc-700');
                    }
                };

                documentInput.addEventListener('change', () => {
                    updateFilename(documentInput.files?.[0]);
                });

                // Visual feedback for drag events
                ['dragenter', 'dragover'].forEach(eventName => {
                    documentDropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        documentDropzone.classList.add('border-blue-500', 'dark:border-blue-400');
                        documentDropzone.classList.remove('border-zinc-300', 'dark:border-zinc-700');
                    });
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    documentDropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        
                        if (eventName === 'drop' && e.dataTransfer.files.length) {
                            documentInput.files = e.dataTransfer.files;
                            updateFilename(documentInput.files[0]);
                        }
                        
                        if (!documentInput.files?.length) {
                            documentDropzone.classList.remove('border-blue-500', 'dark:border-blue-400');
                            documentDropzone.classList.add('border-zinc-300', 'dark:border-zinc-700');
                        }
                    });
                });
            }
        })();
    </script>
</x-layouts::app>
