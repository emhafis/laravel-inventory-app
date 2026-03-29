<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Produk</h2>
            <a href="{{ route('products.create') }}"><x-primary-button type="button">Tambah</x-primary-button></a>
        </div>
    </x-slot>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 text-left">
                        <tr>
                            <th class="px-4 py-3">SKU</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3 text-right">Stok</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($products as $p)
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $p->sku }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $p->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $p->category->name }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">{{ number_format((float) ($p->stockBalance->quantity ?? 0), 4, ',', '.') }} {{ $p->unit->code }}</td>
                                <td class="px-4 py-3 text-right"><a href="{{ route('products.show', $p) }}" class="text-indigo-600 hover:underline">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $products->links() }}</div>
        </div>
    </div>
</x-app-layout>
