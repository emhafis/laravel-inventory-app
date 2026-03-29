<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800">{{ $unit->name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('units.edit', $unit) }}"><x-secondary-button type="button">Edit</x-secondary-button></a>
                <form method="POST" action="{{ route('units.destroy', $unit) }}" onsubmit="return confirm('Hapus satuan?');">
                    @csrf @method('DELETE')
                    <x-danger-button type="submit">Hapus</x-danger-button>
                </form>
            </div>
        </div>
    </x-slot>
    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100 text-sm text-gray-700">
                <div>Kode: <span class="font-medium">{{ $unit->code }}</span></div>
                @if ($unit->description)<div class="mt-2">{{ $unit->description }}</div>@endif
            </div>
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <div class="font-semibold text-gray-800 mb-2">Produk terkait</div>
                <ul class="text-sm divide-y divide-gray-50">
                    @forelse ($unit->products as $p)
                        <li class="py-2 flex justify-between"><span>{{ $p->name }}</span><a href="{{ route('products.show', $p) }}" class="text-indigo-600">Detail</a></li>
                    @empty
                        <li class="py-4 text-gray-500">Tidak ada produk.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
