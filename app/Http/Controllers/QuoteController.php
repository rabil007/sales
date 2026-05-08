<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteRequest;
use App\Models\Client;
use App\Models\Quote;
use App\Models\Rank;
use App\Support\Quotes\CrewLineTotalsCalculator;
use App\Support\Quotes\QuoteDocumentNumberGenerator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuoteController extends Controller
{
    private const LOCKED_STATUSES = ['Approved', 'Active', 'Expired'];

    public function __construct(
        private CrewLineTotalsCalculator $crewLineTotals,
        private QuoteDocumentNumberGenerator $documentNumbers,
    ) {}

    /**
     * Display dashboard summary.
     */
    public function dashboard(): View
    {
        $this->authorize('viewAny', Quote::class);

        $statusCounts = Quote::query()
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $topClients = Quote::query()
            ->select('client_name', DB::raw('COUNT(*) as quotes_count'), DB::raw('SUM(total_amount) as total_value'))
            ->whereNotNull('client_name')
            ->groupBy('client_name')
            ->orderByDesc('total_value')
            ->limit(5)
            ->get();

        $monthlyValue = Quote::query()
            ->whereNotNull('issue_date')
            ->where('issue_date', '>=', now()->subMonths(5)->startOfMonth())
            ->get(['issue_date', 'total_amount'])
            ->groupBy(fn (Quote $quote) => optional($quote->issue_date)->format('Y-m'))
            ->map(fn ($quotes) => (float) $quotes->sum('total_amount'));

        $monthlyChart = collect(range(0, 5))
            ->mapWithKeys(function (int $offset) use ($monthlyValue): array {
                $date = now()->subMonths(5 - $offset)->startOfMonth();
                $key = $date->format('Y-m');

                return [$date->format('M Y') => (float) ($monthlyValue[$key] ?? 0)];
            });

        $monthlyQuoteCount = Quote::query()
            ->whereNotNull('issue_date')
            ->where('issue_date', '>=', now()->subMonths(5)->startOfMonth())
            ->get(['issue_date'])
            ->groupBy(fn (Quote $quote) => optional($quote->issue_date)->format('Y-m'))
            ->map(fn ($quotes) => (int) $quotes->count());

        $monthlyCountChart = collect(range(0, 5))
            ->mapWithKeys(function (int $offset) use ($monthlyQuoteCount): array {
                $date = now()->subMonths(5 - $offset)->startOfMonth();
                $key = $date->format('Y-m');

                return [$date->format('M Y') => (int) ($monthlyQuoteCount[$key] ?? 0)];
            });

        $typeMixChart = Quote::query()
            ->select('type', DB::raw('COUNT(*) as count'))
            ->whereNotNull('type')
            ->groupBy('type')
            ->orderByDesc('count')
            ->limit(6)
            ->pluck('count', 'type')
            ->map(fn ($count): int => (int) $count);

        return view('dashboard', [
            'totalQuotes' => Quote::query()->count(),
            'activeAgreements' => Quote::query()->where('status', 'Active')->count(),
            'pendingApproval' => Quote::query()->where('status', 'Sent')->count(),
            'totalValue' => Quote::query()->sum('total_amount'),
            'averageQuoteValue' => Quote::query()->avg('total_amount') ?? 0,
            'expiringSoon' => Quote::query()
                ->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '>=', now()->toDateString())
                ->whereDate('expiry_date', '<=', now()->addDays(30)->toDateString())
                ->count(),
            'draftCount' => (int) ($statusCounts['Draft'] ?? 0),
            'sentCount' => (int) ($statusCounts['Sent'] ?? 0),
            'approvedCount' => (int) ($statusCounts['Approved'] ?? 0),
            'expiredCount' => (int) ($statusCounts['Expired'] ?? 0),
            'topClients' => $topClients,
            'monthlyChart' => $monthlyChart,
            'monthlyCountChart' => $monthlyCountChart,
            'statusChart' => collect([
                'Draft' => (int) ($statusCounts['Draft'] ?? 0),
                'Sent' => (int) ($statusCounts['Sent'] ?? 0),
                'Approved' => (int) ($statusCounts['Approved'] ?? 0),
                'Active' => (int) ($statusCounts['Active'] ?? 0),
                'Expired' => (int) ($statusCounts['Expired'] ?? 0),
            ]),
            'typeMixChart' => $typeMixChart,
            'recentQuotes' => Quote::query()->latest('id')->limit(5)->get(),
        ]);
    }

    /**
     * Display a listing of quotes.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Quote::class);

        $status = $request->string('status')->toString();
        $q = $request->string('q')->trim()->toString();
        $perPage = (int) $request->integer('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        return view('pages.quotes.index', [
            'status' => $status,
            'q' => $q,
            'perPage' => $perPage,
            'stats' => [
                'total' => Quote::query()->count(),
                'draft' => Quote::query()->where('status', 'Draft')->count(),
                'active' => Quote::query()->where('status', 'Active')->count(),
            ],
            'quotes' => Quote::query()
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($q !== '', fn ($query) => $query->where(function ($query) use ($q): void {
                    $query->where('client_name', 'like', "%{$q}%")
                        ->orWhere('doc_no', 'like', "%{$q}%");
                }))
                ->with('client:id,name')
                ->latest('id')
                ->paginate($perPage)
                ->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Quote::class);

        return view('pages.quotes.form', [
            'quote' => new Quote([
                'doc_no' => $this->documentNumbers->next(),
                'type' => 'Proposal',
                'issue_date' => now()->toDateString(),
                'status' => 'Draft',
                'currency' => 'AED',
                'payment_terms' => '30 days from invoice',
            ]),
            'crewLines' => [],
            'clients' => Client::query()->orderBy('name')->get(['id', 'name']),
            'ranks' => Rank::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'category', 'default_basis', 'default_rate']),
            'isEdit' => false,
            'isLocked' => false,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuoteRequest $request): RedirectResponse
    {
        $this->authorize('create', Quote::class);

        DB::transaction(function () use ($request): Quote {
            $validated = $request->validated();
            $crewLines = $this->crewLineTotals->normalize($validated['crew_lines'] ?? []);
            $client = $this->resolveClient($validated);

            $quote = Quote::query()->create([
                ...collect($validated)->except('crew_lines')->all(),
                'client_id' => $client?->id,
                'client_name' => $client?->name ?? $validated['client_name'],
                'total_amount' => $this->crewLineTotals->total($crewLines),
            ]);

            $quote->crewLines()->createMany($crewLines);

            return $quote;
        });

        return redirect()->route('quotes.index')->with('status', 'Quote created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Quote $quote): View
    {
        $this->authorize('view', $quote);

        $quote->load(['crewLines', 'client']);

        return view('pages.quotes.show', ['quote' => $quote]);
    }

    public function preview(Quote $quote): View
    {
        $this->authorize('view', $quote);

        $quote->load(['crewLines', 'client']);

        return view('pdf.quote-proposal', [
            'quote' => $quote,
            'today' => now(),
        ]);
    }

    public function previewPdf(Quote $quote): Response
    {
        $this->authorize('view', $quote);

        $quote->load(['crewLines', 'client']);

        $pdf = Pdf::loadView('pdf.quote-proposal', [
            'quote' => $quote,
            'today' => now(),
        ])->setPaper('a4');

        return $pdf->stream($quote->doc_no.'-proposal.pdf');
    }

    public function exportPdf(Quote $quote): Response
    {
        $this->authorize('view', $quote);

        $quote->load(['crewLines', 'client']);

        $pdf = Pdf::loadView('pdf.quote-proposal', [
            'quote' => $quote,
            'today' => now(),
        ])->setPaper('a4');

        return $pdf->download($quote->doc_no.'-proposal.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quote $quote): View
    {
        $this->authorize('update', $quote);

        $quote->refresh()->load(['crewLines', 'client']);

        return view('pages.quotes.form', [
            'quote' => $quote,
            'crewLines' => $quote->crewLines->toArray(),
            'clients' => Client::query()->orderBy('name')->get(['id', 'name']),
            'ranks' => Rank::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'category', 'default_basis', 'default_rate']),
            'isEdit' => true,
            'isLocked' => $this->isLocked($quote),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(QuoteRequest $request, Quote $quote): RedirectResponse
    {
        $this->authorize('update', $quote);

        if ($this->isLocked($quote)) {
            return redirect()->route('quotes.index')->with('status', 'Locked quotes cannot be edited.');
        }

        DB::transaction(function () use ($request, $quote): void {
            $validated = $request->validated();
            $crewLines = $this->crewLineTotals->normalize($validated['crew_lines'] ?? []);
            $client = $this->resolveClient($validated);

            $quote->update([
                ...collect($validated)->except('crew_lines')->all(),
                'client_id' => $client?->id,
                'client_name' => $client?->name ?? $validated['client_name'],
                'total_amount' => $this->crewLineTotals->total($crewLines),
            ]);

            $quote->crewLines()->delete();
            $quote->crewLines()->createMany($crewLines);
        });

        return redirect()->route('quotes.index')->with('status', 'Quote updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quote $quote): RedirectResponse
    {
        $this->authorize('delete', $quote);

        if ($this->isLocked($quote)) {
            return redirect()->route('quotes.index')->with('status', 'Locked quotes cannot be deleted.');
        }

        $quote->delete();

        return redirect()->route('quotes.index')->with('status', 'Quote deleted.');
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

    public function send(Quote $quote): RedirectResponse
    {
        $this->authorize('update', $quote);

        return $this->transitionTo($quote, 'Sent');
    }

    public function approve(Quote $quote): RedirectResponse
    {
        $this->authorize('update', $quote);

        return $this->transitionTo($quote, 'Approved');
    }

    public function activate(Quote $quote): RedirectResponse
    {
        $this->authorize('update', $quote);

        return $this->transitionTo($quote, 'Active');
    }

    public function expire(Quote $quote): RedirectResponse
    {
        $this->authorize('update', $quote);

        return $this->transitionTo($quote, 'Expired');
    }

    public function renew(Quote $quote): RedirectResponse
    {
        $this->authorize('renew', $quote);

        DB::transaction(function () use ($quote): void {
            $quote->loadMissing('crewLines');
            $linesPayload = $quote->crewLines->map(fn ($line) => $line->only([
                'rank',
                'category',
                'qty',
                'basis',
                'rate',
                'monthly_rate',
                'duration',
                'duration_days',
                'duration_months',
                'manual_total',
                'ot_rate',
                'mob_date',
                'demob_date',
                'remarks',
                'line_total',
            ]))->all();

            $renewalTotal = (float) collect($linesPayload)->sum(fn ($line): float => (float) ($line['line_total'] ?? 0));

            $newQuote = Quote::query()->create([
                ...$quote->only([
                    'type',
                    'currency',
                    'client_id',
                    'client_name',
                    'client_po',
                    'vessel',
                    'location',
                    'start_date',
                    'end_date',
                    'duration_text',
                    'project_name',
                    'payment_terms',
                    'scope',
                    'special_conditions',
                ]),
                'doc_no' => $this->documentNumbers->next(),
                'issue_date' => now()->toDateString(),
                'expiry_date' => null,
                'status' => 'Draft',
                'renewed_from_expiry_date' => $quote->expiry_date,
                'total_amount' => $renewalTotal,
            ]);

            $newQuote->crewLines()->createMany($linesPayload);
        });

        return redirect()->route('quotes.index')->with('status', 'Renewal quote generated.');
    }

    private function transitionTo(Quote $quote, string $status): RedirectResponse
    {
        $quote->update(['status' => $status]);

        return back()->with('status', "Quote moved to {$status}.");
    }

    private function isLocked(Quote $quote): bool
    {
        return in_array($quote->status, self::LOCKED_STATUSES, true);
    }
}
