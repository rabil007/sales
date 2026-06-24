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
                <flux:text class="text-zinc-500">{{ $isEdit ? __('Update agreement details and contract period.') : __('Add a new client agreement with auto-calculated end date.') }}</flux:text>
            </div>
            <flux:button variant="ghost" icon="arrow-left" :href="route('client-agreements.index')" wire:navigate>{{ __('Back to list') }}</flux:button>
        </div>

        @if (! $isEdit)
            <flux:callout icon="information-circle" color="zinc" inline>
                {{ __('End date is calculated automatically from start date and duration using an inclusive period (e.g. 30 days starting Jun 1 ends Jun 30).') }}
            </flux:callout>
        @endif

        <form method="POST" action="{{ $isEdit ? route('client-agreements.update', $agreement) : route('client-agreements.store') }}" class="space-y-0">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <x-app-form-card>
                <div class="p-6 space-y-6">
                    {{-- Agreement Details --}}
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
                </div>

                <x-slot:footer class="justify-end">
                    <flux:button variant="ghost" :href="route('client-agreements.index')" wire:navigate>{{ __('Cancel') }}</flux:button>
                    <flux:button variant="primary" type="submit" icon="check">
                        {{ $isEdit ? __('Update Agreement') : __('Save Agreement') }}
                    </flux:button>
                </x-slot:footer>
            </x-app-form-card>
        </form>
    </div>

    <script>
        (() => {
            const startDateInput = document.getElementById('start-date-input');
            const durationDaysInput = document.getElementById('duration-days-input');
            const endDateInput = document.getElementById('end-date-input');

            if (!startDateInput || !durationDaysInput || !endDateInput) {
                return;
            }

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
        })();
    </script>
</x-layouts::app>
