<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Pelanggan baru</h2></x-slot>
    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('customers.store') }}" class="space-y-4 rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100 text-sm">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-input-label for="name" value="Nama" />
                        <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name')" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="code" value="Kode" />
                        <x-text-input id="code" name="code" class="mt-1 block w-full" :value="old('code')" />
                    </div>
                    <div>
                        <x-input-label for="phone" value="Telepon" />
                        <x-text-input id="phone" name="phone" class="mt-1 block w-full" :value="old('phone')" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="address" value="Alamat" />
                        <textarea id="address" name="address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 text-sm">{{ old('address') }}</textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="notes" value="Catatan" />
                        <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 text-sm">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <label class="inline-flex items-center gap-2 text-gray-700">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-gray-300 text-indigo-600"> Aktif
                </label>
                <div class="flex gap-3">
                    <x-primary-button type="submit">Simpan</x-primary-button>
                    <a href="{{ route('customers.index') }}" class="self-center text-gray-600 hover:underline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
