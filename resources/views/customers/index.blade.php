<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Pelanggan</h2>
            <a href="{{ route('customers.create') }}"><x-primary-button type="button">Tambah</x-primary-button></a>
        </div>
    </x-slot>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 text-left">
                        <tr><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Kode</th><th class="px-4 py-3">Kontak</th><th class="px-4 py-3"></th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($customers as $c)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $c->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $c->code ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $c->phone ?? $c->email ?? '—' }}</td>
                                <td class="px-4 py-3 text-right"><a href="{{ route('customers.show', $c) }}" class="text-indigo-600 hover:underline">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $customers->links() }}</div>
        </div>
    </div>
</x-app-layout>
