<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientAgreementRequest;
use App\Models\Client;
use App\Models\ClientAgreement;
use App\Models\CompanySetting;
use App\Support\ClientAgreements\ClientAgreementExportQuery;
use App\Support\ClientAgreements\ClientAgreementSpreadsheetExporter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientAgreementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ClientAgreement::class);

        $q = $request->string('q')->trim()->toString();
        $clientId = $request->string('client_id')->toString();
        $perPage = (int) $request->integer('per_page', 15);
        if (! in_array($perPage, [10, 15, 25, 50], true)) {
            $perPage = 15;
        }

        $query = ClientAgreementExportQuery::fromRequest($request);

        $totalCount = (clone $query)->count();
        $activeCount = (clone $query)->whereDate('end_date', '>=', now()->toDateString())->count();
        $totalMonthlyValue = (clone $query)->sum('monthly_invoice_value');

        return view('pages.client-agreements.index', [
            'q' => $q,
            'clientId' => $clientId,
            'perPage' => $perPage,
            'clients' => Client::query()->orderBy('name')->get(['id', 'name']),
            'stats' => [
                'total' => $totalCount,
                'active' => $activeCount,
                'totalMonthlyValue' => $totalMonthlyValue,
            ],
            'agreements' => (clone $query)->latest('id')->paginate($perPage)->withQueryString(),
        ]);
    }

    public function exportExcel(Request $request, ClientAgreementSpreadsheetExporter $exporter): StreamedResponse
    {
        $this->authorize('viewAny', ClientAgreement::class);

        return $exporter->download(
            ClientAgreementExportQuery::fromRequest($request),
            'client-agreements-'.now()->format('Y-m-d').'.xlsx',
        );
    }

    public function exportPdf(Request $request): Response
    {
        $this->authorize('viewAny', ClientAgreement::class);

        $agreements = ClientAgreementExportQuery::fromRequest($request)->get();

        $pdf = Pdf::loadView('pdf.client-agreements-export', [
            'agreements' => $agreements,
            'generatedAt' => now(),
            'appName' => CompanySetting::get('app_name', config('app.name', 'OMS Sales')),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('client-agreements-'.now()->format('Y-m-d').'.pdf');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', ClientAgreement::class);

        return view('pages.client-agreements.form', [
            'agreement' => new ClientAgreement,
            'clients' => Client::query()->orderBy('name')->get(['id', 'name']),
            'isEdit' => false,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClientAgreementRequest $request): RedirectResponse
    {
        $this->authorize('create', ClientAgreement::class);

        ClientAgreement::query()->create($request->validated());

        return redirect()->route('client-agreements.index')->with('status', 'Client agreement created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClientAgreement $clientAgreement): View
    {
        $this->authorize('update', $clientAgreement);

        return view('pages.client-agreements.form', [
            'agreement' => $clientAgreement,
            'clients' => Client::query()->orderBy('name')->get(['id', 'name']),
            'isEdit' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClientAgreementRequest $request, ClientAgreement $clientAgreement): RedirectResponse
    {
        $this->authorize('update', $clientAgreement);

        $clientAgreement->update($request->validated());

        return redirect()->route('client-agreements.index')->with('status', 'Client agreement updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClientAgreement $clientAgreement): RedirectResponse
    {
        $this->authorize('delete', $clientAgreement);

        $clientAgreement->delete();

        return redirect()->route('client-agreements.index')->with('status', 'Client agreement deleted.');
    }
}
