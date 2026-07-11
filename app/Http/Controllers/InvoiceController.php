<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Quote;
use App\Support\Invoices\InvoiceDocumentNumberGenerator;
use App\Support\Invoices\InvoiceTotalsCalculator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    private const LOCKED_STATUSES = ['Paid', 'Cancelled'];

    public function __construct(
        private InvoiceDocumentNumberGenerator $documentNumbers,
        private InvoiceTotalsCalculator $totalsCalculator,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Invoice::class);

        $status = $request->string('status')->toString();
        $q = $request->string('q')->trim()->toString();
        $perPage = (int) $request->integer('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        return view('pages.invoices.index', [
            'status' => $status,
            'q' => $q,
            'perPage' => $perPage,
            'stats' => [
                'total' => Invoice::query()->count(),
                'draft' => Invoice::query()->where('status', 'Draft')->count(),
                'issued' => Invoice::query()->where('status', 'Issued')->count(),
                'paid' => Invoice::query()->where('status', 'Paid')->count(),
                'overdue' => Invoice::query()->where('status', 'Overdue')->count(),
            ],
            'invoices' => Invoice::query()
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($q !== '', fn ($query) => $query->where(function ($query) use ($q): void {
                    $query->where('client_name', 'like', "%{$q}%")
                        ->orWhere('doc_no', 'like', "%{$q}%")
                        ->orWhere('project_name', 'like', "%{$q}%");
                }))
                ->with(['client:id,name', 'quote:id,doc_no'])
                ->latest('id')
                ->paginate($perPage)
                ->withQueryString(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Invoice::class);

        $quote = null;
        $items = [];
        $clientName = '';
        $clientId = null;
        $vessel = '';
        $location = '';
        $projectName = '';
        $currency = 'AED';
        $subtotal = 0.00;
        $totalAmount = 0.00;

        if ($request->filled('quote_id')) {
            $quote = Quote::query()->with('crewLines')->find((int) $request->input('quote_id'));
            if ($quote) {
                $clientId = $quote->client_id;
                $clientName = $quote->client_name;
                $vessel = (string) $quote->vessel;
                $location = (string) $quote->location;
                $projectName = (string) $quote->project_name;
                $currency = $quote->currency;
                $subtotal = $quote->total_amount;
                $totalAmount = $quote->total_amount;

                foreach ($quote->crewLines as $line) {
                    $duration = $line->duration_days > 0 ? $line->duration_days : ($line->duration > 0 ? $line->duration : 1);
                    $items[] = [
                        'description' => $line->rank ?? 'Service Item',
                        'category' => $line->category ?? 'Marine',
                        'qty' => $line->qty,
                        'basis' => $line->basis,
                        'rate' => $line->rate,
                        'duration' => $duration,
                        'duration_unit' => $line->duration_days > 0 ? 'Days' : ($line->duration_months > 0 ? 'Months' : $line->basis),
                        'line_total' => $line->line_total > 0 ? $line->line_total : round($line->qty * $line->rate * $duration, 2),
                    ];
                }
            }
        }

        if (empty($items)) {
            $items[] = [
                'description' => '',
                'category' => 'Marine',
                'qty' => 1,
                'basis' => 'Day',
                'rate' => 0.00,
                'duration' => 1,
                'duration_unit' => 'Days',
                'line_total' => 0.00,
            ];
        }

        return view('pages.invoices.form', [
            'invoice' => new Invoice([
                'doc_no' => $this->documentNumbers->next(),
                'quote_id' => $quote?->id,
                'client_id' => $clientId,
                'client_name' => $clientName,
                'vessel' => $vessel,
                'location' => $location,
                'project_name' => $projectName,
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'status' => 'Draft',
                'currency' => $currency,
                'subtotal' => $subtotal,
                'tax_rate' => 0.00,
                'tax_amount' => 0.00,
                'total_amount' => $totalAmount,
            ]),
            'items' => $items,
            'clients' => Client::query()->orderBy('name')->get(['id', 'name']),
            'isEdit' => false,
            'isLocked' => false,
        ]);
    }

    public function store(InvoiceRequest $request): RedirectResponse
    {
        $this->authorize('create', Invoice::class);

        DB::transaction(function () use ($request): void {
            $validated = $request->validated();
            $items = $this->totalsCalculator->normalizeItems($validated['items'] ?? []);
            $client = $this->resolveClient($validated);

            $subtotal = $this->totalsCalculator->subtotal($items);
            $taxRate = (float) ($validated['tax_rate'] ?? 0);
            $taxAmount = $this->totalsCalculator->taxAmount($subtotal, $taxRate);
            $totalAmount = $this->totalsCalculator->total($subtotal, $taxAmount);

            $invoice = Invoice::query()->create([
                ...collect($validated)->except('items')->all(),
                'client_id' => $client?->id,
                'client_name' => $client?->name ?? $validated['client_name'],
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
            ]);

            $invoice->items()->createMany($items);
        });

        return redirect()->route('invoices.index')->with('status', 'Invoice created.');
    }

    public function show(Invoice $invoice): View
    {
        $this->authorize('view', $invoice);

        $invoice->load(['items', 'client', 'quote']);

        return view('pages.invoices.show', [
            'invoice' => $invoice,
            'isLocked' => $this->isLocked($invoice),
        ]);
    }

    public function edit(Invoice $invoice): View|RedirectResponse
    {
        $this->authorize('update', $invoice);

        if ($this->isLocked($invoice)) {
            return redirect()->route('invoices.index')->with('status', 'Paid or cancelled invoices cannot be edited.');
        }

        $invoice->refresh()->load(['items', 'client']);

        return view('pages.invoices.form', [
            'invoice' => $invoice,
            'items' => $invoice->items->toArray(),
            'clients' => Client::query()->orderBy('name')->get(['id', 'name']),
            'isEdit' => true,
            'isLocked' => $this->isLocked($invoice),
        ]);
    }

    public function update(InvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);

        if ($this->isLocked($invoice)) {
            return redirect()->route('invoices.index')->with('status', 'Paid or cancelled invoices cannot be edited.');
        }

        DB::transaction(function () use ($request, $invoice): void {
            $validated = $request->validated();
            $items = $this->totalsCalculator->normalizeItems($validated['items'] ?? []);
            $client = $this->resolveClient($validated);

            $subtotal = $this->totalsCalculator->subtotal($items);
            $taxRate = (float) ($validated['tax_rate'] ?? 0);
            $taxAmount = $this->totalsCalculator->taxAmount($subtotal, $taxRate);
            $totalAmount = $this->totalsCalculator->total($subtotal, $taxAmount);

            $invoice->update([
                ...collect($validated)->except('items')->all(),
                'client_id' => $client?->id,
                'client_name' => $client?->name ?? $validated['client_name'],
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
            ]);

            $invoice->items()->delete();
            $invoice->items()->createMany($items);
        });

        return redirect()->route('invoices.show', $invoice)->with('status', 'Invoice updated.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->authorize('delete', $invoice);

        if ($invoice->status !== 'Draft' && $invoice->status !== 'Cancelled') {
            return redirect()->route('invoices.index')->with('status', 'Only draft or cancelled invoices can be deleted.');
        }

        $invoice->delete();

        return redirect()->route('invoices.index')->with('status', 'Invoice deleted.');
    }

    public function convertFromQuote(Quote $quote): RedirectResponse
    {
        $this->authorize('create', Invoice::class);

        if ($quote->status === 'Expired' || $quote->status === 'Draft') {
            return back()->with('status', 'Only sent, approved, or active quotes can be converted into an invoice.');
        }

        $invoice = DB::transaction(function () use ($quote): Invoice {
            $quote->loadMissing('crewLines');

            $subtotal = (float) $quote->total_amount;
            $taxRate = 0.00;
            $taxAmount = 0.00;
            $totalAmount = $subtotal;

            $invoice = Invoice::query()->create([
                'doc_no' => $this->documentNumbers->next(),
                'quote_id' => $quote->id,
                'client_id' => $quote->client_id,
                'client_name' => $quote->client_name,
                'client_po' => $quote->client_po,
                'vessel' => $quote->vessel,
                'location' => $quote->location,
                'project_name' => $quote->project_name,
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'status' => 'Draft',
                'currency' => $quote->currency,
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'notes' => $quote->special_conditions,
                'payment_instructions' => 'Payment terms: '.($quote->payment_terms ?: '30 days from invoice').".\nPlease reference {$quote->doc_no} on transfer.",
            ]);

            $itemsPayload = $quote->crewLines->map(function ($line): array {
                $duration = $line->duration_days > 0 ? $line->duration_days : ($line->duration > 0 ? $line->duration : 1);
                $rate = (float) $line->rate;
                $qty = (int) $line->qty;
                $lineTotal = $line->line_total > 0 ? (float) $line->line_total : round($qty * $rate * $duration, 2);

                return [
                    'description' => $line->rank ?: 'Service Line Item',
                    'category' => $line->category ?: 'Marine',
                    'qty' => $qty,
                    'basis' => $line->basis ?: 'Day',
                    'rate' => $rate,
                    'duration' => $duration,
                    'duration_unit' => $line->duration_days > 0 ? 'Days' : ($line->duration_months > 0 ? 'Months' : ($line->basis ?: 'Days')),
                    'line_total' => $lineTotal,
                ];
            })->all();

            $invoice->items()->createMany($itemsPayload);

            if ($quote->status === 'Approved') {
                $quote->update(['status' => 'Active']);
            }

            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice)->with('status', "Invoice {$invoice->doc_no} generated from Quote {$quote->doc_no}.");
    }

    public function issue(Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);

        return $this->transitionTo($invoice, 'Issued');
    }

    public function markPaid(Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);

        return $this->transitionTo($invoice, 'Paid');
    }

    public function cancel(Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);

        return $this->transitionTo($invoice, 'Cancelled');
    }

    public function previewPdf(Invoice $invoice): Response
    {
        $this->authorize('view', $invoice);

        $invoice->load(['items', 'client', 'quote']);

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'today' => now(),
        ])->setPaper('a4');

        return $pdf->stream($invoice->doc_no.'.pdf');
    }

    public function exportPdf(Invoice $invoice): Response
    {
        $this->authorize('view', $invoice);

        $invoice->load(['items', 'client', 'quote']);

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'today' => now(),
        ])->setPaper('a4');

        return $pdf->download($invoice->doc_no.'.pdf');
    }

    private function transitionTo(Invoice $invoice, string $status): RedirectResponse
    {
        $invoice->update(['status' => $status]);

        return back()->with('status', "Invoice marked as {$status}.");
    }

    private function isLocked(Invoice $invoice): bool
    {
        return in_array($invoice->status, self::LOCKED_STATUSES, true);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function resolveClient(array $validated): ?Client
    {
        if (! empty($validated['client_id'])) {
            return Client::query()->find((int) $validated['client_id']);
        }

        if (! empty($validated['client_name'])) {
            return Client::query()->where('name', (string) $validated['client_name'])->first();
        }

        return null;
    }
}
