@php
    /** @var \App\Models\StockTransaction $transaction */
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-start">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">{{ $transaction->document_number }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $transaction->type->label() }} · {{ $transaction->occurred_on->format('d M Y') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($transaction->isDraft())
                    <a href="{{ route('stock-transactions.edit', $transaction) }}"><x-secondary-button type="button">Edit</x-secondary-button></a>
                    <form method="POST" action="{{ route('stock-transactions.post', $transaction) }}" onsubmit="return confirm('Posting akan mengunci dokumen dan memperbarui stok. Lanjutkan?');">
                        @csrf
                        <x-primary-button type="submit">Posting</x-primary-button>
                    </form>
                    <form method="POST" action="{{ route('stock-transactions.destroy', $transaction) }}" onsubmit="return confirm('Hapus draft?');">
                        @csrf @method('DELETE')
                        <x-danger-button type="submit">Hapus</x-danger-button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>
    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100 text-sm text-gray-700 space-y-2">
                <div class="flex flex-wrap gap-2 items-center">
                    <span class="text-xs font-semibold uppercase text-gray-500">Status</span>
                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium
                        @if($transaction->status->value === 'draft') bg-amber-50 text-amber-900
                        @elseif($transaction->status->value === 'posted') bg-emerald-50 text-emerald-800
                        @else bg-gray-100 text-gray-600 @endif">
                        {{ $transaction->status->label() }}
                    </span>
                </div>
                @if ($transaction->supplier)
                    <div>Supplier: <span class="font-medium">{{ $transaction->supplier->name }}</span></div>
                @endif
                @if ($transaction->customer)
                    <div>Pelanggan: <span class="font-medium">{{ $transaction->customer->name }}</span></div>
                @endif
                @if ($transaction->notes)
                    <div class="pt-2 border-t whitespace-pre-line">{{ $transaction->notes }}</div>
                @endif
                @if ($transaction->isPosted())
                    <div class="text-xs text-gray-500 pt-2 border-t">
                        Diposting {{ $transaction->posted_at?->format('d M Y H:i') }}
                        @if ($transaction->postedByUser) oleh {{ $transaction->postedByUser->name }} @endif
                    </div>
                @endif
            </div>

            <div class="overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 text-left">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3 text-right">Qty</th>
                            <th class="px-4 py-3 text-right">Harga / unit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($transaction->lines as $line)
                            <tr>
                                <td class="px-4 py-3 text-gray-500">{{ $line->line_no }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $line->product->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $line->product->sku }} · {{ $line->product->unit->code }}</div>
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums">{{ number_format((float) $line->quantity, 4, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">
                                    {{ $line->unit_cost !== null ? number_format((float) $line->unit_cost, 2, ',', '.') : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
