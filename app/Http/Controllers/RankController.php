<?php

namespace App\Http\Controllers;

use App\Http\Requests\RankRequest;
use App\Models\Rank;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('pages.ranks.index', [
            'ranks' => Rank::query()->latest('id')->paginate(15),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pages.ranks.form', [
            'rank' => new Rank([
                'category' => 'Marine',
                'default_basis' => 'Day',
                'default_rate' => 0,
                'is_active' => true,
            ]),
            'isEdit' => false,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RankRequest $request): RedirectResponse
    {
        $rank = Rank::query()->create([
            ...$request->validated(),
            'is_active' => (bool) $request->boolean('is_active', true),
        ]);

        return redirect()->route('ranks.edit', $rank)->with('status', 'Rank created.');
    }

    /**
     * Display the specified resource.
     */
    public function edit(Rank $rank): View
    {
        return view('pages.ranks.form', [
            'rank' => $rank,
            'isEdit' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RankRequest $request, Rank $rank): RedirectResponse
    {
        $rank->update([
            ...$request->validated(),
            'is_active' => (bool) $request->boolean('is_active', false),
        ]);

        return redirect()->route('ranks.edit', $rank)->with('status', 'Rank updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rank $rank): RedirectResponse
    {
        $rank->delete();

        return redirect()->route('ranks.index')->with('status', 'Rank deleted.');
    }
}
