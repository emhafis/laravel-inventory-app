<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">{{ $product->name }}</h2>
                <p class="text-sm text-gray-500 font-mono mt-1">{{ $product->sku }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('products.edit', $product) }}"><x-secondary-button type="button">Edit</x-secondary-button></a>
                <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Hapus produk?');">@csrf @method('DELETE')<x-danger-button type="submit">Hapus</x-danger-button></form>
            </div>
        </div>
    </x-slot>
    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
                    <div class="text-xs font-medium uppercase text-gray-500">Stok</div>
                    <div class="mt-2 text-2xl font-semibold tabular-nums">{{ number_format((float) ($product->stockBalance->quantity ?? 0), 4, ',', '.') }}</div>
                    <div class="text-sm text-gray-500">{{ $product->unit->code }}</div>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
                    <div class="text-xs font-medium uppercase text-gray-500">Harga pokok</div>
                    <div class="mt-2 text-xl font-semibold">{{ number_format((float) $product->cost_price, 2, ',', '.') }}</div>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
                    <div class="text-xs font-medium uppercase text-gray-500">Harga jual</div>
                    <div class="mt-2 text-xl font-semibold">{{ number_format((float) $product->sell_price, 2, ',', '.') }}</div>
                </div>
            </div>
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100 text-sm text-gray-700 space-y-2">
                <div>Kategori: <span class="font-medium">{{ $product->category->name }}</span></div>
                <div>Minimum stok: <span class="font-medium tabular-nums">{{ number_format((float) $product->min_stock_level, 4, ',', '.') }}</span></div>
                @if ($product->barcode)<div>Barcode: {{ $product->barcode }}</div>@endif
                @if ($product->description)<div class="pt-2 border-t whitespace-pre-line">{{ $product->description }}</div>@endif
            </div>
        </div>
    </div>
</x-app-layout>
