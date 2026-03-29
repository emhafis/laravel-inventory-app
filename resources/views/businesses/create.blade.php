<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Bisnis baru
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 rounded-lg bg-slate-50 p-4 text-sm text-slate-700 ring-1 ring-slate-200">
                Isi data bisnis. Anda akan otomatis menjadi <strong>pemilik</strong> dan bisnis ini dipilih sebagai bisnis aktif.
            </div>

            <form method="POST" action="{{ route('businesses.store') }}" class="space-y-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                @csrf

                <div>
                    <x-input-label for="name" value="Nama bisnis" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="slug" value="Slug URL (opsional)" />
                    <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full font-mono text-sm" placeholder="contoh: toko-saya" :value="old('slug')" />
                    <p class="mt-1 text-xs text-gray-500">Huruf kecil, angka, dan tanda hubung. Kosongkan untuk dibuat otomatis dari nama.</p>
                    <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="timezone" value="Zona waktu" />
                    <x-text-input id="timezone" name="timezone" type="text" class="mt-1 block w-full" :value="old('timezone', 'Asia/Jakarta')" required />
                    <x-input-error :messages="$errors->get('timezone')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="currency_code" value="Mata uang (ISO 4217)" />
                    <x-text-input id="currency_code" name="currency_code" type="text" class="mt-1 block w-full uppercase max-w-[8rem]" maxlength="3" :value="old('currency_code', 'IDR')" required />
                    <x-input-error :messages="$errors->get('currency_code')" class="mt-2" />
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <x-primary-button type="submit">Simpan bisnis</x-primary-button>
                    @if (auth()->user()->businesses()->exists())
                        <a href="{{ route('business.select') }}" class="text-sm text-gray-600 hover:underline">Kembali ke daftar bisnis</a>
                    @else
                        <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:underline">Beranda</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
