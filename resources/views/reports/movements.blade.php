<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800">Riwayat gerakan stok</h2>
            <a href="{{ route('reports.index') }}" class="text-sm text-indigo-600 hover:underline">← Kembali</a>
        </div>
    </x-slot>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <form method="GET" class="flex flex-wrap items-end gap-3 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100 text-sm">
                <div>
                    <label class="text-xs text-gray-500">Dari</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="mt-1 block rounded-md border-gray-300 text-sm" />
                </div>
                <div>
                    <label class="text-xs text-gray-500">Sampai</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="mt-1 block rounded-md border-gray-300 text-sm" />
                </div>
                <div>
                    <label class="text-xs text-gray-500">Produk</label>
                    <select name="product_id" class="mt-1 block rounded-md border-gray-300 text-sm">
                        <option value="">Semua</option>
                        @foreach ($products as $p)
                            <option value="{{ $p->id }}" @selected(request('product_id') == $p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-primary-button class="h-10">Filter</x-primary-button>
            </form>

            <div class="overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 text-left">
                        <tr>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3">Dokumen</th>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3 text-right">Δ Qty</th>
                            <th class="px-4 py-3 text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($entries as $e)
                            <tr>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $e->recorded_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3 font-mono text-xs">
                                    <a href="{{ route('stock-transactions.show', $e->stockTransaction) }}" class="text-indigo-600 hover:underline">
                                        {{ $e->stockTransaction->document_number }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $e->product->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $e->product->unit->code }}</div>
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums {{ (float) $e->change_qty < 0 ? 'text-red-700' : 'text-emerald-700' }}">
                                    {{ (float) $e->change_qty >= 0 ? '+' : '' }}{{ number_format((float) $e->change_qty, 4, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums">{{ number_format((float) $e->quantity_after, 4, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div>{{ $entries->links() }}</div>
        </div>
    </div>
</x-app-layout>
