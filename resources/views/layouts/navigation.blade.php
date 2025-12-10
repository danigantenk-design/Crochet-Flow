<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            <div class="flex flex-1 items-center">
                
                <div class="shrink-0 flex items-center mr-4">
    <a href="{{ route('front.index') }}" class="text-2xl font-bold flex items-center gap-2" style="color:#ff7db8">
        <img src="{{ asset('images/notxnobg.png') }}" 
             alt="CrochetFlow Logo" 
             class="h-8 w-8 object-contain">
            CrochetFlow
    </a>
</div>


                @if(!Auth::check() || (Auth::user()->role !== 'admin' && !request()->is('shop*') && !request()->is('products*') && !request()->is('open-shop*')))
                    <div class="hidden sm:flex flex-1 max-w-lg px-4">
                        <form action="{{ route('front.index') }}" method="GET" class="w-full">
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-full focus:ring-pink-500 focus:border-pink-500 block pl-10 p-2.5 transition" 
                                    placeholder="Cari rajutan, pola, atau toko...">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <div class="flex items-center">
                
                @auth
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex items-center">

                    {{-- ========================= --}}
                    {{-- =      ADMIN PANEL      = --}}
                    {{-- ========================= --}}
                    @if(Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                            📊 Admin Panel
                        </x-nav-link>
                        <x-nav-link :href="route('front.index')" :active="request()->routeIs('front.index')">
                            Lihat Website
                        </x-nav-link>

                    {{-- ========================= --}}
                    {{-- =        PENJUAL        = --}}
                    {{-- ========================= --}}
                    {{-- @elseif(request()->is('shop*') || request()->is('products*') || request()->is('open-shop*')) --}}
                    @elseif(Auth::user()->shop && (request()->is('shop*') || request()->is('products*')))
                        
                        <x-nav-link :href="route('shop.index')" :active="request()->routeIs('shop.index')">
                            Dashboard
                        </x-nav-link>

                        <x-nav-link :href="route('shop.orders')" :active="request()->routeIs('shop.orders')">
                            Pesanan
                        </x-nav-link>

                        <x-nav-link :href="route('shop.finance')" :active="request()->routeIs('shop.finance')">
                            Keuangan
                        </x-nav-link>

                        {{-- [UPDATE UX] Link Preview Toko: Arahkan ke Halaman Publik --}}
                        <x-nav-link :href="route('shop.show', Auth::user()->shop->id ?? '#')" target="_blank" title="Lihat tampilan toko di mata pembeli">
                            👁️ Preview Toko
                        </x-nav-link>

                        {{-- <div class="flex items-center ml-4 border-l pl-4">
                            <a href="{{ route('front.index') }}" class="text-xs font-bold text-gray-500 hover:text-pink-600 uppercase tracking-wide">
                                &larr; Pembeli
                            </a>
                        </div> --}}

                    {{-- ========================= --}}
                    {{-- =        PEMBELI        = --}}
                    {{-- ========================= --}}
                    @else
                        
                        <div class="flex items-center">
                            <a href="{{ route('shop.index') }}" class="ml-2 px-4 py-1 rounded-full bg-pink-50 text-pink-700 text-sm font-bold hover:bg-pink-100 transition border border-pink-200">
                                {{ Auth::user()->shop ? '🏪 Kelola Toko' : 'Buka Toko' }}
                            </a>
                        </div>

                        @if(!request()->is('shop*') && !request()->is('products*') && !request()->is('admin*'))
                        <x-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">
                            Keranjang
                        </x-nav-link>
                        @endif

                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            Pesanan Saya
                        </x-nav-link>

                    @endif
                </div>
                @endauth

                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    @auth

                        @if(request()->is('shop*') || request()->is('products*'))
                            {{-- === PROFIL TOKO (SELLER MODE) === --}}
                            {{-- [UPDATE UX] Klik Avatar Toko -> Ke Preview Publik --}}
                            <a href="{{ route('shops.show', Auth::user()->shop->id ?? '#') }}" class="flex items-center gap-2 hover:opacity-80 transition" title="Lihat Tampilan Toko">
                                @if(Auth::user()->shop && Auth::user()->shop->logo_url)
                                    <img src="{{ Auth::user()->shop->logo_url }}" class="w-9 h-9 rounded-full object-cover border border-pink-300">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold border border-purple-300">
                                        {{ substr(Auth::user()->shop->name ?? 'S', 0, 1) }}
                                    </div>
                                @endif
                            </a>

                        @else
                            {{-- === PROFIL BUYER / ADMIN === --}}
                            {{-- [UPDATE UX] Klik Avatar User -> Ke Halaman View Profil (Read Only) --}}
                            <a href="{{ route('profile.show') }}" class="flex items-center gap-2 hover:opacity-80 transition" title="Lihat Profil Saya">
                                @if(Auth::user()->avatar_url)
                                    <img src="{{ Auth::user()->avatar_url }}" class="w-9 h-9 rounded-full object-cover">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-pink-600 text-white flex items-center justify-center font-bold">
                                        {{ substr(Auth::user()->full_name, 0, 1) }}
                                    </div>
                                @endif
                            </a>
                        @endif

                    @else
                        <div class="space-x-3 flex items-center">
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 font-bold hover:text-pink-600">Masuk</a>
                            <span class="text-gray-300">|</span>
                            <a href="{{ route('register') }}" class="bg-pink-600 text-white px-4 py-2 rounded-full text-sm font-bold hover:bg-pink-700 shadow">Daftar</a>
                        </div>
                    @endauth
                </div>

                <div class="-mr-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        
        @if(!Auth::check() || (Auth::user()->role !== 'admin' && !request()->is('shop*')))
        <div class="p-4 border-b border-gray-200">
            <form action="{{ route('front.index') }}" method="GET">
                <input type="text" name="search" class="w-full border rounded-full px-4 py-2 text-sm" placeholder="Cari produk...">
            </form>
        </div>
        @endif

        @auth
            <div class="pt-2 pb-3 space-y-1">
                @if(request()->is('shop*') || request()->is('products*'))
                    <x-responsive-nav-link :href="route('shop.index')" :active="request()->routeIs('shop.index')">Dashboard</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('shop.orders')">Pesanan</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('shop.finance')">Keuangan</x-responsive-nav-link>
                    
                    {{-- [UPDATE UX Mobile] Preview Toko --}}
                    <x-responsive-nav-link :href="route('shops.show', Auth::user()->shop->id ?? '#')">👁️ Preview Toko</x-responsive-nav-link>
                    
                    <x-responsive-nav-link :href="route('front.index')">Mode Pembeli</x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Pesanan Saya</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('cart.index')">Keranjang</x-responsive-nav-link>
                    
                    {{-- [UPDATE UX Mobile] Ke View Profil (Read Only) --}}
                    <x-responsive-nav-link :href="route('profile.show')">Profil Saya</x-responsive-nav-link>
                @endif
            </div>
        @else
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('login')">Masuk</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">Daftar</x-responsive-nav-link>
            </div>
        @endauth
    </div>
</nav>