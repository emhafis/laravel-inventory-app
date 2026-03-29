<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Produk baru</h2></x-slot>
    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('products.store') }}" class="space-y-4 rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100 text-sm">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="sku" value="SKU" />
                        <x-text-input id="sku" name="sku" class="mt-1 block w-full" :value="old('sku')" required />
                        <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="barcode" value="Barcode" />
                        <x-text-input id="barcode" name="barcode" class="mt-1 block w-full" :value="old('barcode')" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="name" value="Nama" />
                        <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name')" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="category_id" value="Kategori" />
                        <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm" required>
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}" @selected(old('category_id') == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="unit_id" value="Satuan" />
                        <select id="unit_id" name="unit_id" class="mt-1 block w-full rounded-md border-gray-300 text-sm" required>
                            @foreach ($units as $u)
                                <option value="{{ $u->id }}" @selected(old('unit_id') == $u->id)>{{ $u->name }} ({{ $u->code }})</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('unit_id')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="description" value="Deskripsi" />
                        <textarea id="description" name="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 text-sm">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <x-input-label for="cost_price" value="Harga pokok" />
                        <x-text-input id="cost_price" name="cost_price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('cost_price', '0')" required />
                    </div>
                    <div>
                        <x-input-label for="sell_price" value="Harga jual" />
                        <x-text-input id="sell_price" name="sell_price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('sell_price', '0')" required />
                    </div>
                    <div>
                        <x-input-label for="min_stock_level" value="Minimum stok" />
                        <x-text-input id="min_stock_level" name="min_stock_level" type="number" step="0.0001" min="0" class="mt-1 block w-full" :value="old('min_stock_level', '0')" required />
                    </div>
                </div>
                <label class="inline-flex items-center gap-2 text-gray-700">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-gray-300 text-indigo-600"> Aktif
                </label>
                <div class="flex gap-3">
                    <x-primary-button type="submit">Simpan</x-primary-button>
                    <a href="{{ route('products.index') }}" class="self-center text-gray-600 hover:underline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
