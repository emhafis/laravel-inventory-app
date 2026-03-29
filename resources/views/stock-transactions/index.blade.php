<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Dokumen stok</h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('stock-transactions.create', ['type' => 'in']) }}"><x-secondary-button type="button">+ Masuk</x-secondary-button></a>
                <a href="{{ route('stock-transactions.create', ['type' => 'out']) }}"><x-secondary-button type="button">+ Keluar</x-secondary-button></a>
                <a href="{{ route('stock-transactions.create', ['type' => 'adjustment']) }}"><x-secondary-button type="button">+ Penyesuaian</x-secondary-button></a>
            </div>
        </div>
    </x-slot>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <form method="GET" class="flex flex-wrap items-end gap-3 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100 text-sm">
                <div>
                    <label class="block text-xs font-medium text-gray-500">Tipe</label>
                    <select name="type" class="mt-1 rounded-md border-gray-300 text-sm">
                        <option value="">Semua</option>
                        <option value="in" @selected(request('type') === 'in')>Masuk</option>
                        <option value="out" @selected(request('type') === 'out')>Keluar</option>
                        <option value="adjustment" @selected(request('type') === 'adjustment')>Penyesuaian</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500">Status</label>
                    <select name="status" class="mt-1 rounded-md border-gray-300 text-sm">
                        <option value="">Semua</option>
                        <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                        <option value="posted" @selected(request('status') === 'posted')>Diposting</option>
                    </select>
                </div>
                <x-primary-button class="h-10">Filter</x-primary-button>
                <a href="{{ route('stock-transactions.index') }}" class="text-gray-600 hover:underline self-center">Reset</a>
            </form>

            <div class="overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 text-left">
                        <tr>
                            <th class="px-4 py-3">Nomor</th>
                            <th class="px-4 py-3">Tipe</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($transactions as $tx)
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs">{{ $tx->document_number }}</td>
                                <td class="px-4 py-3">{{ $tx->type->label() }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $tx->occurred_on->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium
                                        @if($tx->status->value === 'draft') bg-amber-50 text-amber-900
                                        @elseif($tx->status->value === 'posted') bg-emerald-50 text-emerald-800
                                        @else bg-gray-100 text-gray-600 @endif">
                                        {{ $tx->status->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right"><a href="{{ route('stock-transactions.show', $tx) }}" class="text-indigo-600 hover:underline">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div>{{ $transactions->links() }}</div>
        </div>
    </div>
</x-app-layout>
