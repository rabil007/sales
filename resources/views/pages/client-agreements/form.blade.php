<x-layouts::app :title="$isEdit ? __('Edit Client Agreement') : __('New Client Agreement')">
    <div class="mx-auto max-w-4xl space-y-8">
        @if ($errors->any())
            <flux:callout icon="exclamation-triangle" color="red" class="rounded-2xl">Please review agreement details and try again.</flux:callout>
        @endif

        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <flux:heading size="xl" class="font-bold tracking-tight">{{ $isEdit ? 'Edit Client Agreement' : 'Create Client Agreement' }}</flux:heading>
                <flux:text class="mt-1 text-zinc-500 dark:text-zinc-400">
                    {{ $isEdit ? 'Update agreement details and contract period.' : 'Add a new client agreement with auto-calculated end date.' }}
                </flux:text>
            </div>
            <flux:button variant="ghost" icon="arrow-left" :href="route('client-agreements.index')" wire:navigate class="rounded-full transition-transform hover:-translate-x-0.5">Back to list</flux:button>
        </div>

        <form method="POST" action="{{ $isEdit ? route('client-agreements.update', $agreement) : route('client-agreements.store') }}" class="space-y-8 rounded-3xl border border-zinc-200/60 bg-white/60 p-8 shadow-sm backdrop-blur-xl dark:border-zinc-700/60 dark:bg-zinc-900/60">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <flux:callout icon="information-circle" color="blue" class="rounded-2xl">
                End date is calculated automatically from start date and duration using an inclusive period (e.g. 30 days starting Jun 1 ends Jun 30).
            </flux:callout>

            <div>
                <h3 class="mb-4 text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">Agreement Details</h3>
                <div class="grid gap-6 md:grid-cols-2">
                    <flux:select name="client_id" label="Client Name" required>
                        <option value="">Select client</option>
                        @foreach ($clients as $clientOption)
                            <option value="{{ $clientOption->id }}" @selected((string) old('client_id', $agreement->client_id) === (string) $clientOption->id)>
                                {{ $clientOption->name }}
                            </option>
                        @endforeach
                    </flux:select>
                    <flux:input name="agreement_ref" label="Agreement Ref." placeholder="e.g. OMS-AGR-2026-001" :value="old('agreement_ref', $agreement->agreement_ref)" required />
                    <flux:textarea name="scope_of_work" label="Scope of Work" placeholder="Describe the scope of work included in this agreement..." rows="4" class="md:col-span-2">{{ old('scope_of_work', $agreement->scope_of_work) }}</flux:textarea>
                </div>

                @if ($clients->isEmpty())
                    <flux:text class="mt-3 text-sm text-zinc-500">
                        No clients available yet.
                        <flux:link :href="route('clients.create')" wire:navigate class="font-medium">Create a client</flux:link>
                        before adding an agreement.
                    </flux:text>
                @endif
            </div>

            <flux:separator class="border-zinc-200/60 dark:border-zinc-700/60" />

            <div>
                <h3 class="mb-4 text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">Contract Period</h3>
                <div class="rounded-2xl border border-zinc-200/60 bg-zinc-50/50 p-6 dark:border-zinc-700/60 dark:bg-zinc-800/20">
                    <div class="grid gap-6 md:grid-cols-3">
                        <flux:input name="duration_days" id="duration-days-input" type="number" min="1" step="1" label="Duration (days)" placeholder="e.g. 30" :value="old('duration_days', $agreement->duration_days)" required />
                        <flux:input name="start_date" id="start-date-input" type="date" label="Start Date" :value="old('start_date', optional($agreement->start_date)->toDateString())" required />
                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <flux:label>End Date</flux:label>
                                <flux:badge size="sm" color="emerald">Auto-calculated</flux:badge>
                            </div>
                            <flux:input name="end_date" id="end-date-input" type="date" :value="old('end_date', optional($agreement->end_date)->toDateString())" readonly class="bg-emerald-50/50 dark:bg-emerald-950/20" />
                        </div>
                    </div>
                    <flux:text class="mt-4 text-xs text-zinc-500 dark:text-zinc-400">
                        Adjust duration or start date to update the end date instantly.
                    </flux:text>
                </div>
            </div>

            <flux:separator class="border-zinc-200/60 dark:border-zinc-700/60" />

            <div>
                <h3 class="mb-4 text-xs font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">Billing</h3>
                <div class="grid gap-6 md:grid-cols-2">
                    <flux:input name="monthly_invoice_value" type="number" min="0" step="0.01" label="Monthly Invoice Value (USD)" placeholder="e.g. 12500.00" icon="currency-dollar" :value="old('monthly_invoice_value', $agreement->monthly_invoice_value)" required />
                </div>
            </div>

            <flux:separator class="border-zinc-200/60 dark:border-zinc-700/60" />

            <div class="flex flex-wrap items-center justify-end gap-3">
                <flux:button variant="ghost" :href="route('client-agreements.index')" wire:navigate class="rounded-full px-5">Cancel</flux:button>
                <flux:button variant="primary" type="submit" icon="check" class="rounded-full px-6 transition-transform hover:-translate-y-0.5 hover:shadow-md">
                    {{ $isEdit ? 'Update Agreement' : 'Save Agreement' }}
                </flux:button>
            </div>
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
