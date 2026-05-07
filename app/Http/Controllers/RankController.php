<?php

namespace App\Http\Controllers;

use App\Http\Requests\RankRequest;
use App\Models\Rank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $q = $request->string('q')->trim()->toString();
        $category = $request->string('category')->toString();
        $basis = $request->string('basis')->toString();
        $status = $request->string('status')->toString();
        $perPage = (int) $request->integer('per_page', 15);
        if (! in_array($perPage, [10, 15, 25, 50], true)) {
            $perPage = 15;
        }

        $query = Rank::query()
            ->when($q !== '', fn ($builder) => $builder->where('name', 'like', "%{$q}%"))
            ->when($category !== '', fn ($builder) => $builder->where('category', $category))
            ->when($basis !== '', fn ($builder) => $builder->where('default_basis', $basis))
            ->when($status !== '', fn ($builder) => $builder->where('is_active', $status === 'active'));

        $totalCount = (clone $query)->count();
        $activeCount = (clone $query)->where('is_active', true)->count();
        $inactiveCount = (clone $query)->where('is_active', false)->count();

        return view('pages.ranks.index', [
            'q' => $q,
            'category' => $category,
            'basis' => $basis,
            'status' => $status,
            'perPage' => $perPage,
            'categories' => Rank::query()->select('category')->distinct()->orderBy('category')->pluck('category'),
            'bases' => Rank::query()->select('default_basis')->distinct()->orderBy('default_basis')->pluck('default_basis'),
            'stats' => [
                'total' => $totalCount,
                'active' => $activeCount,
                'inactive' => $inactiveCount,
            ],
            'ranks' => $query->latest('id')->paginate($perPage)->withQueryString(),
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

    /**
     * Toggle active status directly from list.
     */
    public function toggleStatus(Rank $rank): RedirectResponse
    {
        $rank->update([
            'is_active' => ! $rank->is_active,
        ]);

        return back()->with('status', 'Rank status updated.');
    }
}
