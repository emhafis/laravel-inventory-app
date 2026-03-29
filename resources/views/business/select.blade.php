<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pilih bisnis
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-700">
                <p class="mb-4 text-sm">Anda memiliki akses ke lebih dari satu bisnis. Pilih bisnis aktif untuk melanjutkan.</p>
                <ul class="divide-y divide-gray-100 rounded-lg border border-gray-200">
                    @foreach ($businesses as $business)
                        <li class="flex items-center justify-between gap-4 p-4">
                            <div>
                                <div class="font-medium text-gray-900">{{ $business->name }}</div>
                                <div class="text-xs text-gray-500">{{ $business->currency_code }} · {{ $business->timezone }}</div>
                            </div>
                            <form method="POST" action="{{ route('business.switch') }}">
                                @csrf
                                <input type="hidden" name="business_id" value="{{ $business->id }}">
                                <x-primary-button type="submit">Pilih</x-primary-button>
                            </form>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-6 text-sm text-gray-600">
                    Perlu bisnis tambahan?
                    <a href="{{ route('businesses.create') }}" class="font-medium text-indigo-600 hover:underline">Tambah bisnis baru</a>
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
