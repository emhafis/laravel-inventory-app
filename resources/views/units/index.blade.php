<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Satuan</h2>
            <a href="{{ route('units.create') }}"><x-primary-button type="button">Tambah</x-primary-button></a>
        </div>
    </x-slot>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Kode</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($units as $unit)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $unit->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $unit->code }}</td>
                                <td class="px-4 py-3">{{ $unit->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('units.show', $unit) }}" class="text-indigo-600 hover:underline">Lihat</a>
                                    <a href="{{ route('units.edit', $unit) }}" class="text-gray-700 hover:underline">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $units->links() }}</div>
        </div>
    </div>
</x-app-layout>
