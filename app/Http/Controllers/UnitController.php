<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use App\Models\Unit;
use App\Support\BusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(): View
    {
        $units = Unit::query()->orderBy('name')->paginate(15);

        return view('units.index', compact('units'));
    }

    public function create(): View
    {
        return view('units.create');
    }

    public function store(StoreUnitRequest $request): RedirectResponse
    {
        Unit::query()->create([
            'business_id' => BusinessContext::id(),
            'name' => $request->string('name'),
            'code' => $request->string('code'),
            'description' => $request->input('description'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('units.index')->with('status', 'Satuan berhasil dibuat.');
    }

    public function show(Unit $unit): View
    {
        $unit->load('products');

        return view('units.show', compact('unit'));
    }

    public function edit(Unit $unit): View
    {
        return view('units.edit', compact('unit'));
    }

    public function update(UpdateUnitRequest $request, Unit $unit): RedirectResponse
    {
        $unit->update([
            'name' => $request->string('name'),
            'code' => $request->string('code'),
            'description' => $request->input('description'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('units.show', $unit)->with('status', 'Satuan diperbarui.');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        if ($unit->products()->exists()) {
            return back()->with('error', 'Satuan masih digunakan oleh produk.');
        }

        $unit->delete();

        return redirect()->route('units.index')->with('status', 'Satuan dihapus.');
    }
}
