@php
    use App\Enums\StockTransactionType;
    /** @var \App\Enums\StockTransactionType $type */
    $showUnitCost = $type === StockTransactionType::In;
    $showSupplier = $type === StockTransactionType::In;
    $showCustomer = $type === StockTransactionType::Out;
    $defaultLines = old('lines', [['product_id' => '', 'quantity' => '', 'unit_cost' => '']]);
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Dokumen stok — {{ $type->label() }}</h2>
    </x-slot>
    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('stock-transactions.store') }}"
                  x-data="{
                    lines: @js($defaultLines),
                    showUnitCost: @js($showUnitCost),
                    add() { this.lines.push({ product_id: '', quantity: '', unit_cost: '' }) },
                    remove(i) { if (this.lines.length > 1) this.lines.splice(i, 1) }
                  }"
                  class="space-y-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100 text-sm">
                @csrf
                <input type="hidden" name="type" value="{{ $type->value }}">

                @if ($showSupplier)
                    <div>
                        <x-input-label for="supplier_id" value="Supplier (opsional)" />
                        <select id="supplier_id" name="supplier_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                            <option value="">—</option>
                            @foreach ($suppliers as $s)
                                <option value="{{ $s->id }}" @selected(old('supplier_id') == $s->id)>{{ $s->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('supplier_id')" class="mt-2" />
                    </div>
                @endif

                @if ($showCustomer)
                    <div>
                        <x-input-label for="customer_id" value="Pelanggan (opsional)" />
                        <select id="customer_id" name="customer_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                            <option value="">—</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}" @selected(old('customer_id') == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                    </div>
                @endif

                <div>
                    <x-input-label for="occurred_on" value="Tanggal" />
                    <x-text-input id="occurred_on" name="occurred_on" type="date" class="mt-1 block w-full" :value="old('occurred_on', now()->format('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('occurred_on')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="notes" value="Catatan" />
                    <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 text-sm">{{ old('notes') }}</textarea>
                </div>

                @if ($type === StockTransactionType::Adjustment)
                    <p class="text-xs text-amber-800 bg-amber-50 ring-1 ring-amber-100 rounded-lg p-3">
                        Penyesuaian: gunakan jumlah <strong>positif</strong> untuk menambah stok, <strong>negatif</strong> untuk mengurangi.
                    </p>
                @endif

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800">Baris</h3>
                        <button type="button" @click="add()" class="text-sm font-medium text-indigo-600 hover:underline">+ Baris</button>
                    </div>
                    <template x-for="(line, index) in lines" :key="index">
                        <div class="grid gap-3 sm:grid-cols-12 items-end border border-gray-100 rounded-lg p-3">
                            <div class="sm:col-span-5">
                                <label class="text-xs text-gray-500">Produk</label>
                                <select class="mt-1 block w-full rounded-md border-gray-300 text-sm" x-model="line.product_id" :name="'lines['+index+'][product_id]'">
                                    <option value="">Pilih</option>
                                    @foreach ($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} — {{ $p->sku }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-3">
                                <label class="text-xs text-gray-500">Jumlah</label>
                                <input type="number" step="0.0001" class="mt-1 block w-full rounded-md border-gray-300 text-sm" x-model="line.quantity" :name="'lines['+index+'][quantity]'" required />
                            </div>
                            <div class="sm:col-span-3" x-show="showUnitCost" x-cloak>
                                <label class="text-xs text-gray-500">Harga / unit (opsional)</label>
                                <input type="number" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 text-sm" x-model="line.unit_cost" :name="'lines['+index+'][unit_cost]'" />
                            </div>
                            <div class="sm:col-span-1 flex justify-end">
                                <button type="button" class="text-xs text-red-600 hover:underline" @click="remove(index)">Hapus</button>
                            </div>
                        </div>
                    </template>
                </div>
                <x-input-error :messages="$errors->get('lines')" class="mt-2" />

                <div class="flex gap-3">
                    <x-primary-button type="submit">Simpan draft</x-primary-button>
                    <a href="{{ route('stock-transactions.index') }}" class="self-center text-gray-600 hover:underline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
