<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Satuan baru</h2></x-slot>
    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('units.store') }}" class="space-y-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                @csrf
                <div>
                    <x-input-label for="name" value="Nama" />
                    <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name')" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="code" value="Kode (mis. PCS)" />
                    <x-text-input id="code" name="code" class="mt-1 block w-full uppercase" :value="old('code')" required />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="description" value="Deskripsi" />
                    <x-text-input id="description" name="description" class="mt-1 block w-full" :value="old('description')" />
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-gray-300 text-indigo-600">
                    Aktif
                </label>
                <div class="flex gap-3">
                    <x-primary-button type="submit">Simpan</x-primary-button>
                    <a href="{{ route('units.index') }}" class="text-sm text-gray-600 hover:underline self-center">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
