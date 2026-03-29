<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
            @isset($currentBusiness)
                <p class="text-sm text-gray-500">{{ $currentBusiness->name }}</p>
            @endisset
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <div class="text-sm font-medium text-gray-500">Produk aktif</div>
                    <div class="mt-2 text-3xl font-semibold text-gray-900">{{ $productCount }}</div>
                </div>
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-amber-100">
                    <div class="text-sm font-medium text-amber-800">Stok di bawah minimum</div>
                    <div class="mt-2 text-3xl font-semibold text-amber-900">{{ $lowStockCount }}</div>
                    @if ($lowStockCount > 0)
                        <a href="{{ route('reports.low-stock') }}" class="mt-3 inline-block text-sm font-medium text-amber-800 hover:underline">Lihat laporan</a>
                    @endif
                </div>
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                    <div class="text-sm font-medium text-gray-500">Draft stok</div>
                    <div class="mt-2 text-3xl font-semibold text-gray-900">{{ $draftStockCount }}</div>
                    @if ($draftStockCount > 0)
                        <a href="{{ route('stock-transactions.index', ['status' => 'draft']) }}" class="mt-3 inline-block text-sm font-medium text-indigo-600 hover:underline">Buka daftar</a>
                    @endif
                </div>
            </div>

            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                <div class="border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-800">Pergerakan terbaru (posted)</h3>
                    <a href="{{ route('stock-transactions.index', ['status' => 'posted']) }}" class="text-sm text-indigo-600 hover:underline">Semua</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse ($recentMovements as $tx)
                        <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <div class="font-medium text-gray-900">{{ $tx->document_number }}</div>
                                <div class="text-xs text-gray-500">{{ $tx->type->label() }} · {{ $tx->occurred_on->format('d M Y') }}</div>
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ $tx->lines->count() }} baris
                            </div>
                            <a href="{{ route('stock-transactions.show', $tx) }}" class="text-sm font-medium text-indigo-600 hover:underline">Detail</a>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center text-sm text-gray-500">Belum ada dokumen yang diposting.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
