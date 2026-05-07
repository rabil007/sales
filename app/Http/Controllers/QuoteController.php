<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteRequest;
use App\Models\Quote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuoteController extends Controller
{
    /**
     * Display dashboard summary.
     */
    public function dashboard(): View
    {
        return view('dashboard', [
            'totalQuotes' => Quote::query()->count(),
            'activeAgreements' => Quote::query()->where('status', 'Active')->count(),
            'pendingApproval' => Quote::query()->where('status', 'Sent')->count(),
            'totalValue' => Quote::query()->sum('total_amount'),
            'recentQuotes' => Quote::query()->latest('id')->limit(5)->get(),
        ]);
    }

    /**
     * Display a listing of quotes.
     */
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        return view('pages.quotes.index', [
            'status' => $status,
            'quotes' => Quote::query()
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->latest('id')
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.quotes.form', [
            'quote' => new Quote([
                'doc_no' => $this->generateDocNo(),
                'type' => 'Proposal',
                'issue_date' => now()->toDateString(),
                'status' => 'Draft',
                'currency' => 'AED',
                'payment_terms' => '30 days from invoice',
            ]),
            'crewLines' => [],
            'isEdit' => false,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuoteRequest $request): RedirectResponse
    {
        $quote = DB::transaction(function () use ($request): Quote {
            $validated = $request->validated();
            $crewLines = collect($validated['crew_lines'] ?? [])->filter(
                fn (array $line) => filled($line['rank'] ?? null)
            );

            $quote = Quote::query()->create([
                ...collect($validated)->except('crew_lines')->all(),
                'total_amount' => $this->calculateTotalAmount($crewLines->all()),
            ]);

            $quote->crewLines()->createMany($crewLines->all());

            return $quote;
        });

        return redirect()->route('quotes.edit', $quote)->with('status', 'Quote created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Quote $quote): View
    {
        $quote->load('crewLines');

        return view('pages.quotes.show', ['quote' => $quote]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quote $quote): View
    {
        $quote->load('crewLines');

        return view('pages.quotes.form', [
            'quote' => $quote,
            'crewLines' => $quote->crewLines->toArray(),
            'isEdit' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(QuoteRequest $request, Quote $quote): RedirectResponse
    {
        DB::transaction(function () use ($request, $quote): void {
            $validated = $request->validated();
            $crewLines = collect($validated['crew_lines'] ?? [])->filter(
                fn (array $line) => filled($line['rank'] ?? null)
            );

            $quote->update([
                ...collect($validated)->except('crew_lines')->all(),
                'total_amount' => $this->calculateTotalAmount($crewLines->all()),
            ]);

            $quote->crewLines()->delete();
            $quote->crewLines()->createMany($crewLines->all());
        });

        return redirect()->route('quotes.edit', $quote)->with('status', 'Quote updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quote $quote): RedirectResponse
    {
        $quote->delete();

        return redirect()->route('quotes.index')->with('status', 'Quote deleted.');
    }

    /**
     * @param  array<int, array<string, mixed>>  $crewLines
     */
    private function calculateTotalAmount(array $crewLines): float
    {
        return collect($crewLines)->sum(function (array $line): float {
            return (float) ($line['qty'] ?? 0)
                * (float) ($line['rate'] ?? 0)
                * (float) ($line['duration'] ?? 0);
        });
    }

    private function generateDocNo(): string
    {
        $year = now()->format('Y');
        $latest = Quote::query()
            ->where('doc_no', 'like', "OMS-Q-{$year}-%")
            ->latest('id')
            ->value('doc_no');

        if (! is_string($latest)) {
            return "OMS-Q-{$year}-001";
        }

        $lastNumber = (int) substr($latest, -3);

        return sprintf('OMS-Q-%s-%03d', $year, $lastNumber + 1);
    }
}
