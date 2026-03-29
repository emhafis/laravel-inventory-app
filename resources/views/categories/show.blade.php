<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800">{{ $category->name }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('categories.edit', $category) }}">
                    <x-secondary-button type="button">Edit</x-secondary-button>
                </a>
                <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?');">
                    @csrf
                    @method('DELETE')
                    <x-danger-button type="submit">Hapus</x-danger-button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100 text-sm text-gray-700 space-y-2">
                <div><span class="text-gray-500">Slug:</span> {{ $category->slug }}</div>
                <div><span class="text-gray-500">Induk:</span> {{ $category->parent?->name ?? '—' }}</div>
                <div><span class="text-gray-500">Status:</span> {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</div>
                @if ($category->description)
                    <div class="pt-2 border-t border-gray-100">{{ $category->description }}</div>
                @endif
            </div>

            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                <div class="border-b border-gray-100 px-6 py-4 font-semibold text-gray-800">Produk ({{ $category->products->count() }})</div>
                <ul class="divide-y divide-gray-50">
                    @forelse ($category->products as $product)
                        <li class="px-6 py-3 flex justify-between gap-4">
                            <div>
                                <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500">SKU {{ $product->sku }}</div>
                            </div>
                            <a href="{{ route('products.show', $product) }}" class="text-sm text-indigo-600 hover:underline">Detail</a>
                        </li>
                    @empty
                        <li class="px-6 py-8 text-center text-sm text-gray-500">Belum ada produk di kategori ini.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
