<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800">Stok di bawah minimum</h2>
            <a href="{{ route('reports.index') }}" class="text-sm text-indigo-600 hover:underline">← Kembali</a>
        </div>
    </x-slot>
    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-x-auto rounded-xl bg-amber-50/60 shadow-sm ring-1 ring-amber-100">
                <table class="min-w-full divide-y divide-amber-100 text-sm">
                    <thead class="bg-amber-100/80 text-xs uppercase text-amber-900 text-left">
                        <tr>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3 text-right">Stok</th>
                            <th class="px-4 py-3 text-right">Minimum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-50 bg-white">
                        @forelse ($rows as $row)
                            @php $p = $row->product; @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $p->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $p->sku }}</div>
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums font-semibold text-amber-900">{{ number_format((float) $row->quantity, 4, ',', '.') }} {{ $p->unit->code }}</td>
                                <td class="px-4 py-3 text-right tabular-nums text-gray-600">{{ number_format((float) $p->min_stock_level, 4, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">Semua produk di atas minimum.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $rows->links() }}</div>
        </div>
    </div>
</x-app-layout>
