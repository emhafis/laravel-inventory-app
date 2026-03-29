<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800">Kartu stok</h2>
            <a href="{{ route('reports.index') }}" class="text-sm text-indigo-600 hover:underline">← Kembali</a>
        </div>
    </x-slot>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if ($valuation)
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
                        <div class="text-xs font-semibold uppercase text-gray-500">Nilai pokok (qty × cost)</div>
                        <div class="mt-2 text-2xl font-semibold tabular-nums">{{ number_format((float) ($valuation->total_cost ?? 0), 2, ',', '.') }}</div>
                    </div>
                    <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-emerald-50">
                        <div class="text-xs font-semibold uppercase text-emerald-800">Nilai jual (qty × sell)</div>
                        <div class="mt-2 text-2xl font-semibold tabular-nums text-emerald-900">{{ number_format((float) ($valuation->total_sell ?? 0), 2, ',', '.') }}</div>
                    </div>
                </div>
            @endif

            <div class="overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 text-left">
                        <tr>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3 text-right">Qty</th>
                            <th class="px-4 py-3 text-right">Cost</th>
                            <th class="px-4 py-3 text-right">Sell</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($rows as $row)
                            @php $p = $row->product; @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $p->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $p->sku }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $p->category->name }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">{{ number_format((float) $row->quantity, 4, ',', '.') }} {{ $p->unit->code }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">{{ number_format((float) $p->cost_price, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">{{ number_format((float) $p->sell_price, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div>{{ $rows->links() }}</div>
        </div>
    </div>
</x-app-layout>
