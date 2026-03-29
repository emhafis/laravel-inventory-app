<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Laporan</h2>
    </x-slot>
    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <a href="{{ route('reports.stock-on-hand') }}" class="block rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100 hover:ring-indigo-200 transition">
                <div class="font-semibold text-gray-900">Kartu stok &amp; valuasi</div>
                <p class="mt-1 text-sm text-gray-600">Saldo per produk, estimasi nilai pokok dan jual.</p>
            </a>
            <a href="{{ route('reports.movements') }}" class="block rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100 hover:ring-indigo-200 transition">
                <div class="font-semibold text-gray-900">Riwayat gerakan</div>
                <p class="mt-1 text-sm text-gray-600">Audit trail dari posting dokumen (ledger).</p>
            </a>
            <a href="{{ route('reports.low-stock') }}" class="block rounded-xl bg-white p-5 shadow-sm ring-1 ring-amber-100 hover:ring-amber-200 transition">
                <div class="font-semibold text-amber-900">Stok di bawah minimum</div>
                <p class="mt-1 text-sm text-amber-900/80">Produk yang perlu reorder.</p>
            </a>
        </div>
    </div>
</x-app-layout>
