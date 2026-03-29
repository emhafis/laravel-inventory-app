<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800">{{ $customer->name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('customers.edit', $customer) }}"><x-secondary-button type="button">Edit</x-secondary-button></a>
                <form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('Hapus?');">@csrf @method('DELETE')<x-danger-button type="submit">Hapus</x-danger-button></form>
            </div>
        </div>
    </x-slot>
    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100 text-sm text-gray-700 space-y-1">
                @if ($customer->code)<div>Kode: {{ $customer->code }}</div>@endif
                @if ($customer->phone)<div>Telepon: {{ $customer->phone }}</div>@endif
                @if ($customer->email)<div>Email: {{ $customer->email }}</div>@endif
                @if ($customer->address)<div class="pt-2 whitespace-pre-line">{{ $customer->address }}</div>@endif
                @if ($customer->notes)<div class="pt-2 border-t text-gray-600">{{ $customer->notes }}</div>@endif
            </div>
        </div>
    </div>
</x-app-layout>
