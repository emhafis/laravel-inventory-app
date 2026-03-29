<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Kategori</h2>
            <a href="{{ route('categories.create') }}">
                <x-primary-button type="button">Tambah</x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Induk</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($categories as $cat)
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $cat->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $cat->parent?->name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $cat->is_active ? 'bg-emerald-50 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('categories.show', $cat) }}" class="text-indigo-600 hover:underline">Lihat</a>
                                    <a href="{{ route('categories.edit', $cat) }}" class="text-gray-700 hover:underline">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $categories->links() }}</div>
        </div>
    </div>
</x-app-layout>
