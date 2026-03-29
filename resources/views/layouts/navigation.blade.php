<nav x-data="{ open: false }" class="bg-slate-900 text-slate-100 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-lg font-semibold tracking-tight text-white">
                        Inventory
                    </a>
                </div>

                <div class="hidden space-x-4 sm:-my-px sm:ms-10 sm:flex sm:items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                        Produk
                    </x-nav-link>
                    <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                        Kategori
                    </x-nav-link>
                    <x-nav-link :href="route('units.index')" :active="request()->routeIs('units.*')">
                        Satuan
                    </x-nav-link>
                    <x-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                        Supplier
                    </x-nav-link>
                    <x-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">
                        Pelanggan
                    </x-nav-link>
                    <x-nav-link :href="route('stock-transactions.index')" :active="request()->routeIs('stock-transactions.*')">
                        Stok
                    </x-nav-link>
                    <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                        Laporan
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:gap-4 sm:ms-6">
                @isset($currentBusiness)
                    <form method="POST" action="{{ route('business.switch') }}" class="flex items-center gap-2 text-sm" onchange="this.submit()">
                        @csrf
                        <label for="nav-business" class="text-slate-400 whitespace-nowrap">Bisnis</label>
                        <select name="business_id" id="nav-business" class="rounded-md border-0 bg-slate-800 py-1.5 pl-2 pr-8 text-sm text-white ring-1 ring-slate-600 focus:ring-2 focus:ring-emerald-500">
                            @foreach (auth()->user()->businesses()->orderBy('name')->get() as $biz)
                                <option value="{{ $biz->id }}" @selected($biz->id === $currentBusiness->id)>
                                    {{ $biz->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endisset

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button type="button" class="inline-flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-slate-200 hover:bg-slate-800 focus:outline-none">
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="h-4 w-4 opacity-70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('businesses.create')">Bisnis baru</x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit')">Profil</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button type="button" @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-slate-300 hover:bg-slate-800 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-800 bg-slate-900">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">Produk</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">Kategori</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('units.index')" :active="request()->routeIs('units.*')">Satuan</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">Supplier</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">Pelanggan</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('stock-transactions.index')" :active="request()->routeIs('stock-transactions.*')">Stok</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">Laporan</x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-3 border-t border-slate-800 px-4">
            <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
            <div class="text-sm text-slate-400">{{ Auth::user()->email }}</div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('businesses.create')">Bisnis baru</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.edit')">Profil</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Keluar
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
