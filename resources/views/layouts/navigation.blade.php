<nav class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            <div class="flex flex-1 items-center">
                <div class="shrink-0 flex items-center mr-4">
                    <a href="{{ route('front.index') }}" class="text-2xl font-bold flex items-center gap-2" style="color:#ff7db8">
                        <img src="{{ asset('images/notxnobg.png') }}" alt="Logo" class="h-8 w-8 object-contain">
                        <span class="hidden md:block">CrochetFlow</span>
                    </a>
                </div>

                @if(!Auth::check() || (Auth::user()->role !== 'admin' && !request()->is('shop*') && !request()->is('products*')))
                    <div class="hidden sm:flex flex-1 max-w-lg px-4">
                        <form action="{{ route('front.index') }}" method="GET" class="w-full">
                            <div class="relative">
                                <input type="text" name="q" value="{{ request('q') }}" class="w-full bg-gray-50 border border-gray-300 rounded-full py-2 px-4 pl-10 text-sm focus:ring-pink-500 focus:border-pink-500 transition" placeholder="Cari...">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 20 20"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/></svg>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @auth
                    {{-- ADMIN --}}
                    @if(Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">📊 Admin Panel</x-nav-link>
                        <x-nav-link :href="route('front.index')">Lihat Website</x-nav-link>
                    
                    {{-- SELLER (Dashboard Toko) --}}
                    @elseif(Auth::user()->shop && (request()->is('shop*') || request()->is('products*')))
                        <x-nav-link :href="route('shop.index')" :active="request()->routeIs('shop.index')">Dashboard</x-nav-link>
                        <x-nav-link :href="route('shop.orders')">Pesanan</x-nav-link>
                        <x-nav-link :href="route('front.index')" class="ml-4 border-l pl-4 text-gray-500 hover:text-pink-600">&larr; Mode Pembeli</x-nav-link>
                    
                    {{-- BUYER (Default) --}}
                    @else
                        @if(Auth::user()->shop)
                            <a href="{{ route('shop.index') }}" class="mr-4 px-4 py-1 rounded-full bg-pink-50 text-pink-700 text-sm font-bold border border-pink-200">🏪 Toko Saya</a>
                        @else
                            <a href="{{ route('shop.index') }}" class="mr-4 px-4 py-1 rounded-full bg-gray-100 text-gray-700 text-sm font-bold border border-gray-300">Buka Toko</a>
                        @endif
                        <x-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">🛒 Keranjang</x-nav-link>
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Pesanan Saya</x-nav-link>
                    @endif

                    <div class="ml-3 relative">
                        <button type="button" onclick="toggleProfileDropdown()" id="user-menu-button" class="flex items-center gap-2 hover:opacity-80 transition focus:outline-none">
                            @if(Auth::user()->avatar_url)
                                <img src="{{ Auth::user()->avatar_url }}" class="w-9 h-9 rounded-full object-cover border">
                            @else
                                <div class="w-9 h-9 rounded-full bg-pink-600 text-white flex items-center justify-center font-bold">
                                    {{ substr(Auth::user()->full_name, 0, 1) }}
                                </div>
                            @endif
                        </button>
                        
                        <div id="profile-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 border border-gray-100 z-50">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->full_name }}</p>
                            </div>
                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil Saya</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Keluar</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="space-x-3 flex items-center">
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 font-bold hover:text-pink-600">Masuk</a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('register') }}" class="bg-pink-600 text-white px-4 py-2 rounded-full text-sm font-bold hover:bg-pink-700 shadow">Daftar</a>
                    </div>
                @endauth
            </div>

            <div class="-mr-2 flex items-center sm:hidden">
                <button type="button" onclick="toggleMobileMenu()" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path id="burger-icon" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path id="close-icon" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div id="mobile-menu" class="hidden sm:hidden border-t border-gray-200">
        
        @if(!Auth::check() || (Auth::user()->role !== 'admin' && !request()->is('shop*')))
        <div class="pt-4 pb-2 px-4">
            <form action="{{ route('front.index') }}" method="GET">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari..." class="w-full bg-gray-50 border border-gray-300 rounded-lg py-2 px-4 text-sm">
            </form>
        </div>
        @endif

        @auth
            <div class="pt-4 pb-1 border-t border-gray-200 bg-gray-50">
                <div class="px-4 flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-full bg-pink-600 text-white flex items-center justify-center font-bold text-lg">
                        {{ substr(Auth::user()->full_name, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->full_name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
            </div>

            <div class="pt-2 pb-3 space-y-1">
                @if(Auth::user()->role === 'admin')
                    <x-responsive-nav-link :href="route('admin.dashboard')">📊 Admin Panel</x-responsive-nav-link>
                @elseif(Auth::user()->shop && (request()->is('shop*') || request()->is('products*')))
                    <x-responsive-nav-link :href="route('shop.index')">Dashboard Toko</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('shop.orders')">Pesanan Masuk</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('front.index')">⬅ Mode Pembeli</x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('front.index')">🏠 Home</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('cart.index')">🛒 Keranjang</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('dashboard')">📦 Pesanan Saya</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('profile.show')">👤 Profil Saya</x-responsive-nav-link>
                    @if(Auth::user()->shop)
                        <x-responsive-nav-link :href="route('shop.index')">🏪 Kelola Toko</x-responsive-nav-link>
                    @else
                        <x-responsive-nav-link :href="route('shop.index')">✨ Buka Toko</x-responsive-nav-link>
                    @endif
                @endif
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600">
                        {{ __('🚪 Keluar') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        @else
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('front.index')">Home</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('login')">Masuk</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">Daftar</x-responsive-nav-link>
            </div>
        @endauth
    </div>

    <script>
        function toggleMobileMenu() {
            var menu = document.getElementById('mobile-menu');
            var burger = document.getElementById('burger-icon');
            var close = document.getElementById('close-icon');

            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                burger.classList.add('hidden');
                close.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
                burger.classList.remove('hidden');
                close.classList.add('hidden');
            }
        }

        function toggleProfileDropdown() {
            var dropdown = document.getElementById('profile-dropdown');
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
            } else {
                dropdown.classList.add('hidden');
            }
        }

        // Tutup dropdown jika klik di luar area
        window.onclick = function(event) {
            if (!event.target.closest('#user-menu-button')) {
                var dropdown = document.getElementById('profile-dropdown');
                if (dropdown && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                }
            }
        }
    </script>
</nav>