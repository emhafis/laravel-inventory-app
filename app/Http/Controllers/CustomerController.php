<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Support\BusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::query()->orderBy('name')->paginate(15);

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        Customer::query()->create([
            'business_id' => BusinessContext::id(),
            'code' => $request->input('code'),
            'name' => $request->string('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'notes' => $request->input('notes'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('customers.index')->with('status', 'Pelanggan berhasil dibuat.');
    }

    public function show(Customer $customer): View
    {
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update([
            'code' => $request->input('code'),
            'name' => $request->string('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'notes' => $request->input('notes'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('customers.show', $customer)->with('status', 'Pelanggan diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->stockTransactions()->exists()) {
            return back()->with('error', 'Pelanggan masih direferensikan dokumen stok.');
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('status', 'Pelanggan dihapus.');
    }
}
