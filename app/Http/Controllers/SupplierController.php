<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Support\BusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::query()->orderBy('name')->paginate(15);

        return view('suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        Supplier::query()->create([
            'business_id' => BusinessContext::id(),
            'code' => $request->input('code'),
            'name' => $request->string('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'notes' => $request->input('notes'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('suppliers.index')->with('status', 'Supplier berhasil dibuat.');
    }

    public function show(Supplier $supplier): View
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update([
            'code' => $request->input('code'),
            'name' => $request->string('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'notes' => $request->input('notes'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('suppliers.show', $supplier)->with('status', 'Supplier diperbarui.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->stockTransactions()->exists()) {
            return back()->with('error', 'Supplier masih direferensikan dokumen stok.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('status', 'Supplier dihapus.');
    }
}
