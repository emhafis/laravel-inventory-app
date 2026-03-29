<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800">{{ $supplier->name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('suppliers.edit', $supplier) }}"><x-secondary-button type="button">Edit</x-secondary-button></a>
                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Hapus?');">@csrf @method('DELETE')<x-danger-button type="submit">Hapus</x-danger-button></form>
            </div>
        </div>
    </x-slot>
    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100 text-sm text-gray-700 space-y-1">
                @if ($supplier->code)<div>Kode: {{ $supplier->code }}</div>@endif
                @if ($supplier->phone)<div>Telepon: {{ $supplier->phone }}</div>@endif
                @if ($supplier->email)<div>Email: {{ $supplier->email }}</div>@endif
                @if ($supplier->address)<div class="pt-2 whitespace-pre-line">{{ $supplier->address }}</div>@endif
                @if ($supplier->notes)<div class="pt-2 border-t text-gray-600">{{ $supplier->notes }}</div>@endif
            </div>
        </div>
    </div>
</x-app-layout>
