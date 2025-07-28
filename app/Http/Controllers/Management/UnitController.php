<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use App\Models\Unit;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UnitController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Unit::class);

        $search = $request->input('search');

        $query = Unit::withCount('users')
            ->when($search, function ($q) use ($search) {
            $q->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        });

        $units = $query->paginate(10)->withQueryString();

        return Inertia::render('Management/Units/Index', [
            'units' => $units,
            'current_page' => $units->currentPage(),
            'last_page' => $units->lastPage(),
            'total' => $units->total(),
            'prev_page_url' => $units->previousPageUrl(),
            'next_page_url' => $units->nextPageUrl(),
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        $this->authorize('create', Unit::class);
        return Inertia::render('Management/Units/Create');
    }

    public function store(UnitRequest $request)
    {
        $this->authorize('create', Unit::class);
        Unit::create($request->validated());
        return redirect()->route('management.units.index');
    }

    public function show(Unit $unit)
    {
        $this->authorize('view', $unit);
        return Inertia::render('Management/Units/Show', [
            'unit' => $unit,
        ]);
    }

    public function edit(Unit $unit)
    {
        $this->authorize('update', $unit);
        return Inertia::render('Management/Units/Edit', ['unit' => $unit]);
    }

    public function update(UnitRequest $request, Unit $unit)
    {
        $this->authorize('update', $unit);
        $unit->update($request->validated());
        return redirect()->route('management.units.index');
    }

    public function destroy(Unit $unit)
    {
        $this->authorize('delete', $unit);
        $unit->delete();
        return redirect()->route('management.units.index');
    }
}
