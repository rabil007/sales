<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $q = $request->string('q')->trim()->toString();
        $company = $request->string('company')->toString();
        $contact = $request->string('contact')->toString();
        $perPage = (int) $request->integer('per_page', 15);
        if (! in_array($perPage, [10, 15, 25, 50], true)) {
            $perPage = 15;
        }

        $query = Client::query()
            ->when($q !== '', fn ($builder) => $builder->where(function ($builder) use ($q): void {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            }))
            ->when($company !== '', fn ($builder) => $builder->where('company', $company))
            ->when($contact !== '', function ($builder) use ($contact): void {
                if ($contact === 'with') {
                    $builder->where(function ($builder): void {
                        $builder->whereNotNull('email')->where('email', '!=', '')
                            ->orWhereNotNull('phone')->where('phone', '!=', '');
                    });
                }

                if ($contact === 'without') {
                    $builder->where(function ($builder): void {
                        $builder->where(function ($builder): void {
                            $builder->whereNull('email')->orWhere('email', '');
                        })->where(function ($builder): void {
                            $builder->whereNull('phone')->orWhere('phone', '');
                        });
                    });
                }
            });

        $totalCount = (clone $query)->count();
        $withEmailCount = (clone $query)->whereNotNull('email')->where('email', '!=', '')->count();
        $withPhoneCount = (clone $query)->whereNotNull('phone')->where('phone', '!=', '')->count();

        return view('pages.clients.index', [
            'q' => $q,
            'company' => $company,
            'contact' => $contact,
            'perPage' => $perPage,
            'companies' => Client::query()->select('company')->whereNotNull('company')->where('company', '!=', '')->distinct()->orderBy('company')->pluck('company'),
            'stats' => [
                'total' => $totalCount,
                'withEmail' => $withEmailCount,
                'withPhone' => $withPhoneCount,
            ],
            'clients' => $query->latest('id')->paginate($perPage)->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.clients.form', [
            'client' => new Client,
            'isEdit' => false,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClientRequest $request): RedirectResponse
    {
        $client = Client::query()->create($request->validated());

        return redirect()->route('clients.edit', $client)->with('status', 'Client created.');
    }

    /**
     * Display the specified resource.
     */
    public function edit(Client $client): View
    {
        return view('pages.clients.form', [
            'client' => $client,
            'isEdit' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->validated());

        return redirect()->route('clients.edit', $client)->with('status', 'Client updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()->route('clients.index')->with('status', 'Client deleted.');
    }
}
